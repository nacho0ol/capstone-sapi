<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olivia Meat N Fresh - Sistem Informasi Akuntansi & Piutang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen text-gray-800 p-4 antialiased">

    <!-- Card Landing Portal -->
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-border-muted overflow-hidden flex flex-col justify-between p-6 sm:p-8 space-y-6 text-center">
        
        <!-- Branding Logo & Title -->
        <div class="flex flex-col items-center space-y-4 pt-2">
            <div class="w-20 h-20 bg-white rounded-2xl flex items-center justify-center p-2 shadow-md border border-gray-100">
                <img src="{{ asset('images/logo.jpeg') }}" 
                     alt="Logo Olivia Meat N Fresh" 
                     class="max-h-full max-w-full object-contain"
                     onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                <x-lucide-beef class="w-10 h-10 text-[#842529] hidden" />
            </div>

            <div>
                <h1 class="text-xl font-extrabold text-[#842529] tracking-tight">Olivia Meat N Fresh</h1>
                <p class="text-xs text-gray-500 font-medium mt-1">Sistem Informasi Akuntansi & Monitoring Piutang</p>
            </div>
        </div>

        <!-- Ringkasan Layanan -->
        <div class="bg-[#FAE2E2]/50 border border-[#842529]/10 rounded-2xl p-4 text-left space-y-2">
            <div class="flex items-center gap-2 text-[#842529] font-bold text-xs">
                <x-lucide-store class="w-4 h-4" />
                <span>Portal Layanan Toko</span>
            </div>
            <p class="text-xs text-gray-600 leading-relaxed">
                Platform internal untuk pencatatan pesanan harian, manajemen pengeluaran operasional, serta monitoring piutang tempo pelanggan secara terintegrasi.
            </p>
        </div>

        <!-- Tombol Aksi Login / Dashboard -->
        <div class="space-y-3 pt-2">
            @auth
                <a href="{{ route('dashboard') }}" 
                   class="w-full flex items-center justify-center gap-2 bg-[#842529] hover:bg-red-900 text-white py-3 rounded-xl text-xs font-bold shadow-md transition-all active:scale-95">
                    <x-lucide-layout-dashboard class="w-4 h-4 text-white" />
                    <span>Masuk ke Dashboard</span>
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="w-full flex items-center justify-center gap-2 bg-[#842529] hover:bg-red-900 text-white py-3 rounded-xl text-xs font-bold shadow-md transition-all active:scale-95">
                    <x-lucide-log-in class="w-4 h-4 text-white" />
                    <span>Masuk ke Sistem (Login)</span>
                </a>
            @endauth
        </div>

        <!-- Footer Hak Cipta -->
        <p class="text-[11px] text-gray-400 font-medium border-t border-border-muted pt-4">
            &copy; {{ date('Y') }} Olivia Meat N Fresh &bull; Capstone Project
        </p>
    </div>

</body>
</html>