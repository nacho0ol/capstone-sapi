<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olivia Meat N Fresh</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 flex justify-center">

    <div class="w-full max-w-md bg-white min-h-screen shadow-lg flex flex-col justify-between relative pb-24" x-data="{ openAddMenu: false }">
        
        <!-- Header Atas -->
        <header class="bg-red-700 text-white p-4 sticky top-0 z-40 flex justify-between items-center shadow">
            <h1 class="font-bold text-base">🥩 Olivia Meat N Fresh</h1>
            <a href="{{ route('profil.index') }}" class="text-xs bg-red-800 hover:bg-red-900 px-2.5 py-1.5 rounded font-semibold">Profil / Akun</a>
        </header>

        <!-- Mesej Notifikasi -->
        <main class="p-4 flex-1">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded mb-3 text-xs">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded mb-3 text-xs">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

        <!-- POPUP MENU TAMBAH (+) -->
        <div x-show="openAddMenu" @click.away="openAddMenu = false" class="absolute bottom-20 left-1/2 transform -translate-x-1/2 w-11/12 bg-white rounded-xl shadow-2xl border border-gray-200 p-4 z-50 space-y-2" x-transition>
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 text-center">Pilih Menu Tambah Data</p>
            <a href="{{ route('transaksi.index') }}" class="block bg-red-50 text-red-700 hover:bg-red-100 text-xs font-semibold p-2.5 rounded-lg text-center">➕ Tambah Pesanan Baru</a>
            <a href="{{ url('/pengeluaran') }}" class="block bg-orange-50 text-orange-700 hover:bg-orange-100 text-xs font-semibold p-2.5 rounded-lg text-center">📉 Tambah Pengeluaran Harian</a>
            <a href="{{ route('master.index', ['tab' => 'produk']) }}" class="block bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-semibold p-2.5 rounded-lg text-center">🥩 Tambah Produk Baru</a>
            <a href="{{ route('master.index', ['tab' => 'pelanggan']) }}" class="block bg-green-50 text-green-700 hover:bg-green-100 text-xs font-semibold p-2.5 rounded-lg text-center">👤 Tambah Pelanggan Baru</a>
        </div>

        <!-- Navigation Bar Bawah (Tanpa Piutang Mandiri) -->
        <nav class="w-full max-w-md bg-white border-t border-gray-200 fixed bottom-0 flex justify-around py-2 shadow-inner z-40">
            <a href="{{ route('dashboard') }}" class="text-center text-[10px] {{ request()->routeIs('dashboard') ? 'text-red-700 font-bold' : 'text-gray-600' }}">
                <span class="block text-base">🏠</span> Dashboard
            </a>
            
            <!-- Tombol Plus Trigger Popup -->
            <button @click="openAddMenu = !openAddMenu" class="text-center text-[10px] text-gray-600 focus:outline-none">
                <span class="block text-xl bg-red-600 text-white rounded-full w-8 h-8 mx-auto flex items-center justify-center shadow">＋</span> Tambah
            </button>

            <a href="{{ route('master.index') }}" class="text-center text-[10px] {{ request()->routeIs('master.*') ? 'text-red-700 font-bold' : 'text-gray-600' }}">
                <span class="block text-base">📋</span> Kelola Data
            </a>
            <a href="{{ route('keuangan.index') }}" class="text-center text-[10px] {{ request()->routeIs('keuangan.*') ? 'text-red-700 font-bold' : 'text-gray-600' }}">
                <span class="block text-base">📊</span> Keuangan
            </a>
        </nav>

    </div>

</body>
</html>