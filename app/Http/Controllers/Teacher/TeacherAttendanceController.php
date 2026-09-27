<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSession;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherAttendanceController extends Controller
{
    public function index(): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        $sessions = AttendanceSession::with(['schoolClass', 'subject', 'attendances'])
            ->where('teacher_id', $teacher->id)
            ->latest('date')
            ->paginate(15);

        // Get classes and subjects taught by teacher
        $classes = SchoolClass::whereHas('schedules', fn ($q) => $q->where('teacher_id', $teacher->id))
            ->orWhereHas('courses', fn ($q) => $q->where('teacher_id', $teacher->id))
            ->distinct()
            ->get();

        $subjects = $teacher->subjects()->get();

        return view('teacher.attendance.index', compact('sessions', 'classes', 'subjects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

        $validated = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
        ]);

        $session = DB::transaction(function () use ($teacher, $validated) {
            $session = AttendanceSession::create([
                'teacher_id' => $teacher->id,
                'school_class_id' => $validated['school_class_id'],
                'subject_id' => $validated['subject_id'],
                'date' => $validated['date'],
                'start_time' => $validated['start_time'] ?? now()->format('H:i'),
                'status' => 'open',
            ]);

            // Seed initial present status for all students enrolled in this class
            $class = SchoolClass::with('students')->findOrFail($validated['school_class_id']);
            foreach ($class->students as $student) {
                Attendance::firstOrCreate(
                    ['session_id' => $session->id, 'student_id' => $student->id],
                    ['status' => 'present', 'note' => null]
                );
            }

            return $session;
        });

        return redirect()->route('teacher.attendance.edit', $session)
            ->with('success', 'Sesi presensi baru berhasil dibuka.');
    }

    public function edit(AttendanceSession $session): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher && $session->teacher_id === $teacher->id, 403);

        $session->load(['schoolClass.students', 'subject', 'attendances.student']);

        // Ensure every student in class has an attendance row
        foreach ($session->schoolClass->students as $student) {
            Attendance::firstOrCreate(
                ['session_id' => $session->id, 'student_id' => $student->id],
                ['status' => 'present']
            );
        }

        $session->load('attendances.student');

        return view('teacher.attendance.edit', compact('session'));
    }

    public function update(Request $request, AttendanceSession $session): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher && $session->teacher_id === $teacher->id, 403);

        $validated = $request->validate([
            'attendances' => ['required', 'array'],
            'attendances.*.status' => ['required', 'in:present,sick,permission,absent'],
            'attendances.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($session, $validated): void {
            foreach ($validated['attendances'] as $studentId => $data) {
                Attendance::updateOrCreate(
                    ['session_id' => $session->id, 'student_id' => $studentId],
                    [
                        'status' => $data['status'],
                        'note' => $data['note'] ?? null,
                    ]
                );
            }
        });

        return back()->with('success', 'Presensi siswa berhasil disimpan.');
    }
}
