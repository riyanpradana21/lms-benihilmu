<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $student = User::where('email', 'student@school.test')->first();
        $response = $this->actingAs($student)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_student_cannot_access_teacher_dashboard(): void
    {
        $student = User::where('email', 'student@school.test')->first();
        $response = $this->actingAs($student)->get('/teacher/dashboard');
        $response->assertStatus(403);
    }

    public function test_teacher_cannot_access_admin_routes(): void
    {
        $teacher = User::where('email', 'teacher@school.test')->first();
        $response = $this->actingAs($teacher)->get('/admin/academic-years');
        $response->assertStatus(403);
    }

    public function test_parent_cannot_access_student_cbt(): void
    {
        $parent = User::where('email', 'parent@school.test')->first();
        $response = $this->actingAs($parent)->get('/student/quizzes');
        $response->assertStatus(403);
    }
}
