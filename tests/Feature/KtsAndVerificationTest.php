<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class KtsAndVerificationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_student_can_view_own_digital_kts(): void
    {
        $studentUser = User::where('email', 'student@school.test')->first();
        $response = $this->actingAs($studentUser)->get('/student/kts');

        $response->assertStatus(200);
        $response->assertSee('Kartu Tanda Siswa');
        $response->assertSee('202401001');
        $response->assertSee('Ahmad Fauzan');
    }

    public function test_public_can_verify_active_student_via_token(): void
    {
        $student = Student::where('student_number', '202401001')->first();
        $response = $this->get('/verify/student/' . $student->student_number);

        $response->assertStatus(200);
        $response->assertSee('Status: Terverifikasi Aktif');
        $response->assertSee('Ahmad Fauzan');
    }

    public function test_public_verification_fails_for_nonexistent_token(): void
    {
        $response = $this->get('/verify/student/INVALID_99999');

        $response->assertStatus(200);
        $response->assertSee('Data Siswa Tidak Ditemukan');
    }
}
