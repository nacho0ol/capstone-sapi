@extends('layouts.app')

@section('content')
<div x-data="{ isEditing: {{ $errors->hasAny(['nama', 'nama_user', 'username']) ? 'true' : 'false' }} }" class="space-y-4 max-w-2xl mx-auto">
    
    <!-- Header Halaman -->
    <div>
        <h1 class="text-xl font-bold text-gray-900">Pengaturan Profil</h1>
        <p class="text-xs text-gray-500 mt-0.5">Kelola informasi akun dan data pengguna</p>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-xs font-bold flex items-center gap-2">
            <x-lucide-check-circle class="w-4 h-4" />
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-border-muted rounded-2xl p-5 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-rose-100 text-[#842529] rounded-full flex items-center justify-center font-bold text-xl border border-rose-200">
                <x-lucide-user class="w-7 h-7" />
            </div>
            <div class="space-y-1">
                <h2 class="text-base font-bold text-gray-900">
                    {{ auth()->user()->nama_user ?? auth()->user()->nama ?? auth()->user()->name ?? 'Admin Olivia' }}
                </h2>
                
                <!-- BADGE USERNAME (Menggantikan Badge Role) -->
                <div class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-0.5 rounded-md bg-sky-50 text-sky-700 border border-sky-200">
                    <x-lucide-at-sign class="w-3 h-3 text-sky-600" />
                    <span>{{ auth()->user()->username ?? 'admin' }}</span>
                </div>
            </div>
        </div>

        <!-- Tombol Trigger Edit (Hanya Muncul Saat View Mode) -->
        <button x-show="!isEditing" 
                @click="isEditing = true" 
                class="inline-flex items-center gap-1.5 bg-[#842529] hover:bg-red-900 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow-sm transition-all active:scale-95">
            <x-lucide-square-pen class="w-4 h-4" />
            <span>Edit Profil</span>
        </button>
    </div>

    <!-- 1. TAMPILAN VIEW PROFIL (DEFAULT READ-ONLY) -->
    <div x-show="!isEditing" x-cloak class="bg-white border border-border-muted rounded-2xl p-5 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-border-muted pb-3 flex items-center gap-1.5">
            <x-lucide-user-check class="w-4 h-4 text-[#842529]" />
            <span>Informasi Detail Akun</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
            <div class="bg-gray-50 border border-border-muted p-3.5 rounded-xl space-y-1">
                <span class="text-gray-400 font-medium">Nama Lengkap</span>
                <p class="font-bold text-gray-900 text-sm">
                    {{ auth()->user()->nama_user ?? auth()->user()->nama ?? auth()->user()->name ?? 'Admin Olivia' }}
                </p>
            </div>
            <div class="bg-gray-50 border border-border-muted p-3.5 rounded-xl space-y-1">
                <span class="text-gray-400 font-medium">Username</span>
                <p class="font-bold text-gray-900 text-sm">
                    @ {{ auth()->user()->username ?? 'admin' }}
                </p>
            </div>
        </div>
    </div>

    <!-- 2. TAMPILAN EDIT PROFIL (MUNCUL KETIKA DIKLIK EDIT) -->
    <div x-show="isEditing" x-cloak class="bg-white border border-border-muted rounded-2xl p-5 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-border-muted pb-3 flex items-center gap-1.5">
            <x-lucide-user-cog class="w-4 h-4 text-[#842529]" />
            <span>Update Data Profil</span>
        </h3>

        @if ($errors->hasAny(['nama', 'nama_user', 'username']))
            <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="font-medium">• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('profil.update') }}" method="POST" class="space-y-3">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" 
                       name="nama_user" 
                       value="{{ old('nama_user', auth()->user()->nama_user ?? auth()->user()->nama ?? auth()->user()->name) }}" 
                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all" required>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Username</label>
                <input type="text" 
                       name="username" 
                       value="{{ old('username', auth()->user()->username) }}" 
                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all" required>
            </div>

            <div class="pt-2 flex gap-2">
                <button type="button" 
                        @click="isEditing = false" 
                        class="flex-1 border border-border-muted text-gray-700 py-2.5 rounded-xl font-bold text-xs hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="flex-1 bg-[#842529] hover:bg-red-900 text-white py-2.5 rounded-xl font-bold text-xs shadow-md transition-all active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!--  UPDATE PASSWORD  -->
    <div class="bg-white border border-border-muted rounded-2xl p-5 shadow-sm space-y-4">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider border-b border-border-muted pb-3 flex items-center gap-1.5">
            <x-lucide-lock class="w-4 h-4 text-[#842529]" />
            <span>Update Password</span>
        </h3>

        <form action="{{ route('profil.password.update') }}" method="POST" class="space-y-3">
            @csrf
            @method('PUT')

            <!-- Password Saat Ini -->
            <div x-data="{ show: false }">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Password Saat Ini</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" name="current_password" required
                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all pr-10">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-[#842529] transition-colors focus:outline-none">
                        <x-lucide-eye x-show="!show" class="w-4 h-4" />
                        <x-lucide-eye-off x-show="show" class="w-4 h-4" x-cloak />
                    </button>
                </div>
                @error('current_password') <span class="text-[10px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
            </div>

            <!-- Password Baru -->
            <div x-data="{ show: false }">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Password Baru (Min. 8 Karakter)</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" name="password" required
                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all pr-10">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-[#842529] transition-colors focus:outline-none">
                        <x-lucide-eye x-show="!show" class="w-4 h-4" />
                        <x-lucide-eye-off x-show="show" class="w-4 h-4" x-cloak />
                    </button>
                </div>
                @error('password') <span class="text-[10px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
            </div>

            <!-- Konfirmasi Password Baru -->
            <div x-data="{ show: false }">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all pr-10">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-[#842529] transition-colors focus:outline-none">
                        <x-lucide-eye x-show="!show" class="w-4 h-4" />
                        <x-lucide-eye-off x-show="show" class="w-4 h-4" x-cloak />
                    </button>
                </div>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="bg-[#842529] hover:bg-red-900 text-white px-6 py-2.5 rounded-xl font-bold text-xs shadow-md transition-all active:scale-95">
                    Ganti Password
                </button>
            </div>
        </form>
    </div>

    <!-- BUTTON LOGOUT -->
    <form action="{{ route('logout') }}" method="POST" class="pt-2">
        @csrf
        <button type="submit" 
                class="w-full bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 font-bold py-3 rounded-2xl text-xs transition-colors flex items-center justify-center gap-2 active:scale-98">
            <x-lucide-log-out class="w-4 h-4" />
            <span>Logout dari Sistem</span>
        </button>
    </form>

</div>
@endsection