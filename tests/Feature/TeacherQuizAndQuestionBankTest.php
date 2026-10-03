<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TeacherQuizAndQuestionBankTest extends TestCase
{
    use DatabaseTransactions;

    private User $teacherUser;

    private User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teacherUser = User::where('email', 'teacher@school.test')->first();
        $this->studentUser = User::where('email', 'student@school.test')->first();
    }

    public function test_teacher_can_view_question_banks_index(): void
    {
        $response = $this->actingAs($this->teacherUser)->get('/teacher/question-banks');

        $response->assertStatus(200);
        $response->assertSee('Bank Soal Guru');
        $response->assertSee('Buat Bank Soal Baru');
    }

    public function test_teacher_can_create_question_bank(): void
    {
        $subject = Subject::first();

        $response = $this->actingAs($this->teacherUser)->post('/teacher/question-banks', [
            'name' => 'Bank Soal Ujian Tengah Semester',
            'subject_id' => $subject?->id,
            'description' => 'Kumpulan soal persiapan UTS Matematika.',
        ]);

        $this->assertDatabaseHas('question_banks', [
            'name' => 'Bank Soal Ujian Tengah Semester',
            'created_by' => $this->teacherUser->id,
        ]);

        $bank = QuestionBank::where('name', 'Bank Soal Ujian Tengah Semester')->first();
        $response->assertRedirect(route('teacher.question-banks.show', $bank));
    }

    public function test_teacher_can_add_multiple_choice_question_with_options(): void
    {
        $bank = QuestionBank::create([
            'name' => 'Bank Soal Test Fisika',
            'created_by' => $this->teacherUser->id,
        ]);

        $response = $this->actingAs($this->teacherUser)->post("/teacher/question-banks/{$bank->id}/questions", [
            'type' => 'multiple_choice',
            'question_text' => 'Berapa percepatan gravitasi bumi rata-rata?',
            'explanation' => 'Percepatan gravitasi rata-rata di permukaan bumi adalah 9.8 m/s^2.',
            'score' => 20,
            'options' => ['9.8 m/s^2', '10.5 m/s^2', '8.9 m/s^2', '12.0 m/s^2'],
            'correct_option' => 0,
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('questions', [
            'question_bank_id' => $bank->id,
            'type' => 'multiple_choice',
            'question_text' => 'Berapa percepatan gravitasi bumi rata-rata?',
            'score' => 20,
        ]);

        $question = Question::where('question_text', 'Berapa percepatan gravitasi bumi rata-rata?')->first();
        $this->assertNotNull($question);
        $this->assertEquals(4, $question->options()->count());

        $correctOption = $question->options()->where('is_correct', true)->first();
        $this->assertNotNull($correctOption);
        $this->assertEquals('9.8 m/s^2', $correctOption->option_text);
    }

    public function test_teacher_can_add_true_false_question(): void
    {
        $bank = QuestionBank::create([
            'name' => 'Bank Soal Biologi',
            'created_by' => $this->teacherUser->id,
        ]);

        $response = $this->actingAs($this->teacherUser)->post("/teacher/question-banks/{$bank->id}/questions", [
            'type' => 'true_false',
            'question_text' => 'Mitokondria adalah organel penghasil energi sel.',
            'score' => 15,
            'true_false_answer' => 'true',
        ]);

        $response->assertSessionHas('success');

        $question = Question::where('question_text', 'Mitokondria adalah organel penghasil energi sel.')->first();
        $this->assertNotNull($question);

        $trueOption = $question->options()->where('option_text', 'Benar (True)')->first();
        $this->assertTrue($trueOption->is_correct);
    }

    public function test_teacher_can_view_quiz_questions_page(): void
    {
        $teacher = $this->teacherUser->teacher;
        $course = Course::where('teacher_id', $teacher->id)->first();
        $this->assertNotNull($course);

        $quiz = Quiz::where('course_id', $course->id)->first();
        $this->assertNotNull($quiz);

        $response = $this->actingAs($this->teacherUser)->get("/teacher/quizzes/{$quiz->id}/questions");

        $response->assertStatus(200);
        $response->assertSee('Kelola Butir Soal');
        $response->assertSee($quiz->title);
    }

    public function test_teacher_can_attach_and_detach_questions_to_quiz(): void
    {
        $teacher = $this->teacherUser->teacher;
        $course = Course::where('teacher_id', $teacher->id)->first();
        $quiz = Quiz::where('course_id', $course->id)->first();

        $bank = QuestionBank::create([
            'name' => 'Bank Kimia Dasar',
            'created_by' => $this->teacherUser->id,
            'subject_id' => $course->subject_id,
        ]);

        $newQuestion = Question::create([
            'question_bank_id' => $bank->id,
            'type' => 'short_answer',
            'question_text' => 'Rumus kimia air adalah...',
            'score' => 10,
            'correct_answer' => 'H2O',
        ]);

        // Attach question
        $attachResponse = $this->actingAs($this->teacherUser)->post("/teacher/quizzes/{$quiz->id}/questions/attach", [
            'question_ids' => [$newQuestion->id],
        ]);
        $attachResponse->assertSessionHas('success');

        $this->assertTrue($quiz->questions()->where('questions.id', $newQuestion->id)->exists());

        // Detach question
        $detachResponse = $this->actingAs($this->teacherUser)->delete("/teacher/quizzes/{$quiz->id}/questions/{$newQuestion->id}/detach");
        $detachResponse->assertSessionHas('success');

        $this->assertFalse($quiz->fresh()->questions()->where('questions.id', $newQuestion->id)->exists());
    }

    public function test_student_cannot_access_teacher_question_banks(): void
    {
        $response = $this->actingAs($this->studentUser)->get('/teacher/question-banks');
        $response->assertStatus(403);
    }
}
