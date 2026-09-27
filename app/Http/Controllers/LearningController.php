<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\QuestionBank;
use App\Models\Quiz;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\SubmissionFeedback;
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
        $assignments = Assignment::with(['course.subject', 'submissions' => fn ($query) => $query->where('student_id', $student?->id)])
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

        $validated = $request->validate([
            'content' => ['required', 'string', 'min:2', 'max:50000'],
        ]);

        $existing = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();
        $isLate = $assignment->deadline?->isPast() ?? false;

        abort_if($existing?->status === 'graded', 422, 'Submission yang sudah dinilai tidak dapat diganti.');
        abort_if($isLate && ! $assignment->allow_late, 422, 'Batas waktu pengumpulan sudah lewat.');

        DB::transaction(function () use ($assignment, $student, $validated, $isLate): void {
            AssignmentSubmission::updateOrCreate(
                ['assignment_id' => $assignment->id, 'student_id' => $student->id],
                [
                    'content' => $validated['content'],
                    'submitted_at' => now(),
                    'is_late' => $isLate,
                    'status' => 'submitted',
                    'score' => null,
                    'graded_at' => null,
                    'graded_by' => null,
                ],
            );
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
        $grades = StudentGrade::with(['course.subject', 'gradeComponent'])
            ->where('student_id', $student?->id)
            ->latest('graded_at')
            ->paginate(12);

        return view('role.list', [
            'title' => 'Nilai Saya',
            'description' => 'Nilai yang telah dipublikasikan guru.',
            'columns' => ['course' => 'Kursus', 'component' => 'Komponen', 'score' => 'Nilai', 'graded_at' => 'Diperbarui'],
            'rows' => $grades,
            'empty' => 'Belum ada nilai yang tercatat.',
        ]);
    }

    public function studentAttendance(): View
    {
        $student = Auth::user()->student;
        $attendance = Attendance::with('session.subject')
            ->where('student_id', $student?->id)
            ->latest()
            ->paginate(12);

        return view('role.list', [
            'title' => 'Presensi Saya',
            'description' => 'Riwayat kehadiran Anda pada sesi pembelajaran.',
            'columns' => ['date' => 'Tanggal', 'subject' => 'Mata pelajaran', 'status' => 'Status', 'note' => 'Catatan'],
            'rows' => $attendance,
            'empty' => 'Belum ada catatan presensi.',
        ]);
    }

    public function studentSchedule(): View
    {
        $student = Auth::user()->student;
        $classIds = $student?->schoolClasses()->pluck('school_classes.id') ?? collect();
        $schedules = Schedule::with(['schoolClass', 'subject', 'teacher.user'])
            ->whereIn('school_class_id', $classIds)
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')")
            ->orderBy('start_time')
            ->paginate(20);

        return view('role.list', [
            'title' => 'Jadwal Pelajaran',
            'description' => 'Jadwal berdasarkan kelas akademik Anda.',
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
