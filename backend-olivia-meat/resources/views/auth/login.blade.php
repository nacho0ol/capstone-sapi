<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Olivia Meat N Fresh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js & CSS Anti-Flicker (x-cloak) -->
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl border border-border-muted overflow-hidden" x-data="{ showPw: false }">
        <!-- Header Banner Merah Olivia (#842529) + Logo Utama -->
        <div class="bg-[#842529] p-6 text-center text-white relative">
            <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mx-auto mb-3 border border-white/20 p-2 shadow-md">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo Olivia Meat N Fresh" class="max-h-full max-w-full object-contain">
            </div>
            <h1 class="text-xl font-bold tracking-tight">Olivia Meat N Fresh</h1>
            <p class="text-xs text-rose-100 mt-1">Sistem Informasi Akuntansi & Monitoring Piutang</p>
        </div>

        <div class="p-6">
            <!-- Flash Notifikasi Success -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-2 rounded-xl mb-4 text-xs flex items-center space-x-2">
                    <x-lucide-check-circle class="w-4 h-4 text-emerald-600 flex-shrink-0" />
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Pesan Error Login -->
            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-3 py-2 rounded-xl mb-4 text-xs space-y-1">
                    <p class="font-bold flex items-center space-x-1.5">
                        <x-lucide-alert-triangle class="w-4 h-4 text-rose-600 flex-shrink-0" />
                        <span>Gagal Masuk:</span>
                    </p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Input Username -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-lucide-user class="w-4 h-4 text-border-muted" />
                        </span>
                        <input type="text" 
                               name="username" 
                               value="{{ old('username') }}" 
                               class="w-full pl-9 pr-3 py-2.5 bg-gray-50 border border-border-muted rounded-xl text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all" 
                               placeholder="Masukkan username" 
                               required 
                               autofocus>
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-lucide-lock class="w-4 h-4 text-border-muted" />
                        </span>
                        <input :type="showPw ? 'text' : 'password'" 
                               name="password" 
                               class="w-full pl-9 pr-10 py-2.5 bg-gray-50 border border-border-muted rounded-xl text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all" 
                               placeholder="Masukkan password" 
                               required>
                        
                        <!-- Toggle Password Icon -->
                        <button type="button" 
                                @click="showPw = !showPw" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors">
                            <span x-show="!showPw" class="flex items-center">
                                <x-lucide-eye class="w-4 h-4" />
                            </span>
                            <span x-show="showPw" x-cloak class="flex items-center">
                                <x-lucide-eye-off class="w-4 h-4" />
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs text-gray-600 pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="accent-[#842529] rounded">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" 
                        class="w-full bg-[#842529] hover:bg-red-900 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow-md shadow-red-200 flex items-center justify-center gap-2 transition-all duration-150 transform active:scale-95">
                    <x-lucide-log-in class="w-4 h-4 text-white" />
                    <span>Masuk ke Sistem</span>
                </button>
            </form>

        </div>
    </div>

</body>
</html>