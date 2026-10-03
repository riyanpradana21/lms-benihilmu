<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\QuestionBank;
use App\Models\Quiz;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\SubmissionFeedback;
use App\Support\DirectMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LearningController extends Controller
{
    public function studentCourses(): View|RedirectResponse
    {
        $student = Auth::user()->student;
        $classIds = $student?->schoolClasses()->pluck('school_classes.id') ?? collect();
        $courses = Course::with(['subject', 'teacher.user', 'chapters'])
            ->whereIn('school_class_id', $classIds)
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        return view('student.courses.index', compact('courses'));
    }

    public function studentAssignments(): View
    {
        $student = Auth::user()->student;
        $classIds = $student?->schoolClasses()->pluck('school_classes.id') ?? collect();
        $courseIds = Course::whereIn('school_class_id', $classIds)->pluck('id');
        $assignments = Assignment::with(['course.subject', 'submissions' => fn ($query) => $query->where('student_id', $student?->id)->with('feedback')])
            ->whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->latest('deadline')
            ->paginate(12);

        return view('student.assignments.index', compact('assignments'));
    }

    public function submitAssignment(Request $request, Assignment $assignment): RedirectResponse
    {
        $student = Auth::user()->student;
        abort_unless($student, 403);

        $assignment->load('course');
        abort_unless($student->schoolClasses()->whereKey($assignment->course->school_class_id)->exists(), 403);
        abort_unless($assignment->is_published, 404);

        $validated = $request->validate([
            'content' => ['nullable', 'string', 'min:2', 'max:50000', 'required_without:media'],
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,webm', 'max:20480'],
        ]);

        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();
        $isLate = $assignment->deadline?->isPast() ?? false;

        abort_if($existing?->status === 'graded', 422, 'Submission yang sudah dinilai tidak dapat diganti.');
        abort_if($isLate && ! $assignment->allow_late, 422, 'Batas waktu pengumpulan sudah lewat.');

        DB::transaction(function () use ($assignment, $student, $validated, $isLate, $request): void {
            $submission = AssignmentSubmission::updateOrCreate(
                ['assignment_id' => $assignment->id, 'student_id' => $student->id],
                [
                    'content' => $validated['content'] ?? '',
                    'submitted_at' => now(),
                    'is_late' => $isLate,
                    'status' => 'submitted',
                    'score' => null,
                    'graded_at' => null,
                    'graded_by' => null,
                ],
            );
            if ($request->hasFile('media')) {
                $submission->update(['content' => $submission->content.DirectMedia::attach($submission, $request->file('media'), 'submission-media')]);
            }
        });

        return back()->with('success', 'Tugas berhasil dikumpulkan.');
    }

    public function gradeAssignment(Request $request, AssignmentSubmission $submission): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

        $submission->load('assignment.course');
        abort_unless($submission->assignment->course->teacher_id === $teacher->id, 403);

        $validated = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:'.$submission->assignment->max_score],
            'feedback' => ['nullable', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($submission, $validated): void {
            $submission->update([
                'score' => $validated['score'],
                'status' => 'graded',
                'graded_at' => now(),
                'graded_by' => Auth::id(),
            ]);

            SubmissionFeedback::updateOrCreate(
                ['submission_id' => $submission->id],
                ['feedback' => $validated['feedback'] ?? null],
            );
        });

        return back()->with('success', 'Submission berhasil dinilai.');
    }

    public function teacherCourses(): View
    {
        $courses = Auth::user()->teacher?->courses()
            ->with(['subject', 'schoolClass'])
            ->withCount(['chapters', 'assignments', 'quizzes'])
            ->latest()
            ->paginate(12) ?? Course::whereKey([])->paginate(12);

        return view('teacher.courses.index', compact('courses'));
    }

    public function parentChildren(): View
    {
        $children = Auth::user()->guardian?->students()->with(['schoolClasses', 'user'])->get() ?? collect();

        return view('parent.children', compact('children'));
    }

    public function studentQuizzes(): View
    {
        $student = Auth::user()->student;
        $courseIds = $this->studentCourseIds($student?->id);
        $quizzes = Quiz::with('course.subject')
            ->whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->latest('starts_at')
            ->paginate(12);

        return view('role.list', [
            'title' => 'Kuis & CBT',
            'description' => 'Evaluasi yang tersedia dari kursus kelas Anda.',
            'columns' => ['title' => 'Kuis', 'course' => 'Kursus', 'duration' => 'Durasi', 'window' => 'Jadwal'],
            'rows' => $quizzes,
            'empty' => 'Belum ada kuis yang dipublikasikan.',
        ]);
    }

    public function studentGrades(): View
    {
        $student = Auth::user()->student;
        abort_unless($student, 403, 'Profil siswa tidak ditemukan.');

        $activeYear = AcademicYear::where('is_active', true)->first();
        $classIds = $student->schoolClasses()
            ->when($activeYear, fn ($query) => $query->where('class_students.academic_year_id', $activeYear->id))
            ->pluck('school_classes.id');

        $courses = Course::with(['subject', 'gradeComponents.studentGrades' => fn ($query) => $query->where('student_id', $student->id)])
            ->whereIn('school_class_id', $classIds)
            ->where('status', 'published')
            ->orderBy('title')
            ->paginate(12);

        return view('student.grades.index', compact('courses'));
    }

    public function studentAttendance(): View
    {
        $student = Auth::user()->student;
        abort_unless($student, 403, 'Profil siswa tidak ditemukan.');
        $classIds = $student->schoolClasses()->pluck('school_classes.id');
        $sessions = AttendanceSession::with(['subject', 'attendances' => fn ($query) => $query->where('student_id', $student->id)])
            ->whereIn('school_class_id', $classIds)
            ->orderByDesc('date')
            ->paginate(20);

        return view('student.attendance.index', compact('sessions'));
    }

    public function studentSchedule(): View
    {
        $student = Auth::user()->student;
        $activeYear = AcademicYear::where('is_active', true)->first();
        $classIds = $student?->schoolClasses()
            ->when($activeYear, fn ($query) => $query->where('class_students.academic_year_id', $activeYear->id))
            ->pluck('school_classes.id') ?? collect();
        $schedules = Schedule::with(['schoolClass', 'subject', 'teacher.user'])
            ->whereIn('school_class_id', $classIds)
            ->when($activeYear, fn ($query) => $query->where('academic_year_id', $activeYear->id))
            ->orderByDayAndTime()
            ->paginate(20);

        return view('role.list', [
            'title' => 'Jadwal Pelajaran',
            'description' => 'Jadwal kelas Anda'.($activeYear ? ' · '.$activeYear->name : '').'.',
            'columns' => ['day' => 'Hari', 'time' => 'Jam', 'subject' => 'Mata pelajaran', 'teacher' => 'Guru', 'room' => 'Ruang'],
            'rows' => $schedules,
            'empty' => 'Belum ada jadwal untuk kelas Anda.',
        ]);
    }

    public function teacherAssignments(): View
    {
        $courseIds = Auth::user()->teacher?->courses()->pluck('id') ?? collect();
        $assignments = Assignment::with('course.subject')
            ->whereIn('course_id', $courseIds)
            ->latest('deadline')
            ->paginate(12);

        return view('role.list', [
            'title' => 'Tugas & Penilaian',
            'description' => 'Tugas dari kursus yang Anda kelola.',
            'columns' => ['title' => 'Tugas', 'course' => 'Kursus', 'deadline' => 'Batas waktu', 'status' => 'Status'],
            'rows' => $assignments,
            'empty' => 'Belum ada tugas pada kursus Anda.',
        ]);
    }

    public function teacherQuestionBanks(): View
    {
        $questionBanks = QuestionBank::with(['subject', 'questions'])
            ->where('created_by', Auth::id())
            ->latest()
            ->paginate(12);

        return view('role.list', [
            'title' => 'Bank Soal',
            'description' => 'Bank soal yang dibuat oleh akun Anda.',
            'columns' => ['name' => 'Bank soal', 'subject' => 'Mata pelajaran', 'questions' => 'Jumlah soal'],
            'rows' => $questionBanks,
            'empty' => 'Belum ada bank soal.',
        ]);
    }

    private function studentCourseIds(?int $studentId): Collection
    {
        $student = $studentId ? Student::find($studentId) : null;
        $classIds = $student?->schoolClasses()->pluck('school_classes.id') ?? collect();

        return Course::whereIn('school_class_id', $classIds)->pluck('id');
    }
}
