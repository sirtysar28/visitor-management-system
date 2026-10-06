<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'string'],
        ], [
            'captcha.required' => 'Captcha wajib diisi.',
        ]);

        // Verify captcha (case-insensitive, trimmed)
        $expected = session('captcha_code');
        if (! $expected || strtoupper(trim($credentials['captcha'])) !== strtoupper($expected)) {
            throw ValidationException::withMessages([
                'captcha' => 'Captcha tidak sesuai, silakan coba lagi.',
            ]);
        }
        session()->forget('captcha_code');

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            AuditLog::log('login_failed', 'auth', 'Login gagal untuk ' . $credentials['email']);
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        if (! $user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Akun Anda dinonaktifkan. Hubungi administrator.',
            ]);
        }

        $request->session()->regenerate();

        AuditLog::log('login', 'auth', $user->name . ' berhasil login');

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        if ($user = $request->user()) {
            AuditLog::log('logout', 'auth', $user->name . ' logout');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
