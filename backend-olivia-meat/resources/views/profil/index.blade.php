@extends('layouts.app')

@section('content')
<div class="space-y-4">
    <h2 class="font-bold text-gray-700 text-base mb-2">Pengaturan Profil Akun</h2>

    <div class="bg-white p-4 rounded-xl border shadow-sm space-y-3">
        <h3 class="text-xs font-bold text-red-600 uppercase tracking-wider">Informasi Akun</h3>
        <div class="text-xs space-y-1 text-gray-600">
            <p><strong>Nama Lengkap:</strong> {{ $user->nama }}</p>
            <p><strong>Username:</strong> {{ $user->username }}</p>
            <p><strong>Role Hak Akses:</strong> Administrator / Kasir</p>
        </div>
    </div>

    <div class="bg-white p-4 rounded-xl border shadow-sm space-y-3">
        <h3 class="text-xs font-bold text-gray-600 uppercase tracking-wider">Update Data Profil</h3>
        <form action="{{ route('profil.update') }}" method="POST" class="space-y-2.5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Nama</label>
                <input type="text" name="nama" value="{{ $user->nama }}" required class="w-full text-xs border p-2 rounded">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Username</label>
                <input type="text" name="username" value="{{ $user->username }}" required class="w-full text-xs border p-2 rounded">
            </div>
            <button type="submit" class="w-full bg-red-600 text-white text-xs py-2 rounded font-semibold">Simpan Perubahan</button>
        </form>
    </div>

    <div class="pt-2">
        <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
            @csrf
            <button type="submit" class="w-full text-center bg-gray-100 border border-gray-300 text-red-600 hover:bg-red-50 hover:border-red-300 py-2.5 rounded-xl text-xs font-bold transition-colors">
                🚪 Logout dari Sistem
            </button>
        </form>
    </div>
</div>
@endsection