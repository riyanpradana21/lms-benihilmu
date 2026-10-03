<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\GradeComponent;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Services\ScheduleConflictService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PlatformEnhancementsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_inactive_student_cannot_login(): void
    {
        $studentUser = User::where('email', 'student@school.test')->first();
        $student = $studentUser->student;
        $student->update(['is_active' => false]);

        $response = $this->post('/login', [
            'email' => 'student@school.test',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_teacher_cannot_login(): void
    {
        $teacherUser = User::where('email', 'teacher@school.test')->first();
        $teacher = $teacherUser->teacher;
        $teacher->update(['is_active' => false]);

        $response = $this->post('/login', [
            'email' => 'teacher@school.test',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_schedule_detects_room_conflict(): void
    {
        $conflictService = app(ScheduleConflictService::class);
        $activeYear = AcademicYear::where('is_active', true)->first();
        $class = SchoolClass::first();
        $teacher = Teacher::first();

        // Create an existing schedule in Ruang 101 on monday 07:30 - 09:00
        $result = $conflictService->checkConflict(
            academicYearId: $activeYear->id,
            dayOfWeek: 'monday',
            startTime: '08:00:00',
            endTime: '09:30:00',
            schoolClassId: 999999, // different class
            teacherId: 999999, // different teacher
            room: 'Ruang 101' // same room as seeded schedule
        );

        $this->assertTrue($result['has_conflict']);
        $this->assertStringContainsString('Bentrok ruangan', $result['message']);
    }

    public function test_grade_component_weight_cannot_exceed_100_percent(): void
    {
        $teacherUser = User::where('email', 'teacher@school.test')->first();
        $course = Course::first();

        // Current seeded course1 already has components totaling 100%
        $response = $this->actingAs($teacherUser)->post(route('teacher.grades.components.store'), [
            'course_id' => $course->id,
            'name' => 'Tugas Tambahan',
            'type' => 'assignment',
            'weight' => 20,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_schedule_order_by_day_and_time_works(): void
    {
        $schedules = Schedule::orderByDayAndTime()->get();
        $this->assertNotEmpty($schedules);

        $days = $schedules->pluck('day_of_week')->all();
        // Ensure monday appears before tuesday or wednesday
        $firstDay = $days[0] ?? null;
        $this->assertEquals('monday', $firstDay);
    }
}
