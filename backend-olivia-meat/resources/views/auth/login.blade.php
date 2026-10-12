<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Olivia Meat N Fresh</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden" x-data="{ showPw: false }">
        <!-- Header Banner Merah -->
        <div class="bg-gradient-to-r from-red-700 to-red-600 p-6 text-center text-white relative">
            <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner text-3xl">
                🥩
            </div>
            <h1 class="text-xl font-bold tracking-tight">Olivia Meat N Fresh</h1>
            <p class="text-xs text-red-100 mt-1">Sistem Informasi Akuntansi & Monitoring Piutang</p>
        </div>

        <div class="p-6">
            <!-- Flash Notifikasi Success -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded-lg mb-4 text-xs flex items-center space-x-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Pesan Error Login Sesuai Activity Diagram -->
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg mb-4 text-xs">
                    <p class="font-bold flex items-center space-x-1 mb-0.5">
                        <span>⚠️</span>
                        <span>Gagal Masuk:</span>
                    </p>
                    <ul class="list-disc list-inside">
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
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">👤</span>
                        <input type="text" 
                               name="username" 
                               value="{{ old('username') }}" 
                               class="w-full pl-9 pr-3 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:bg-white transition-all" 
                               placeholder="Masukkan username" 
                               required 
                               autofocus>
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 text-sm">🔒</span>
                        <input :type="showPw ? 'text' : 'password'" 
                               name="password" 
                               class="w-full pl-9 pr-10 py-2.5 bg-gray-50 border border-gray-300 rounded-xl text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:bg-white transition-all" 
                               placeholder="Masukkan password" 
                               required>
                        <button type="button" 
                                @click="showPw = !showPw" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 text-xs">
                            <span x-text="showPw ? '🙈' : '👁️'"></span>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs text-gray-600 pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="accent-red-600 rounded">
                        <span>Ingat saya</span>
                    </label>
                    <span class="text-[11px] text-gray-400 italic">Hak Akses: Pemilik Usaha</span>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow-md shadow-red-200 transition-all duration-150 transform active:scale-95">
                    🚀 Masuk ke Sistem
                </button>
            </form>

            <!-- Bantuan Akun Default -->
            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <p class="text-[11px] text-gray-400">Akun Login Bawaan:</p>
                <div class="inline-block bg-gray-50 border border-gray-200 rounded-lg px-2.5 py-1 text-[11px] text-gray-600 mt-1 font-mono">
                    User: <b>admin</b> &bull; Pass: <b>password</b>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
