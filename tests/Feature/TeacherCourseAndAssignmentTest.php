<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TeacherCourseAndAssignmentTest extends TestCase
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

    public function test_teacher_can_view_courses_index(): void
    {
        $response = $this->actingAs($this->teacherUser)->get('/teacher/courses');

        $response->assertStatus(200);
        $response->assertSee('Kursus dan Materi Guru');
        $response->assertSee('Kelola Kurikulum & Materi', false);
    }

    public function test_teacher_can_view_course_curriculum_show(): void
    {
        $teacher = $this->teacherUser->teacher;
        $course = Course::where('teacher_id', $teacher->id)->first();
        $this->assertNotNull($course);

        $response = $this->actingAs($this->teacherUser)->get("/teacher/courses/{$course->id}");

        $response->assertStatus(200);
        $response->assertSee($course->title);
        $response->assertSee('Struktur Kurikulum dan Silabus');
        $response->assertSee('Tambah Bab Baru');
    }

    public function test_teacher_can_add_chapter_topic_and_lesson(): void
    {
        $teacher = $this->teacherUser->teacher;
        $course = Course::where('teacher_id', $teacher->id)->first();

        // 1. Add chapter
        $chapterResponse = $this->actingAs($this->teacherUser)->post("/teacher/courses/{$course->id}/chapters", [
            'title' => 'Bab Baru: Aljabar Linear',
            'description' => 'Materi matriks dan transformasi linear.',
        ]);
        $chapterResponse->assertSessionHas('success');

        $chapter = Chapter::where('title', 'Bab Baru: Aljabar Linear')->first();
        $this->assertNotNull($chapter);
        $this->assertEquals($course->id, $chapter->course_id);

        // 2. Add topic
        $topicResponse = $this->actingAs($this->teacherUser)->post("/teacher/courses/{$course->id}/chapters/{$chapter->id}/topics", [
            'title' => 'Topik: Determinan Matriks 2x2',
            'description' => 'Cara menghitung determinan.',
        ]);
        $topicResponse->assertSessionHas('success');

        $topic = Topic::where('title', 'Topik: Determinan Matriks 2x2')->first();
        $this->assertNotNull($topic);
        $this->assertEquals($chapter->id, $topic->chapter_id);

        // 3. Add lesson
        $lessonResponse = $this->actingAs($this->teacherUser)->post("/teacher/courses/{$course->id}/topics/{$topic->id}/lessons", [
            'title' => 'Materi Membaca: Rumus ad - bc',
            'content' => 'Determinan matriks [a b; c d] adalah a*d - b*c.',
            'estimated_minutes' => 20,
            'is_published' => 1,
        ]);
        $lessonResponse->assertSessionHas('success');

        $lesson = Lesson::where('title', 'Materi Membaca: Rumus ad - bc')->first();
        $this->assertNotNull($lesson);
        $this->assertEquals($topic->id, $lesson->topic_id);
    }

    public function test_teacher_can_toggle_course_status(): void
    {
        $teacher = $this->teacherUser->teacher;
        $course = Course::where('teacher_id', $teacher->id)->first();
        $initialStatus = $course->status;

        $response = $this->actingAs($this->teacherUser)->patch("/teacher/courses/{$course->id}/toggle-status");
        $response->assertSessionHas('success');

        $this->assertNotEquals($initialStatus, $course->fresh()->status);
    }

    public function test_teacher_can_create_assignment(): void
    {
        $teacher = $this->teacherUser->teacher;
        $course = Course::where('teacher_id', $teacher->id)->first();

        $response = $this->actingAs($this->teacherUser)->post('/teacher/assignments', [
            'course_id' => $course->id,
            'title' => 'Tugas Praktikum Matematika Terapan',
            'instructions' => 'Selesaikan latihan soal nomor 1 sampai 10 di buku cetak.',
            'deadline' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'max_score' => 100,
            'allow_late' => 1,
            'is_published' => 1,
        ]);

        $this->assertDatabaseHas('assignments', [
            'course_id' => $course->id,
            'title' => 'Tugas Praktikum Matematika Terapan',
            'max_score' => 100,
        ]);

        $assignment = Assignment::where('title', 'Tugas Praktikum Matematika Terapan')->first();
        $response->assertRedirect(route('teacher.assignments.show', $assignment));
    }

    public function test_teacher_can_view_assignment_submissions_and_grade_student(): void
    {
        $teacher = $this->teacherUser->teacher;
        $course = Course::where('teacher_id', $teacher->id)->first();
        $student = $this->studentUser->student;

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Tugas Evaluasi Bab 1',
            'instructions' => 'Kumpulkan lembar jawaban PDF atau ringkasan.',
            'deadline' => now()->addDays(3),
            'max_score' => 100,
            'allow_late' => true,
            'is_published' => true,
        ]);

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'content' => 'Berikut jawaban saya: 1. A, 2. B, 3. C',
            'submitted_at' => now(),
            'is_late' => false,
            'status' => 'submitted',
        ]);

        // View assignment show
        $viewResponse = $this->actingAs($this->teacherUser)->get("/teacher/assignments/{$assignment->id}");
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee($assignment->title);
        $viewResponse->assertSee($this->studentUser->name);

        // Grade submission with feedback
        $gradeResponse = $this->actingAs($this->teacherUser)->patch("/teacher/submissions/{$submission->id}/grade", [
            'score' => 95,
            'feedback' => 'Kerja bagus! Pembahasan Anda sangat jelas dan rapi.',
        ]);
        $gradeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('assignment_submissions', [
            'id' => $submission->id,
            'status' => 'graded',
            'score' => 95,
        ]);

        $this->assertDatabaseHas('submission_feedback', [
            'submission_id' => $submission->id,
            'feedback' => 'Kerja bagus! Pembahasan Anda sangat jelas dan rapi.',
        ]);

        // Check student sees the score and feedback
        $studentView = $this->actingAs($this->studentUser)->get('/student/assignments');
        $studentView->assertStatus(200);
        $studentView->assertSee('95');
        $studentView->assertSee('Kerja bagus! Pembahasan Anda sangat jelas dan rapi.');
    }
}
