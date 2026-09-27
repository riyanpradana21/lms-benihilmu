<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizViolation;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class CbtService
{
    /**
     * Start a new attempt or restore an active attempt for a student.
     */
    public function getOrStartAttempt(Quiz $quiz, Student $student): QuizAttempt
    {
        // 1. Check for existing in-progress attempt
        $existing = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        if ($existing) {
            // Check if expired on server
            if ($existing->isExpired()) {
                $this->finalizeAttempt($existing, isAutoSubmitted: true);

                return $existing->fresh();
            }

            return $existing;
        }

        // 2. Check attempt limits
        $completedAttemptsCount = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->count();

        if ($completedAttemptsCount >= $quiz->attempt_limit) {
            // Return latest attempt
            return QuizAttempt::where('quiz_id', $quiz->id)
                ->where('student_id', $student->id)
                ->latest()
                ->firstOrFail();
        }

        // 3. Create server-authoritative attempt
        $now = now();
        $expiresAt = $now->copy()->addMinutes($quiz->duration_minutes);

        // If quiz ends earlier than duration, cap it
        if ($quiz->ends_at && $quiz->ends_at->isBefore($expiresAt)) {
            $expiresAt = $quiz->ends_at;
        }

        return QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $student->id,
            'started_at' => $now,
            'expires_at' => $expiresAt,
            'status' => 'in_progress',
            'is_auto_submitted' => false,
        ]);
    }

    /**
     * Save/autosave an answer idempotently.
     */
    public function saveAnswer(QuizAttempt $attempt, int $questionId, ?string $answerText): QuizAnswer
    {
        if ($attempt->status !== 'in_progress') {
            throw new \RuntimeException('Kuis telah selesai atau dikumpulkan.');
        }

        if ($attempt->isExpired()) {
            $this->finalizeAttempt($attempt, isAutoSubmitted: true);
            throw new \RuntimeException('Waktu pengerjaan kuis telah habis.');
        }

        return QuizAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $questionId,
            ],
            [
                'answer_text' => $answerText,
            ]
        );
    }

    /**
     * Log a browser anti-cheat signal.
     */
    public function recordViolation(QuizAttempt $attempt, string $eventType, ?array $metadata = null): bool
    {
        if ($attempt->status !== 'in_progress') {
            return false;
        }

        QuizViolation::create([
            'attempt_id' => $attempt->id,
            'event_type' => $eventType,
            'occurred_at' => now(),
            'metadata' => $metadata,
        ]);

        $quiz = $attempt->quiz;

        // Auto-submit if violation threshold exceeded and option is enabled
        if ($quiz->auto_submit_on_violation) {
            $violationCount = QuizViolation::where('attempt_id', $attempt->id)->count();
            if ($violationCount >= $quiz->violation_threshold) {
                $this->finalizeAttempt($attempt, isAutoSubmitted: true);

                return true; // indicates forced submission
            }
        }

        return false;
    }

    /**
     * Submit and auto-grade attempt idempotently.
     */
    public function finalizeAttempt(QuizAttempt $attempt, bool $isAutoSubmitted = false): QuizAttempt
    {
        // Prevent duplicate final submission
        if ($attempt->status !== 'in_progress') {
            return $attempt;
        }

        return DB::transaction(function () use ($attempt, $isAutoSubmitted) {
            $attempt->refresh();
            if ($attempt->status !== 'in_progress') {
                return $attempt;
            }

            $attempt->submitted_at = now();
            $attempt->is_auto_submitted = $isAutoSubmitted;
            $attempt->status = 'submitted';

            // Auto-grade objective questions
            $totalScoreEarned = 0.0;
            $maxPossibleScore = 0.0;

            $questions = $attempt->quiz->questions()->with('options')->get();

            foreach ($questions as $question) {
                $maxPossibleScore += (float) $question->score;
                $answer = QuizAnswer::where('attempt_id', $attempt->id)
                    ->where('question_id', $question->id)
                    ->first();

                $earned = 0.0;
                $isCorrect = null;

                if ($answer && $answer->answer_text !== null && $answer->answer_text !== '') {
                    $studentAns = trim(strtolower($answer->answer_text));

                    if ($question->type === 'multiple_choice') {
                        $correctOption = $question->options->firstWhere('is_correct', true);
                        if ($correctOption && strtolower(trim($correctOption->option_text)) === $studentAns) {
                            $earned = (float) $question->score;
                            $isCorrect = true;
                        } else {
                            $isCorrect = false;
                        }
                    } elseif ($question->type === 'true_false') {
                        $correct = strtolower(trim((string) $question->correct_answer));
                        if ($studentAns === $correct || ($studentAns === 'true' && $correct === 'benar') || ($studentAns === 'false' && $correct === 'salah')) {
                            $earned = (float) $question->score;
                            $isCorrect = true;
                        } else {
                            $isCorrect = false;
                        }
                    } elseif ($question->type === 'short_answer') {
                        if ($question->correct_answer && strtolower(trim($question->correct_answer)) === $studentAns) {
                            $earned = (float) $question->score;
                            $isCorrect = true;
                        } else {
                            $isCorrect = false;
                        }
                    }
                    // Essay questions require manual grading; is_correct remains null
                }

                if ($answer) {
                    $answer->update([
                        'is_correct' => $isCorrect,
                        'score_earned' => $earned,
                    ]);
                }

                $totalScoreEarned += $earned;
            }

            // Normalise to 100 or keep raw if single question
            $percentageScore = $maxPossibleScore > 0 ? ($totalScoreEarned / $maxPossibleScore) * 100 : 0;
            $attempt->score = round($percentageScore, 2);
            $attempt->status = 'graded';
            $attempt->save();

            return $attempt;
        });
    }
}
