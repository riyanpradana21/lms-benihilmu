<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_view_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SIAKAD + LMS');
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@school.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_incorrect_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@school.test',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::where('email', 'admin@school.test')->first();
        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
