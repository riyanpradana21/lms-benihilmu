<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole();
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            $isInactive = false;
            if ($user->teacher && ! $user->teacher->is_active) {
                $isInactive = true;
            } elseif ($user->student && ! $user->student->is_active) {
                $isInactive = true;
            } elseif ($user->guardian && ! $user->guardian->is_active) {
                $isInactive = true;
            }

            if ($isInactive) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw ValidationException::withMessages([
                    'email' => 'Akun Anda saat ini berstatus non-aktif. Silakan hubungi Administrator / Tata Usaha sekolah.',
                ]);
            }

            $request->session()->regenerate();

            AuditLog::log('login', $user);

            return $this->redirectBasedOnRole();
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            AuditLog::log('logout', Auth::user());
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function dashboard(): RedirectResponse
    {
        return $this->redirectBasedOnRole();
    }

    protected function redirectBasedOnRole(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasRole(['super_admin', 'admin'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('teacher')) {
            return redirect()->route('teacher.dashboard');
        }

        if ($user->hasRole('student')) {
            return redirect()->route('student.dashboard');
        }

        if ($user->hasRole('parent')) {
            return redirect()->route('parent.dashboard');
        }

        return redirect('/');
    }
}
