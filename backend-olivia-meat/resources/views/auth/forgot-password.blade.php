<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Olivia Meat N Fresh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl border border-border-muted overflow-hidden">
        <!-- Header Banner Merah -->
        <div class="bg-[#842529] p-6 text-center text-white relative">
            <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center mx-auto mb-3 border border-white/20 p-2 shadow-md">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Logo" class="max-h-full max-w-full object-contain">
            </div>
            <h1 class="text-xl font-bold tracking-tight">Lupa Password?</h1>
            <p class="text-xs text-rose-100 mt-1">Masukkan email untuk menerima link reset</p>
        </div>

        <div class="p-6">
            <!-- Flash Notifikasi Success -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-2 rounded-xl mb-4 text-xs flex items-center space-x-2">
                    <x-lucide-check-circle class="w-4 h-4 text-emerald-600 flex-shrink-0" />
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Pesan Error -->
            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-3 py-2 rounded-xl mb-4 text-xs">
                    <ul class="list-disc list-inside space-y-0.5 font-bold">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Input Email -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <x-lucide-mail class="w-4 h-4 text-border-muted" />
                        </span>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               class="w-full pl-9 pr-3 py-2.5 bg-gray-50 border border-border-muted rounded-xl text-xs text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all" 
                               placeholder="admin@example.com" 
                               required 
                               autofocus>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" 
                        class="w-full bg-[#842529] hover:bg-red-900 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow-md shadow-red-200 flex items-center justify-center gap-2 transition-all active:scale-95">
                    <x-lucide-send class="w-4 h-4 text-white" />
                    <span>Kirim Link Reset Password</span>
                </button>
            </form>

            <div class="mt-5 text-center">
                <a href="{{ route('login') }}" class="text-xs text-gray-500 hover:text-[#842529] font-bold flex items-center justify-center gap-1.5 transition-colors">
                    <x-lucide-arrow-left class="w-3.5 h-3.5" />
                    <span>Kembali ke Halaman Login</span>
                </a>
            </div>
        </div>
    </div>
</body>
</html>