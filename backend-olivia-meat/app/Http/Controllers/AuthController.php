<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali, ' . Auth::user()->nama . '!');
        }

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    }

    /**
     * Proses logout pengguna
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil keluar dari sistem.');
    }

    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Proses pengiriman email link reset password
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email'], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Email tidak terdaftar di sistem kami.'
        ]);

        // 2. Membuat token & mengirim email log
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // 3. Logika notifikasi yang lebih pintar (Mendeteksi Spam Block)
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Link reset password telah dikirim. Silakan cek kotak masuk email Anda.');
        } elseif ($status === Password::RESET_THROTTLED) {
            return back()->withErrors(['email' => 'Harap tunggu 1 menit sebelum meminta link baru lagi (Pencegahan Spam).']);
        } else {
            return back()->withErrors(['email' => 'Gagal mengirim link reset password. Terjadi kesalahan pada sistem.']);
        }
    }
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Eksekusi update password ke database
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = \Illuminate\Support\Facades\Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('success', 'Password Anda berhasil di-reset! Silakan login.')
                    : back()->withErrors(['email' => 'Gagal mereset password. Token tidak valid atau sudah kadaluarsa.']);
    }
}
