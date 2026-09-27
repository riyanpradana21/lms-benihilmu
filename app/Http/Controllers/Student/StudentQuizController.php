<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentQuizController extends Controller
{
    public function index(): View
    {
        $student = Auth::user()->student;
        abort_unless($student, 403, 'Akses khusus siswa.');

        $classIds = $student->schoolClasses()->pluck('school_classes.id');
        $courseIds = Course::whereIn('school_class_id', $classIds)->pluck('id');

        $quizzes = Quiz::with(['course.subject'])
            ->withCount('questions')
            ->whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->latest()
            ->paginate(12);

        // Load attempts for each quiz for this student
        $quizIds = $quizzes->pluck('id');
        $attempts = QuizAttempt::where('student_id', $student->id)
            ->whereIn('quiz_id', $quizIds)
            ->get()
            ->groupBy('quiz_id');

        return view('student.quizzes.index', compact('quizzes', 'attempts'));
    }

    public function show(Quiz $quiz): View
    {
        $student = Auth::user()->student;
        abort_unless($student, 403, 'Akses khusus siswa.');

        $isEnrolled = $student->schoolClasses()->where('school_classes.id', $quiz->course->school_class_id)->exists();
        abort_unless($isEnrolled, 403, 'Anda tidak terdaftar di kelas untuk kuis ini.');

        $quiz->load(['course.subject', 'questions']);

        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        $activeAttempt = $attempts->firstWhere('status', 'in_progress');
        if ($activeAttempt && $activeAttempt->isExpired()) {
            $this->finalizeAttempt($activeAttempt, true);
            $activeAttempt = null;
            $attempts = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('student_id', $student->id)
                ->latest()
                ->get();
        }

        $canAttempt = false;
        if (! $activeAttempt) {
            $count = $attempts->where('status', 'completed')->count();
            $limitNotReached = ($quiz->attempt_limit <= 0) || ($count < $quiz->attempt_limit);
            $windowValid = (! $quiz->starts_at || now()->gte($quiz->starts_at)) &&
                           (! $quiz->ends_at || now()->lte($quiz->ends_at));
            $canAttempt = $limitNotReached && $windowValid;
        }

        return view('student.quizzes.show', compact('quiz', 'attempts', 'activeAttempt', 'canAttempt'));
    }

    public function start(Quiz $quiz): RedirectResponse
    {
        $student = Auth::user()->student;
        abort_unless($student, 403, 'Akses khusus siswa.');

        $isEnrolled = $student->schoolClasses()->where('school_classes.id', $quiz->course->school_class_id)->exists();
        abort_unless($isEnrolled, 403, 'Anda tidak terdaftar di kelas untuk kuis ini.');

        // Check if an in_progress attempt already exists
        $activeAttempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        if ($activeAttempt) {
            if ($activeAttempt->isExpired()) {
                $this->finalizeAttempt($activeAttempt, true);
            } else {
                return redirect()->route('student.quizzes.take', [$quiz, $activeAttempt]);
            }
        }

        // Verify attempt limit
        $completedAttempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->where('status', 'completed')
            ->count();

        if ($quiz->attempt_limit > 0 && $completedAttempts >= $quiz->attempt_limit) {
            return back()->with('error', 'Anda telah mencapai batas maksimal percobaan kuis ini.');
        }

        // Verify window
        if ($quiz->starts_at && now()->lt($quiz->starts_at)) {
            return back()->with('error', 'Waktu pelaksanaan kuis belum dimulai.');
        }
        if ($quiz->ends_at && now()->gt($quiz->ends_at)) {
            return back()->with('error', 'Waktu pelaksanaan kuis telah berakhir.');
        }

        // Server-authoritative start & expiration timestamp
        $durationMinutes = $quiz->duration_minutes ?: 60;
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'started_at' => now(),
            'expires_at' => now()->addMinutes($durationMinutes),
            'status' => 'in_progress',
            'score' => 0,
        ]);

        return redirect()->route('student.quizzes.take', [$quiz, $attempt]);
    }

    public function take(Quiz $quiz, QuizAttempt $attempt): View|RedirectResponse
    {
        $student = Auth::user()->student;
        abort_unless($student, 403, 'Akses khusus siswa.');
        abort_unless($attempt->student_id === $student->id && $attempt->quiz_id === $quiz->id, 403);

        if ($attempt->status === 'completed') {
            return redirect()->route('student.quizzes.result', [$quiz, $attempt]);
        }

        // Server-authoritative expiration check
        if ($attempt->isExpired()) {
            $this->finalizeAttempt($attempt, true);

            return redirect()->route('student.quizzes.result', [$quiz, $attempt])
                ->with('info', 'Waktu pengerjaan telah berakhir. Jawaban Anda otomatis tersimpan.');
        }

        $quiz->load(['questions.options']);
        $savedAnswers = $attempt->answers->keyBy('question_id');
        $remainingSeconds = $attempt->remainingSeconds();

        return view('student.quizzes.take', compact('quiz', 'attempt', 'savedAnswers', 'remainingSeconds'));
    }

    public function saveAnswer(Request $request, QuizAttempt $attempt): JsonResponse
    {
        $student = Auth::user()->student;
        abort_unless($student && $attempt->student_id === $student->id, 403);

        if ($attempt->status !== 'in_progress' || $attempt->isExpired()) {
            if ($attempt->isExpired()) {
                $this->finalizeAttempt($attempt, true);
            }

            return response()->json(['error' => 'Waktu pengerjaan telah berakhir atau attempt sudah selesai.'], 422);
        }

        $validated = $request->validate([
            'question_id' => ['required', 'exists:questions,id'],
            'answer_text' => ['nullable', 'string', 'max:10000'],
        ]);

        QuizAnswer::updateOrCreate(
            ['attempt_id' => $attempt->id, 'question_id' => $validated['question_id']],
            ['answer_text' => $validated['answer_text']]
        );

        return response()->json(['success' => true]);
    }

    public function logViolation(Request $request, QuizAttempt $attempt): JsonResponse
    {
        $student = Auth::user()->student;
        abort_unless($student && $attempt->student_id === $student->id, 403);

        if ($attempt->status !== 'in_progress' || $attempt->isExpired()) {
            return response()->json(['error' => 'Attempt not in progress'], 422);
        }

        $validated = $request->validate([
            'event_type' => ['required', 'string', 'max:50'],
            'metadata' => ['nullable', 'array'],
        ]);

        $attempt->violations()->create([
            'event_type' => $validated['event_type'],
            'occurred_at' => now(),
            'metadata' => $validated['metadata'] ?? [],
        ]);

        $quiz = $attempt->quiz;
        $totalViolations = $attempt->violations()->count();

        $autoSubmitted = false;
        if ($quiz->auto_submit_on_violation && $quiz->violation_threshold > 0 && $totalViolations >= $quiz->violation_threshold) {
            $this->finalizeAttempt($attempt, true);
            $autoSubmitted = true;
        }

        return response()->json([
            'success' => true,
            'violations_count' => $totalViolations,
            'auto_submitted' => $autoSubmitted,
        ]);
    }

    public function submit(Request $request, QuizAttempt $attempt): RedirectResponse
    {
        $student = Auth::user()->student;
        abort_unless($student && $attempt->student_id === $student->id, 403);

        if ($attempt->status !== 'completed') {
            $this->finalizeAttempt($attempt, (bool) $request->input('auto', false));
        }

        return redirect()->route('student.quizzes.result', [$attempt->quiz, $attempt])
            ->with('success', 'Ujian berhasil diselesaikan!');
    }

    public function result(Quiz $quiz, QuizAttempt $attempt): View
    {
        $student = Auth::user()->student;
        abort_unless($student, 403);
        abort_unless($attempt->student_id === $student->id && $attempt->quiz_id === $quiz->id, 403);

        $quiz->load(['questions.options', 'course.subject']);
        $attempt->load(['answers', 'violations']);

        $isPassed = $attempt->score >= ($quiz->passing_score ?: 70);

        return view('student.quizzes.result', compact('quiz', 'attempt', 'isPassed'));
    }

    private function finalizeAttempt(QuizAttempt $attempt, bool $isAuto = false): void
    {
        DB::transaction(function () use ($attempt, $isAuto): void {
            $quiz = $attempt->quiz()->with('questions.options')->first();
            $answers = $attempt->answers->keyBy('question_id');

            $totalScoreEarned = 0;
            $maxPossibleScore = 0;

            foreach ($quiz->questions as $question) {
                $questionScore = $question->score ?: 10;
                $maxPossibleScore += $questionScore;

                $savedAnswer = $answers->get($question->id);
                $isCorrect = false;

                if ($savedAnswer && ! empty($savedAnswer->answer_text)) {
                    if (in_array($question->type, ['multiple_choice', 'true_false'])) {
                        // Check matching correct option
                        $correctOption = $question->options->firstWhere('is_correct', true);
                        if ($correctOption && (string) $correctOption->id === (string) $savedAnswer->answer_text) {
                            $isCorrect = true;
                        } elseif ($question->correct_answer && strcasecmp(trim($question->correct_answer), trim($savedAnswer->answer_text)) === 0) {
                            $isCorrect = true;
                        }
                    } elseif ($question->type === 'short_answer') {
                        if ($question->correct_answer && strcasecmp(trim($question->correct_answer), trim($savedAnswer->answer_text)) === 0) {
                            $isCorrect = true;
                        }
                    }
                }

                $earned = $isCorrect ? $questionScore : 0;
                $totalScoreEarned += $earned;

                if ($savedAnswer) {
                    $savedAnswer->update([
                        'is_correct' => $isCorrect,
                        'score_earned' => $earned,
                    ]);
                }
            }

            // Normalizing score to 0 - 100 scale
            $finalScore = $maxPossibleScore > 0 ? round(($totalScoreEarned / $maxPossibleScore) * 100, 2) : 0;

            $attempt->update([
                'status' => 'completed',
                'submitted_at' => now(),
                'score' => $finalScore,
                'is_auto_submitted' => $isAuto,
            ]);
        });
    }
}
