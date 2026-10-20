<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olivia Meat N Fresh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 flex justify-center min-h-screen text-gray-800 antialiased" x-data="{ openAddMenu: false }">

    <!-- Container Frame Web Mobile View -->
    <div class="w-full max-w-md bg-white min-h-screen shadow-xl flex flex-col justify-between relative pb-20">
        
        <!-- Header Atas dengan Logo (.jpeg) -->
        <header class="bg-[#842529] text-white px-4 py-3 sticky top-0 z-40 flex justify-between items-center shadow-md">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-white rounded-xl flex items-center justify-center p-1 shadow-sm flex-shrink-0">
                    <img src="{{ asset('images/logo.jpeg') }}" 
                         alt="Logo Olivia Meat N Fresh" 
                         class="max-h-full max-w-full object-contain"
                         onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                    <x-lucide-beef class="w-5 h-5 text-[#842529] hidden" />
                </div>
                <h1 class="font-bold text-sm tracking-tight">Olivia Meat N Fresh</h1>
            </div>

            <a href="{{ route('profil.index') }}" 
               class="text-xs bg-black/20 hover:bg-black/30 px-3 py-1.5 rounded-xl font-semibold flex items-center gap-1.5 transition-all border border-white/10">
                <x-lucide-user class="w-3.5 h-3.5" />
                <span>{{ auth()->user()->nama ?? 'Profil' }}</span>
            </a>
        </header>

        <!-- Main Content & Notifikasi -->
        <main class="p-4 flex-1">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-3 py-2.5 rounded-xl mb-3 text-xs font-medium flex items-center gap-2 shadow-sm">
                    <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600 flex-shrink-0" />
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-3 py-2.5 rounded-xl mb-3 text-xs font-medium flex items-center gap-2 shadow-sm">
                    <x-lucide-alert-circle class="w-4 h-4 text-rose-600 flex-shrink-0" />
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="bg-sky-50 border border-sky-200 text-sky-800 px-3 py-2.5 rounded-xl mb-3 text-xs font-medium flex items-center gap-2 shadow-sm">
                    <x-lucide-info class="w-4 h-4 text-sky-600 flex-shrink-0" />
                    <span>{{ session('info') }}</span>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- POPUP MENU TAMBAH DATA (+) -->
        <div x-show="openAddMenu" x-cloak 
             @click.away="openAddMenu = false" 
             class="fixed bottom-20 left-1/2 transform -translate-x-1/2 w-11/12 max-w-sm bg-white rounded-2xl shadow-2xl border border-border-muted p-4 z-50 space-y-2" 
             x-transition>
            
            <div class="flex justify-between items-center border-b border-border-muted pb-2 mb-2">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Pilih Aksi Tambah Data</p>
                <button @click="openAddMenu = false" class="text-gray-400 hover:text-[#842529]">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            <a href="{{ route('transaksi.index') }}" 
               class="flex items-center gap-2.5 bg-[#FAE2E2]/60 text-[#842529] hover:bg-[#FAE2E2] text-xs font-semibold p-2.5 rounded-xl transition-all">
                <x-lucide-shopping-bag class="w-4 h-4" />
                <span>Catat Pesanan Baru</span>
            </a>

            <a href="{{ url('/pengeluaran') }}" 
               class="flex items-center gap-2.5 bg-amber-50 text-amber-800 hover:bg-amber-100 text-xs font-semibold p-2.5 rounded-xl transition-all border border-amber-100">
                <x-lucide-wallet class="w-4 h-4 text-amber-700" />
                <span>Tambah Pengeluaran Harian</span>
            </a>

            <a href="{{ route('master.index', ['tab' => 'produk']) }}" 
               class="flex items-center gap-2.5 bg-sky-50 text-sky-800 hover:bg-sky-100 text-xs font-semibold p-2.5 rounded-xl transition-all border border-sky-100">
                <x-lucide-beef class="w-4 h-4 text-sky-700" />
                <span>Tambah Produk Baru</span>
            </a>

            <a href="{{ route('master.index', ['tab' => 'pelanggan']) }}" 
               class="flex items-center gap-2.5 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 text-xs font-semibold p-2.5 rounded-xl transition-all border border-emerald-100">
                <x-lucide-user-plus class="w-4 h-4 text-emerald-700" />
                <span>Tambah Pelanggan Baru</span>
            </a>
        </div>

        <!-- Navigation Bar Bawah Mobile -->
        <nav class="w-full max-w-md bg-white border-t border-border-muted fixed bottom-0 flex justify-around items-center py-2 shadow-lg z-40">
            <a href="{{ route('dashboard') }}" 
               class="flex flex-col items-center gap-0.5 text-[10px] {{ request()->routeIs('dashboard') ? 'text-[#842529] font-bold' : 'text-gray-500' }}">
                <x-lucide-layout-dashboard class="w-5 h-5" />
                <span>Beranda</span>
            </a>

            <a href="{{ route('master.index') }}" 
               class="flex flex-col items-center gap-0.5 text-[10px] {{ request()->routeIs('master.*') ? 'text-[#842529] font-bold' : 'text-gray-500' }}">
                <x-lucide-database class="w-5 h-5" />
                <span>Kelola Data</span>
            </a>
            
            <!-- Tombol Floating (+) -->
            <button @click="openAddMenu = !openAddMenu" class="flex flex-col items-center gap-0.5 text-[10px] text-gray-500 focus:outline-none -mt-4">
                <div class="w-11 h-11 bg-[#842529] text-white rounded-full flex items-center justify-center shadow-md border-2 border-white active:scale-90 transition-transform">
                    <x-lucide-plus class="w-6 h-6 text-white" />
                </div>
                <span class="font-bold text-[#842529]">Tambah</span>
            </button>

            <a href="{{ route('keuangan.index') }}" 
               class="flex flex-col items-center gap-0.5 text-[10px] {{ request()->routeIs('keuangan.*') ? 'text-[#842529] font-bold' : 'text-gray-500' }}">
                <x-lucide-receipt class="w-5 h-5" />
                <span>Keuangan</span>
            </a>

            <a href="{{ route('profil.index') }}" 
               class="flex flex-col items-center gap-0.5 text-[10px] {{ request()->routeIs('profil.*') ? 'text-[#842529] font-bold' : 'text-gray-500' }}">
                <x-lucide-user class="w-5 h-5" />
                <span>Akun</span>
            </a>
        </nav>

    </div>

</body>
</html>