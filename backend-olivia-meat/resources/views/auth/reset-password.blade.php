<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Olivia Meat N Fresh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl border border-border-muted overflow-hidden">
        <div class="bg-[#842529] p-6 text-center text-white relative">
            <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mx-auto mb-3 border border-white/20 p-2 shadow-md">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="max-h-full max-w-full object-contain">
            </div>
            <h1 class="text-xl font-bold tracking-tight">Buat Password Baru</h1>
            <p class="text-xs text-rose-100 mt-1">Silakan masukkan password baru Anda</p>
        </div>

        <div class="p-6">
            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-3 py-2 rounded-xl mb-4 text-xs">
                    <ul class="list-disc list-inside space-y-0.5 font-bold">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Token Rahasia (Jangan Dihapus) -->
                <input type="hidden" name="token" value="{{ $token }}">
                <!-- Email asli yang tadi minta reset -->
                <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                <div x-data="{ show: false }">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Password Baru (Min. 8 Karakter)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-lucide-lock class="w-4 h-4 text-border-muted" />
                        </span>
                        <input :type="show ? 'text' : 'password'" name="password" required
                               class="w-full pl-9 pr-10 py-2.5 bg-gray-50 border border-border-muted rounded-xl text-xs focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-[#842529]">
                            <x-lucide-eye x-show="!show" class="w-4 h-4" />
                            <x-lucide-eye-off x-show="show" class="w-4 h-4" x-cloak />
                        </button>
                    </div>
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-lucide-lock class="w-4 h-4 text-border-muted" />
                        </span>
                        <input :type="show ? 'text' : 'password'" name="password_confirmation" required
                               class="w-full pl-9 pr-10 py-2.5 bg-gray-50 border border-border-muted rounded-xl text-xs focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-gray-400 hover:text-[#842529]">
                            <x-lucide-eye x-show="!show" class="w-4 h-4" />
                            <x-lucide-eye-off x-show="show" class="w-4 h-4" x-cloak />
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#842529] hover:bg-red-900 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow-md shadow-red-200 transition-all active:scale-95">
                    Simpan Password Baru
                </button>
            </form>
        </div>
    </div>
</body>
</html>