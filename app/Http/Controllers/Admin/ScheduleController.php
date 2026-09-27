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
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')")
            ->orderBy('start_time')
            ->paginate(15);

        $classes = SchoolClass::when($activeYear, fn ($q) => $q->where('academic_year_id', $activeYear->id))->get();
        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::with('user')->where('is_active', true)->get();

        return view('admin.schedules.index', compact('schedules', 'classes', 'subjects', 'teachers', 'classId', 'day', 'activeYear'));
    }

    public function store(Request $request): RedirectResponse
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

        // Run schedule conflict detection
        $conflictCheck = $this->conflictService->checkConflict(
            academicYearId: $activeYear->id,
            dayOfWeek: $validated['day_of_week'],
            startTime: $validated['start_time'].':00',
            endTime: $validated['end_time'].':00',
            schoolClassId: (int) $validated['school_class_id'],
            teacherId: (int) $validated['teacher_id']
        );

        if ($conflictCheck['has_conflict']) {
            return back()->withInput()->with('error', $conflictCheck['message']);
        }

        $schedule = Schedule::create([
            ...$validated,
            'academic_year_id' => $activeYear->id,
        ]);

        AuditLog::log('create_schedule', $schedule, null, $schedule->toArray());

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal pelajaran berhasil ditambahkan tanpa bentrok waktu.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        AuditLog::log('delete_schedule', $schedule, $schedule->toArray(), null);
        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
