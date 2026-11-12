<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Login, register, logout. Penanggung jawab: Marcel.
 */
class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [], [
            'email'    => 'email',
            'password' => 'password',
        ]);

        // Batasi 5 percobaan per menit per email + IP
        $throttleKey = Str::lower($credentials['email']) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan masuk. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        // is_active => true: akun yang dinonaktifkan admin tidak bisa masuk
        if (! Auth::attempt($credentials + ['is_active' => true], $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey);
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah, atau akun sedang dinonaktifkan.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        $user = $request->user();

        if ($user->canAccessDashboard()) {
            ActivityLog::record('auth.login', 'Masuk ke dashboard', $user);

            return redirect()->intended(route('dashboard.index'));
        }

        return redirect()->intended(route('home'))->with('status', "Selamat datang, {$user->name}.");
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['required', 'string', 'regex:/^(\+62|62|0)8[0-9]{7,12}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Silakan masuk.',
            'phone.regex'  => 'Nomor HP harus diawali 08, 62, atau +62, contoh 081234567890.',
        ], [
            'name'     => 'nama',
            'phone'    => 'nomor HP',
            'password' => 'password',
        ]);

        // Role SELALU 'user'. Jangan pernah ambil role dari input form.
        $user = User::create($data + ['role' => User::ROLE_USER, 'is_active' => true]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'))->with('status', 'Akun berhasil dibuat. Selamat datang!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Kamu sudah keluar.');
    }
}
