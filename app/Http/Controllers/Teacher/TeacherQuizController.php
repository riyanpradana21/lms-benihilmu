<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TeacherQuizController extends Controller
{
    public function index(): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        $courses = $teacher->courses()->with('schoolClass', 'subject')->get();
        $courseIds = $courses->pluck('id');

        $quizzes = Quiz::with(['course.subject', 'course.schoolClass'])
            ->withCount(['questions', 'attempts'])
            ->whereIn('course_id', $courseIds)
            ->latest()
            ->paginate(12);

        return view('teacher.quizzes.index', compact('quizzes', 'courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

        $validated = $request->validate([
            'course_id' => ['required', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:300'],
            'attempt_limit' => ['required', 'integer', 'min:0', 'max:10'],
            'passing_score' => ['required', 'integer', 'min:0', 'max:100'],
            'violation_threshold' => ['required', 'integer', 'min:1', 'max:20'],
            'is_published' => ['required', 'boolean'],
        ]);

        // Verify teacher owns the course
        abort_unless($teacher->courses()->whereKey($validated['course_id'])->exists(), 403);

        Quiz::create($validated);

        return back()->with('success', 'Kuis baru berhasil dibuat dan dijadwalkan.');
    }

    public function attempts(Quiz $quiz): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

        $quiz->load('course');
        abort_unless($quiz->course->teacher_id === $teacher->id, 403);

        $attempts = $quiz->attempts()
            ->with(['student.user', 'violations'])
            ->latest('submitted_at')
            ->paginate(20);

        return view('teacher.quizzes.attempts', compact('quiz', 'attempts'));
    }
}
