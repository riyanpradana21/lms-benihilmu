<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Course;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $quiz = Quiz::create($validated);

        return redirect()->route('teacher.quizzes.questions', $quiz)
            ->with('success', 'Kuis baru berhasil dibuat. Silakan tambahkan butir soal ke dalam kuis.');
    }

    public function questions(Quiz $quiz): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

        $quiz->load(['course.subject', 'course.schoolClass', 'questions.options']);
        abort_unless($quiz->course->teacher_id === $teacher->id, 403);

        $existingQuestionIds = $quiz->questions->pluck('id')->all();

        // Get question banks available to this teacher (by subject or creator)
        $subjectId = $quiz->course->subject_id;
        $questionBanks = QuestionBank::with(['subject', 'questions' => function ($q) use ($existingQuestionIds): void {
            if (! empty($existingQuestionIds)) {
                $q->whereNotIn('questions.id', $existingQuestionIds);
            }
            $q->with('options')->latest();
        }])
            ->where(function ($q) use ($subjectId): void {
                $q->where('created_by', Auth::id());
                if ($subjectId) {
                    $q->orWhere('subject_id', $subjectId);
                }
            })
            ->get();

        $totalScore = $quiz->questions->sum('score');

        return view('teacher.quizzes.questions', compact('quiz', 'questionBanks', 'totalScore'));
    }

    public function attachQuestions(Request $request, Quiz $quiz): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

        $quiz->load('course');
        abort_unless($quiz->course->teacher_id === $teacher->id, 403);

        $validated = $request->validate([
            'question_ids' => ['required', 'array', 'min:1'],
            'question_ids.*' => ['exists:questions,id'],
        ]);

        DB::transaction(function () use ($quiz, $validated): void {
            $maxOrder = (int) DB::table('quiz_questions')
                ->where('quiz_id', $quiz->id)
                ->max('order');

            $syncData = [];
            foreach ($validated['question_ids'] as $qId) {
                // Check if not already attached
                if (! $quiz->questions()->where('questions.id', $qId)->exists()) {
                    $maxOrder++;
                    $syncData[$qId] = ['order' => $maxOrder];
                }
            }

            if (! empty($syncData)) {
                $quiz->questions()->attach($syncData);
            }
        });

        return back()->with('success', 'Butir soal berhasil ditambahkan ke dalam kuis CBT.');
    }

    public function detachQuestion(Quiz $quiz, Question $question): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

        $quiz->load('course');
        abort_unless($quiz->course->teacher_id === $teacher->id, 403);

        $quiz->questions()->detach($question->id);

        return back()->with('success', 'Butir soal telah dilepas dari kuis.');
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

    public function openAccess(Quiz $quiz, QuizAttempt $attempt): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403);

        $quiz->load('course');
        abort_unless($quiz->course->teacher_id === $teacher->id, 403);
        abort_unless($attempt->quiz_id === $quiz->id && $attempt->status === 'in_progress', 404);
        abort_unless($attempt->is_locked, 422, 'Percobaan ini tidak sedang dikunci.');
        abort_if($attempt->isExpired(), 422, 'Waktu ujian siswa sudah berakhir.');

        $attempt->update(['is_locked' => false]);
        AuditLog::log('open_quiz_attempt_access', $attempt, ['is_locked' => true], ['is_locked' => false]);

        return back()->with('success', 'Akses ujian siswa berhasil dibuka kembali.');
    }
}
