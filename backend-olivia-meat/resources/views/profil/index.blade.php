@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <!-- Header Halaman -->
    <div>
        <h1 class="text-xl font-bold text-gray-900">Pengaturan Profil</h1>
        <p class="text-xs text-gray-500 mt-0.5">Kelola informasi akun dan data pengguna</p>
    </div>

    <!-- Ringkasan Informasi Akun -->
    <div class="bg-white p-4 rounded-2xl border border-border-muted shadow-sm flex items-center gap-3">
        <div class="w-12 h-12 bg-[#FAE2E2] rounded-2xl flex items-center justify-center text-[#842529] flex-shrink-0">
            <x-lucide-user class="w-6 h-6" />
        </div>
        <div class="overflow-hidden">
            <h2 class="font-bold text-gray-900 text-sm truncate">{{ $user->nama }}</h2>
            <p class="text-xs text-gray-500 font-mono">@<span>{{ $user->username }}</span></p>
            <span class="inline-flex items-center gap-1 text-[10px] bg-emerald-50 text-emerald-700 font-semibold px-2 py-0.5 rounded-md border border-emerald-200 mt-1">
                <x-lucide-shield-check class="w-3 h-3 text-emerald-600" />
                <span>Administrator / Kasir</span>
            </span>
        </div>
    </div>

    <!-- Form Update Data Profil -->
    <div class="bg-white p-5 rounded-2xl border border-border-muted shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider flex items-center gap-2 border-b border-border-muted pb-3">
            <x-lucide-user-cog class="w-4 h-4 text-[#842529]" />
            <span>Update Data Profil</span>
        </h3>

        <form action="{{ route('profil.update') }}" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required 
                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Username</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" required 
                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
            </div>

            <button type="submit" 
                    class="w-full bg-[#842529] hover:bg-red-900 text-white text-xs py-2.5 rounded-xl font-bold transition-all shadow-md mt-2 flex items-center justify-center gap-2 active:scale-95">
                <x-lucide-save class="w-4 h-4" />
                <span>Simpan Perubahan</span>
            </button>
        </form>
    </div>

    <!-- Tombol Logout -->
    <div class="pt-2">
        <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
            @csrf
            <button type="submit" 
                    class="w-full flex items-center justify-center gap-2 bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 hover:border-rose-300 py-3 rounded-2xl text-xs font-bold transition-all shadow-sm active:scale-95">
                <x-lucide-log-out class="w-4 h-4" />
                <span>Logout dari Sistem</span>
            </button>
        </form>
    </div>
</div>
@endsection