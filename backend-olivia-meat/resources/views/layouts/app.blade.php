<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olivia Meat N Fresh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        @media (max-width: 767px) {
            .mobile-drawer {
                transform: translateX(-100%);
            }
            .mobile-drawer.is-open {
                transform: translateX(0);
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.Alpine) {
                var s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js';
                document.head.appendChild(s);
            }
        });
    </script>
</head>
<body class="bg-gray-100 min-h-screen text-gray-800 antialiased" 
      x-data="{ openAddMenu: false, mobileSidebar: false }"
      @keydown.escape.window="mobileSidebar = false; openAddMenu = false">

    <div class="min-h-screen flex flex-col md:flex-row">
        
        <!-- BACKDROP SIDEBAR MOBILE -->
        <div x-show="mobileSidebar" x-cloak 
             @click="mobileSidebar = false"
             class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 md:hidden"
             x-transition.opacity></div>

        <!-- SIDEBAR UTAMA (Desktop Fixed + Mobile Slide-over Drawer) -->
        <aside :class="{ 'is-open': mobileSidebar }"
               class="mobile-drawer fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-border-muted flex flex-col justify-between p-4 shadow-lg md:shadow-sm transition-transform duration-200 ease-in-out overflow-y-auto">
            
            <div class="space-y-6">
                <!-- Logo & Brand Header -->
                <div class="flex items-center justify-between px-2 py-2 border-b border-border-muted pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center p-1 shadow-sm flex-shrink-0 border border-gray-100">
                            <img src="{{ asset('images/logo.jpeg') }}" 
                                 alt="Logo Olivia Meat N Fresh" 
                                 class="max-h-full max-w-full object-contain"
                                 onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                            <x-lucide-beef class="w-6 h-6 text-[#842529] hidden" />
                        </div>
                        <div>
                            <h1 class="font-extrabold text-sm text-[#842529] leading-tight">Olivia Meat N Fresh</h1>
                            <p class="text-[10px] text-gray-400 font-medium">Akuntansi & Tagihan</p>
                        </div>
                    </div>

                    <!-- Tombol Tutup Sidebar di Mobile -->
                    <button type="button" @click="mobileSidebar = false" class="md:hidden text-gray-500 hover:text-[#842529] bg-gray-100 hover:bg-gray-200 p-1.5 rounded-lg transition-colors">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <!-- Menu Navigasi Sidebar -->
                @php
                    $isPengeluaranActive = (request()->routeIs('keuangan.*') && request('tab') === 'pengeluaran') || request()->routeIs('pengeluaran.*');
                    $isKeuanganActive = (request()->routeIs('keuangan.*') && request('tab') !== 'pengeluaran') || request()->routeIs('piutang.*');
                @endphp

                <nav class="space-y-1.5 text-xs font-semibold">
                    <a href="{{ route('dashboard') }}" @click="mobileSidebar = false"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-[#842529] text-white shadow-sm font-bold' : 'text-gray-600 hover:bg-[#FAE2E2]/60 hover:text-[#842529]' }}">
                        <x-lucide-layout-dashboard class="w-4 h-4" />
                        <span>Beranda</span>
                    </a>

                    <a href="{{ route('transaksi.index') }}" @click="mobileSidebar = false"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('transaksi.*') ? 'bg-[#842529] text-white shadow-sm font-bold' : 'text-gray-600 hover:bg-[#FAE2E2]/60 hover:text-[#842529]' }}">
                        <x-lucide-shopping-bag class="w-4 h-4" />
                        <span>Pemesanan</span>
                    </a>

                    <a href="{{ route('pemasokan.index') }}" @click="mobileSidebar = false"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('pemasokan.*') ? 'bg-[#842529] text-white shadow-sm font-bold' : 'text-gray-600 hover:bg-[#FAE2E2]/60 hover:text-[#842529]' }}">
                        <x-lucide-truck class="w-4 h-4" />
                        <span>Pemasokan Daging</span>
                    </a>

                    <a href="{{ route('produk.index') }}" @click="mobileSidebar = false"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('produk.*') ? 'bg-[#842529] text-white shadow-sm font-bold' : 'text-gray-600 hover:bg-[#FAE2E2]/60 hover:text-[#842529]' }}">
                        <x-lucide-beef class="w-4 h-4" />
                        <span>Produk</span>
                    </a>

                    <a href="{{ route('pelanggan.index') }}" @click="mobileSidebar = false"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('pelanggan.*') ? 'bg-[#842529] text-white shadow-sm font-bold' : 'text-gray-600 hover:bg-[#FAE2E2]/60 hover:text-[#842529]' }}">
                        <x-lucide-users class="w-4 h-4" />
                        <span>Pelanggan</span>
                    </a>

                    <a href="{{ route('keuangan.index') }}" @click="mobileSidebar = false"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ $isKeuanganActive ? 'bg-[#842529] text-white shadow-sm font-bold' : 'text-gray-600 hover:bg-[#FAE2E2]/60 hover:text-[#842529]' }}">
                        <x-lucide-receipt class="w-4 h-4" />
                        <span>Keuangan</span>
                    </a>

                    <a href="{{ route('keuangan.index', ['tab' => 'pengeluaran']) }}" @click="mobileSidebar = false"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ $isPengeluaranActive ? 'bg-[#842529] text-white shadow-sm font-bold' : 'text-gray-600 hover:bg-[#FAE2E2]/60 hover:text-[#842529]' }}">
                        <x-lucide-wallet class="w-4 h-4" />
                        <span>Pengeluaran</span>
                    </a>
                </nav>
            </div>

            <!-- Tombol Tambah & Profil Sidebar -->
            <div class="space-y-3 pt-4 border-t border-border-muted mt-6">
                <button type="button" @click="mobileSidebar = false; openAddMenu = !openAddMenu" 
                        class="w-full flex items-center justify-center gap-2 bg-[#842529] hover:bg-red-900 text-white py-2.5 px-3 rounded-xl text-xs font-bold shadow-sm transition-all active:scale-95">
                    <x-lucide-plus-circle class="w-4 h-4 text-white" />
                    <span>Tambah Data Fast</span>
                </button>

                <a href="{{ route('profil.index') }}" @click="mobileSidebar = false"
                   class="flex items-center justify-between p-2.5 rounded-xl bg-gray-50 border border-border-muted hover:border-[#842529] transition-all {{ request()->routeIs('profil.*') ? 'border-[#842529] bg-[#FAE2E2]/30' : '' }}">
                    <div class="flex items-center gap-2 overflow-hidden">
                        <div class="w-7 h-7 rounded-lg bg-[#FAE2E2] text-[#842529] flex items-center justify-center font-bold text-xs flex-shrink-0">
                            <x-lucide-user class="w-4 h-4" />
                        </div>
                        <span class="text-xs font-bold text-gray-800 truncate">{{ auth()->user()->nama ?? 'Profil' }}</span>
                    </div>
                    <x-lucide-chevron-right class="w-4 h-4 text-gray-400 flex-shrink-0" />
                </a>
            </div>
        </aside>

        <!-- AREA UTAMA KONTEN -->
        <div class="flex-1 flex flex-col min-h-screen md:pl-64">
            
            <!-- HEADER MOBILE (Dengan Tombol Hamburger untuk Buka Sidebar) -->
            <header class="md:hidden bg-[#842529] text-white px-4 py-3 sticky top-0 z-30 flex justify-between items-center shadow-md">
                <div class="flex items-center gap-3">
                    <button type="button" @click="mobileSidebar = true" class="p-1.5 rounded-xl bg-black/20 hover:bg-black/30 text-white focus:outline-none">
                        <x-lucide-menu class="w-5 h-5" />
                    </button>
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 bg-white rounded-lg flex items-center justify-center p-1 shadow-sm flex-shrink-0">
                            <img src="{{ asset('images/logo.jpeg') }}" 
                                 alt="Logo" 
                                 class="max-h-full max-w-full object-contain"
                                 onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                            <x-lucide-beef class="w-4 h-4 text-[#842529] hidden" />
                        </div>
                        <h1 class="font-bold text-sm tracking-tight">Olivia Meat N Fresh</h1>
                    </div>
                </div>

                <a href="{{ route('profil.index') }}" 
                   class="text-xs bg-black/20 hover:bg-black/30 px-2.5 py-1.5 rounded-xl font-semibold flex items-center gap-1.5 transition-all border border-white/10">
                    <x-lucide-user class="w-3.5 h-3.5" />
                    <span class="max-w-[80px] truncate">{{ auth()->user()->nama ?? 'Profil' }}</span>
                </a>
            </header>

            <!-- KONTEN HALAMAN -->
            <main class="p-4 sm:p-6 md:p-8 flex-1 w-full max-w-md md:max-w-7xl mx-auto pb-8">
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
        </div>

        <!-- POPUP MENU TAMBAH DATA (+) -->
        <div x-show="openAddMenu" x-cloak 
             @click.away="openAddMenu = false" 
             class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-11/12 max-w-sm bg-white rounded-2xl shadow-2xl border border-border-muted p-4 z-50 space-y-2" 
             x-transition>
            
            <div class="flex justify-between items-center border-b border-border-muted pb-2 mb-2">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Pilih Aksi Tambah Data</p>
                <button type="button" @click="openAddMenu = false" class="text-gray-400 hover:text-[#842529]">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            <a href="{{ route('transaksi.index') }}" 
               class="flex items-center gap-2.5 bg-[#FAE2E2]/60 text-[#842529] hover:bg-[#FAE2E2] text-xs font-semibold p-2.5 rounded-xl transition-all">
                <x-lucide-shopping-bag class="w-4 h-4" />
                <span>Catat Pemesanan Baru</span>
            </a>

            <a href="{{ route('pemasokan.index') }}" 
               class="flex items-center gap-2.5 bg-purple-50 text-purple-800 hover:bg-purple-100 text-xs font-semibold p-2.5 rounded-xl transition-all border border-purple-100">
                <x-lucide-truck class="w-4 h-4 text-purple-700" />
                <span>Catat Pemasokan Daging</span>
            </a>

            <a href="{{ route('keuangan.index', ['tab' => 'pengeluaran']) }}" 
               class="flex items-center gap-2.5 bg-amber-50 text-amber-800 hover:bg-amber-100 text-xs font-semibold p-2.5 rounded-xl transition-all border border-amber-100">
                <x-lucide-wallet class="w-4 h-4 text-amber-700" />
                <span>Tambah Pengeluaran Operasional</span>
            </a>

            <a href="{{ route('produk.index') }}" 
               class="flex items-center gap-2.5 bg-sky-50 text-sky-800 hover:bg-sky-100 text-xs font-semibold p-2.5 rounded-xl transition-all border border-sky-100">
                <x-lucide-beef class="w-4 h-4 text-sky-700" />
                <span>Tambah Produk Baru</span>
            </a>

            <a href="{{ route('produk.index', ['tab' => 'jagal']) }}" 
               class="flex items-center gap-2.5 bg-rose-50 text-rose-800 hover:bg-rose-100 text-xs font-semibold p-2.5 rounded-xl transition-all border border-rose-200">
                <x-lucide-store class="w-4 h-4 text-rose-700" />
                <span>Tambah Jagal Baru</span>
            </a>

            <a href="{{ route('pelanggan.index') }}" 
               class="flex items-center gap-2.5 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 text-xs font-semibold p-2.5 rounded-xl transition-all border border-emerald-100">
                <x-lucide-user-plus class="w-4 h-4 text-emerald-700" />
                <span>Tambah Pelanggan Baru</span>
            </a>
        </div>

    </div>

    <!-- GLOBAL DYNAMIC CONFIRMATION MODAL -->
    <div x-data="{ 
            open: false, 
            actionUrl: '', 
            title: 'Konfirmasi',
            message: '',
            method: 'POST',
            confirmText: 'Ya, Lanjutkan',
            confirmClass: 'bg-rose-600 hover:bg-rose-700'
        }"
        @confirm-action.window="
            actionUrl = $event.detail.action;
            title = $event.detail.title || 'Konfirmasi';
            message = $event.detail.message || 'Apakah Anda yakin ingin melanjutkan?';
            method = $event.detail.method || 'POST';
            confirmText = $event.detail.confirmText || 'Ya, Lanjutkan';
            confirmClass = $event.detail.confirmClass || 'bg-rose-600 hover:bg-rose-700';
            open = true;
        "
        x-show="open" 
        x-cloak 
        style="display: none;" 
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        
        <div @click.away="open = false" class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-5 border border-border-muted text-center space-y-4">
            <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mx-auto">
                <x-lucide-alert-triangle class="w-6 h-6" />
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-900" x-text="title"></h3>
                <p class="text-xs text-gray-500 mt-1 leading-relaxed" x-text="message"></p>
            </div>
            <form :action="actionUrl" method="POST" class="flex gap-2 pt-2">
                @csrf
                <template x-if="method !== 'POST'">
                    <input type="hidden" name="_method" :value="method">
                </template>
                <button type="button" @click="open = false" class="flex-1 border border-border-muted text-gray-700 py-2.5 rounded-xl font-bold text-xs hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit" :class="confirmClass" class="flex-1 text-white py-2.5 rounded-xl font-bold text-xs shadow-md transition-all active:scale-95" x-text="confirmText">
                </button>
            </form>
        </div>
    </div>

</body>
</html>