<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Support\DirectMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TeacherQuestionBankController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        $teacherSubjectIds = $teacher->subjects()->pluck('subjects.id')->all();

        $query = QuestionBank::with(['subject', 'creator'])
            ->withCount('questions')
            ->where(function ($q) use ($teacherSubjectIds): void {
                $q->where('created_by', Auth::id());
                if (! empty($teacherSubjectIds)) {
                    $q->orWhereIn('subject_id', $teacherSubjectIds);
                }
            });

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->query('subject_id'));
        }

        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        $questionBanks = $query->latest()->paginate(12)->withQueryString();

        $subjects = Subject::orderBy('name')->get();

        // Calculate quick summary
        $totalBanks = QuestionBank::where('created_by', Auth::id())->count();
        $totalQuestions = Question::whereIn('question_bank_id', function ($sub): void {
            $sub->select('id')->from('question_banks')->where('created_by', Auth::id());
        })->count();

        return view('teacher.question-banks.index', compact(
            'questionBanks',
            'subjects',
            'totalBanks',
            'totalQuestions'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $bank = QuestionBank::create([
            'name' => $validated['name'],
            'subject_id' => $validated['subject_id'] ?? null,
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('teacher.question-banks.show', $bank)
            ->with('success', 'Bank soal "'.$bank->name.'" berhasil dibuat. Sekarang Anda dapat menambahkan butir soal.');
    }

    public function show(QuestionBank $questionBank): View
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        $this->authorizeBankAccess($questionBank);

        $questionBank->load(['subject', 'creator', 'questions.options']);

        $totalScore = $questionBank->questions->sum('score');
        $mcCount = $questionBank->questions->where('type', 'multiple_choice')->count();
        $tfCount = $questionBank->questions->where('type', 'true_false')->count();
        $shortCount = $questionBank->questions->where('type', 'short_answer')->count();
        $essayCount = $questionBank->questions->where('type', 'essay')->count();

        return view('teacher.question-banks.show', compact(
            'questionBank',
            'totalScore',
            'mcCount',
            'tfCount',
            'shortCount',
            'essayCount'
        ));
    }

    public function destroy(QuestionBank $questionBank): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        abort_unless($questionBank->created_by === Auth::id(), 403, 'Anda hanya dapat menghapus bank soal yang Anda buat.');

        $bankName = $questionBank->name;
        $questionBank->delete();

        return redirect()->route('teacher.question-banks.index')
            ->with('success', 'Bank soal "'.$bankName.'" berhasil dihapus.');
    }

    public function storeQuestion(Request $request, QuestionBank $questionBank): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        $this->authorizeBankAccess($questionBank);

        $validated = $request->validate([
            'type' => ['required', 'in:multiple_choice,true_false,short_answer,essay'],
            'question_text' => ['required', 'string', 'min:3'],
            'explanation' => ['nullable', 'string', 'max:3000'],
            'score' => ['required', 'integer', 'min:1', 'max:100'],
            'options' => ['nullable', 'array', 'min:2', 'max:6'],
            'options.*' => ['nullable', 'string'],
            'correct_option' => ['nullable', 'integer', 'min:0'],
            'true_false_answer' => ['nullable', 'in:true,false'],
            'correct_answer' => ['nullable', 'string', 'max:1000'],
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,webm', 'max:20480'],
        ]);

        $uploadedMedia = $request->file('media');
        DB::transaction(function () use ($questionBank, $validated, $uploadedMedia): void {
            $correctAnswer = null;

            if ($validated['type'] === 'multiple_choice') {
                $correctIdx = (int) ($validated['correct_option'] ?? 0);
                $correctAnswer = $validated['options'][$correctIdx] ?? null;
            } elseif ($validated['type'] === 'true_false') {
                $correctAnswer = $validated['true_false_answer'] ?? 'true';
            } elseif ($validated['type'] === 'short_answer') {
                $correctAnswer = $validated['correct_answer'] ?? null;
            }

            $question = Question::create([
                'question_bank_id' => $questionBank->id,
                'type' => $validated['type'],
                'question_text' => $validated['question_text'],
                'explanation' => $validated['explanation'] ?? null,
                'score' => $validated['score'],
                'correct_answer' => $correctAnswer,
            ]);

            if ($uploadedMedia) {
                $question->update(['question_text' => $question->question_text.DirectMedia::attach($question, $uploadedMedia, 'question-media')]);
            }

            if ($validated['type'] === 'multiple_choice' && ! empty($validated['options'])) {
                $correctIdx = (int) ($validated['correct_option'] ?? 0);
                foreach ($validated['options'] as $idx => $optText) {
                    if (trim((string) $optText) === '') {
                        continue;
                    }
                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => trim((string) $optText),
                        'is_correct' => ($idx === $correctIdx),
                        'order' => $idx + 1,
                    ]);
                }
            } elseif ($validated['type'] === 'true_false') {
                $isTrueCorrect = ($validated['true_false_answer'] ?? 'true') === 'true';
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => 'Benar (True)',
                    'is_correct' => $isTrueCorrect,
                    'order' => 1,
                ]);
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => 'Salah (False)',
                    'is_correct' => ! $isTrueCorrect,
                    'order' => 2,
                ]);
            }
        });

        return back()->with('success', 'Butir soal baru berhasil ditambahkan.');
    }

    public function destroyQuestion(QuestionBank $questionBank, Question $question): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        $this->authorizeBankAccess($questionBank);
        abort_unless($question->question_bank_id === $questionBank->id, 404);

        $question->delete();

        return back()->with('success', 'Butir soal berhasil dihapus dari bank soal.');
    }

    public function updateQuestion(Request $request, QuestionBank $questionBank, Question $question): RedirectResponse
    {
        $teacher = Auth::user()->teacher;
        abort_unless($teacher, 403, 'Akses khusus guru.');

        $this->authorizeBankAccess($questionBank);
        abort_unless($question->question_bank_id === $questionBank->id, 404);

        $validated = $request->validate([
            'question_text' => ['required', 'string', 'min:3', 'max:50000'],
            'explanation' => ['nullable', 'string', 'max:3000'],
            'score' => ['required', 'integer', 'min:1', 'max:100'],
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4,webm', 'max:20480'],
        ]);

        $hasResponses = DB::table('quiz_answers')
            ->join('quiz_attempts', 'quiz_attempts.id', '=', 'quiz_answers.attempt_id')
            ->join('quiz_questions', 'quiz_questions.quiz_id', '=', 'quiz_attempts.quiz_id')
            ->where('quiz_questions.question_id', $question->id)
            ->exists();
        abort_if($hasResponses, 409, 'Soal sudah digunakan dalam ujian yang memiliki jawaban siswa dan tidak dapat diubah.');

        $uploadedMedia = $request->file('media');
        $oldValues = $question->toArray();
        $question->update($validated);
        if ($uploadedMedia) {
            $question->update(['question_text' => $question->question_text.DirectMedia::attach($question, $uploadedMedia, 'question-media')]);
        }
        AuditLog::log('update_question', $question, $oldValues, $question->fresh()->toArray());

        return back()->with('success', 'Butir soal berhasil diperbarui.');
    }

    private function authorizeBankAccess(QuestionBank $bank): void
    {
        $teacher = Auth::user()->teacher;
        if ($bank->created_by === Auth::id()) {
            return;
        }

        if ($bank->subject_id && $teacher->subjects()->where('subjects.id', $bank->subject_id)->exists()) {
            return;
        }

        abort(403, 'Anda tidak memiliki hak akses ke bank soal ini.');
    }
}
