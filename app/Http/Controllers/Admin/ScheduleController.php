<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Services\ScheduleConflictService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(
        protected ScheduleConflictService $conflictService
    ) {}

    public function index(Request $request): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $classId = $request->get('class_id');
        $day = $request->get('day');

        $schedules = Schedule::with(['schoolClass', 'subject', 'teacher.user'])
            ->when($activeYear, fn ($q) => $q->where('academic_year_id', $activeYear->id))
            ->when($classId, fn ($q) => $q->where('school_class_id', $classId))
            ->when($day, fn ($q) => $q->where('day_of_week', $day))
            ->orderByDayAndTime()
            ->paginate(15);

        $classes = SchoolClass::when($activeYear, fn ($q) => $q->where('academic_year_id', $activeYear->id))->get();
        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::with('user')->where('is_active', true)->get();

        return view('admin.schedules.index', compact('schedules', 'classes', 'subjects', 'teachers', 'classId', 'day', 'activeYear'));
    }

    public function print(Request $request): View
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        $classId = $request->integer('class_id') ?: null;
        $class = $classId
            ? SchoolClass::query()->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))->findOrFail($classId)
            : null;
        $schedules = Schedule::with(['schoolClass', 'subject', 'teacher.user'])
            ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
            ->when($classId, fn ($query) => $query->where('school_class_id', $classId))
            ->when($request->filled('day'), fn ($query) => $query->where('day_of_week', $request->string('day')->toString()))
            ->orderByDayAndTime()
            ->get();

        return view('schedules.print', ['schedules' => $schedules, 'class' => $class, 'activeYear' => $activeYear, 'title' => 'Jadwal Pelajaran']);
    }

    public function studentPrint(): View
    {
        $student = Auth::user()->student;
        abort_unless($student, 403);
        $activeYear = AcademicYear::where('is_active', true)->first();
        $classIds = $student->schoolClasses()
            ->when($activeYear, fn ($query) => $query->where('class_students.academic_year_id', $activeYear->id))
            ->pluck('school_classes.id');
        $class = $student->schoolClasses()->whereIn('school_classes.id', $classIds)->first();
        $schedules = Schedule::with(['schoolClass', 'subject', 'teacher.user'])
            ->whereIn('school_class_id', $classIds)
            ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
            ->orderByDayAndTime()
            ->get();

        return view('schedules.print', ['schedules' => $schedules, 'class' => $class, 'activeYear' => $activeYear, 'title' => 'Jadwal Pelajaran']);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->save($request);
    }

    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        return $this->save($request, $schedule);
    }

    private function save(Request $request, ?Schedule $schedule = null): RedirectResponse
    {
        $validated = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:teachers,id'],
            'day_of_week' => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string'],
        ]);

        $activeYear = AcademicYear::where('is_active', true)->first();
        if (! $activeYear) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        abort_unless(SchoolClass::whereKey($validated['school_class_id'])->where('academic_year_id', $activeYear->id)->exists(), 422, 'Kelas harus berada pada tahun ajaran aktif.');
        abort_unless(Teacher::whereKey($validated['teacher_id'])->where('is_active', true)->exists(), 422, 'Guru yang dipilih tidak aktif.');

        // Run schedule conflict detection
        $conflictCheck = $this->conflictService->checkConflict(
            academicYearId: $activeYear->id,
            dayOfWeek: $validated['day_of_week'],
            startTime: $validated['start_time'].':00',
            endTime: $validated['end_time'].':00',
            schoolClassId: (int) $validated['school_class_id'],
            teacherId: (int) $validated['teacher_id'],
            ignoreScheduleId: $schedule?->id,
            room: $validated['room'] ?? null,
        );

        if ($conflictCheck['has_conflict']) {
            return back()->withInput()->with('error', $conflictCheck['message']);
        }

        $attributes = [
            ...$validated,
            'academic_year_id' => $activeYear->id,
        ];

        if ($schedule) {
            $oldValues = $schedule->toArray();
            $schedule->update($attributes);
            AuditLog::log('update_schedule', $schedule, $oldValues, $schedule->fresh()->toArray());
        } else {
            $schedule = Schedule::create($attributes);
            AuditLog::log('create_schedule', $schedule, null, $schedule->toArray());
        }

        return redirect()->route('admin.schedules.index')->with('success', $schedule->wasRecentlyCreated ? 'Jadwal pelajaran berhasil ditambahkan tanpa bentrok waktu.' : 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        AuditLog::log('delete_schedule', $schedule, $schedule->toArray(), null);
        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
