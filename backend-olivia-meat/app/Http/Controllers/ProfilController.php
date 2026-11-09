<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Auth;

class ProfilController extends Controller
{
    public function index()
    {
        $user = auth()->user() ?? User::first();
        return view('profil.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user() ?? User::first();
        $request->validate([
            'nama' => 'required|string|min:3',
            'username' => 'required|string'
        ]);

        $user->update([
            'nama' => $request->nama,
            'username' => $request->username
        ]);

        return redirect()->route('profil.index')->with('success', 'Profil akun berhasil diperbarui!');
    }

    // UPDATE PASSWORD ---
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'], 
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'Password saat ini yang Anda masukkan salah.',
            'password.min' => 'Password baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan login kembali dengan password baru.');
    }
}