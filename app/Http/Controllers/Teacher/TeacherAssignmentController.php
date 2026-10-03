<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\SubmissionFeedback;
use App\Support\DirectMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherAssignmentController extends Controller
{
    public function index(): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        $courses = $teacher->courses()->with(['subject', 'schoolClass'])->get();
        $courseIds = $courses->pluck('id');

        $assignments = Assignment::with(['course.subject', 'course.schoolClass', 'submissions'])
            ->withCount(['submissions'])
            ->whereIn('course_id', $courseIds)
            ->latest('deadline')
            ->paginate(12);

        $totalAssignments = Assignment::whereIn('course_id', $courseIds)->count();
        $totalSubmissions = AssignmentSubmission::whereIn('assignment_id', function ($sub) use ($courseIds): void {
            $sub->select('id')->from('assignments')->whereIn('course_id', $courseIds);
        })->count();
        $pendingGrading = AssignmentSubmission::whereIn('assignment_id', function ($sub) use ($courseIds): void {
            $sub->select('id')->from('assignments')->whereIn('course_id', $courseIds);
        })->where('status', 'submitted')->count();

        return view('teacher.assignments.index', compact(
            'assignments',
            'courses',
            'totalAssignments',
            'totalSubmissions',
            'pendingGrading'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'deadline' => ['required', 'date'],
            'max_score' => ['required', 'integer', 'min:10', 'max:1000'],
            'allow_late' => ['required', 'boolean'],
            'is_published' => ['required', 'boolean'],
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,webm', 'max:20480'],
        ]);

        abort_unless($teacher->courses()->whereKey($validated['course_id'])->exists(), 403);

        unset($validated['media']);
        $assignment = Assignment::create($validated);
        if ($request->hasFile('media')) {
            $assignment->update(['instructions' => ($assignment->instructions ?? '').DirectMedia::attach($assignment, $request->file('media'), 'assignment-media')]);
        }

        return redirect()->route('teacher.assignments.show', $assignment)
            ->with('success', 'Tugas baru "'.$assignment->title.'" berhasil dibuat dan dijadwalkan.');
    }

    public function show(Assignment $assignment): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        $assignment->load(['course.subject', 'course.schoolClass.students.user', 'submissions.feedback', 'submissions.gradedBy']);
        abort_unless($assignment->course->teacher_id === $teacher->id, 403, 'Anda bukan pengampu kursus ini.');

        $students = $assignment->course->schoolClass?->students ?? collect();
        $submissionsByStudent = $assignment->submissions->keyBy('student_id');

        $studentsWithSubmissions = $students->map(function ($student) use ($submissionsByStudent) {
            $submission = $submissionsByStudent->get($student->id);

            return (object) [
                'student' => $student,
                'submission' => $submission,
                'status' => $submission ? $submission->status : 'missing',
            ];
        });

        $totalEnrolled = $students->count();
        $submittedCount = $assignment->submissions->count();
        $gradedCount = $assignment->submissions->where('status', 'graded')->count();
        $pendingCount = $submittedCount - $gradedCount;

        return view('teacher.assignments.show', compact(
            'assignment',
            'studentsWithSubmissions',
            'totalEnrolled',
            'submittedCount',
            'gradedCount',
            'pendingCount'
        ));
    }

    public function grade(Request $request, AssignmentSubmission $submission): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

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

        return back()->with('success', 'Penilaian tugas untuk '.$submission->student?->user?->name.' berhasil disimpan.');
    }

    public function destroy(Assignment $assignment): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        $assignment->load('course');
        abort_unless($assignment->course->teacher_id === $teacher->id, 403);

        $title = $assignment->title;
        $assignment->delete();

        return redirect()->route('teacher.assignments.index')
            ->with('success', 'Tugas "'.$title.'" berhasil dihapus.');
    }
}
