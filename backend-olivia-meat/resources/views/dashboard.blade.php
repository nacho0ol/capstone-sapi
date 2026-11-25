@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Dashboard -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Ringkasan Keuangan</h1>
            <p class="text-xs text-gray-500 mt-0.5">Monitoring pemesanan, pemasokan daging, & tagihan Olivia Meat N Fresh</p>
        </div>
    </div>

    <!-- Tombol Aksi Cepat (Pemesanan, Pemasokan Daging, Pengeluaran) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <a href="{{ route('transaksi.index') }}" 
           class="flex items-center justify-center gap-2 bg-[#842529] hover:bg-red-900 text-white py-2.5 px-4 rounded-xl text-xs font-bold shadow-sm transition-all active:scale-95">
            <x-lucide-shopping-bag class="w-4 h-4 text-white" />
            <span>Catat Pemesanan Baru</span>
        </a>

        <a href="{{ route('pemasokan.index') }}" 
           class="flex items-center justify-center gap-2 bg-[#842529] hover:bg-red-900 text-white py-2.5 px-4 rounded-xl text-xs font-bold shadow-sm transition-all active:scale-95">
            <x-lucide-truck class="w-4 h-4 text-white" />
            <span>Catat Pemasokan Daging</span>
        </a>

        <a href="{{ route('keuangan.index', ['tab' => 'pengeluaran']) }}" 
           class="flex items-center justify-center gap-2 bg-[#842529] hover:bg-red-900 text-white py-2.5 px-4 rounded-xl text-xs font-bold shadow-sm transition-all active:scale-95">
            <x-lucide-wallet class="w-4 h-4 text-white" />
            <span>Tambah Pengeluaran</span>
        </a>
    </div>

    <!-- Hero Card 1: Estimasi Laba Bersih -->
    <div class="bg-white border border-border-muted rounded-2xl p-5 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-between">
            <div>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                    <x-lucide-scale class="w-4 h-4 text-[#842529]" />
                    Estimasi Laba Bersih
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-[#842529] mt-1">
                    Rp {{ number_format($labaRugi, 0, ',', '.') }}
                </h2>
            </div>
            <div class="w-12 h-12 bg-[#FAE2E2] rounded-2xl flex items-center justify-center flex-shrink-0">
                <x-lucide-trending-up class="w-6 h-6 text-[#842529]" />
            </div>
        </div>
    </div>

    <!-- Hero Card 2: Tagihan Belum Lunas -->
    <div class="bg-white border border-border-muted rounded-2xl p-5 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-between">
            <div>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                    <x-lucide-clock class="w-4 h-4 text-sky-600" />
                    Total Tagihan Belum Lunas
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-sky-700 mt-1">
                    Rp {{ number_format($totalPiutang, 0, ',', '.') }}
                </h2>
            </div>
            <div class="w-12 h-12 bg-sky-50 border border-sky-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                <x-lucide-receipt-text class="w-6 h-6 text-sky-600" />
            </div>
        </div>
    </div>

    <!-- Grid 2 Kolom: Pemasukan & Pengeluaran -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- Total Pemasukan -->
        <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm flex items-center justify-between overflow-hidden">
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">Total Pemasukan</p>
                <p class="text-lg sm:text-xl font-bold text-emerald-700">
                    Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                </p>
            </div>
            <div class="w-10 h-10 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 flex-shrink-0">
                <x-lucide-arrow-down-left class="w-5 h-5" />
            </div>
        </div>

        <!-- Total Pengeluaran -->
        <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm flex items-center justify-between overflow-hidden">
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1">Total Pengeluaran</p>
                <p class="text-lg sm:text-xl font-bold text-amber-700">
                    Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                </p>
            </div>
            <div class="w-10 h-10 bg-amber-50 border border-amber-100 rounded-xl flex items-center justify-center text-amber-600 flex-shrink-0">
                <x-lucide-arrow-up-right class="w-5 h-5" />
            </div>
        </div>
    </div>

    <!-- Grid 2 Kolom: List Pemesanan Hari Ini & Pemasokan Daging Hari Ini -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        
        <!-- CARD 1: PESANAN HARI INI -->
        <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b border-border-muted pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-[#FAE2E2] text-[#842529] flex items-center justify-center flex-shrink-0">
                        <x-lucide-shopping-bag class="w-4 h-4" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Pemesanan Hari Ini</h3>
                        <p class="text-[11px] text-gray-400">{{ date('d M Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#FAE2E2] text-[#842529]">
                        {{ $orderanHariIni->count() }} Order
                    </span>
                    <a href="{{ route('transaksi.index') }}" class="text-xs font-bold text-[#842529] hover:underline flex items-center gap-1">
                        <span>Semua</span>
                        <x-lucide-arrow-right class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>

            <div class="space-y-2">
                @forelse($orderanHariIni as $ord)
                    @php
                        $totalOrder = $ord->detail->sum('subtotal');
                        $statusLower = strtolower($ord->status_pemesanan);
                        $isCancelled = in_array($statusLower, ['ditolak', 'dibatalkan']);
                    @endphp
                    <div class="bg-gray-50 border border-border-muted rounded-xl p-3 text-xs space-y-1.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-[#842529]">{{ $ord->id_pesanan }}</span>
                                <span class="text-gray-300">•</span>
                                <span class="font-bold text-gray-800">{{ $ord->pelanggan->nama_pelanggan ?? 'Umum' }}</span>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $isCancelled ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-sky-50 border-sky-200 text-sky-700' }}">
                                    {{ ucfirst($ord->status_pemesanan) }}
                                </span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $ord->status_bayar == 'Lunas' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}">
                                    {{ $ord->status_bayar }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-2 text-[11px] text-gray-500">
                            <span class="truncate">
                                @foreach($ord->detail as $det)
                                    {{ $det->produk->nama_produk ?? 'Produk' }} ({{ number_format($det->qty, 2, ',', '.') }} kg){{ !$loop->last ? ', ' : '' }}
                                @endforeach
                            </span>
                            <strong class="text-gray-900 flex-shrink-0">Rp {{ number_format($totalOrder, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <x-lucide-shopping-bag class="w-8 h-8 text-gray-300 mx-auto mb-1.5" />
                        <p class="text-xs text-gray-400">Belum ada pemesanan masuk hari ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- CARD 2: PEMASOKAN DAGING HARI INI -->
        <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b border-border-muted pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center flex-shrink-0">
                        <x-lucide-truck class="w-4 h-4" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Pemasokan Daging Hari Ini</h3>
                        <p class="text-[11px] text-gray-400">{{ date('d M Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-800">
                        {{ $pemasokanHariIni->count() }} Masuk
                    </span>
                    <a href="{{ route('pemasokan.index') }}" class="text-xs font-bold text-[#842529] hover:underline flex items-center gap-1">
                        <span>Semua</span>
                        <x-lucide-arrow-right class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>

            <div class="space-y-2">
                @forelse($pemasokanHariIni as $pm)
                    <div class="bg-gray-50 border border-border-muted rounded-xl p-3 text-xs space-y-1.5">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-bold text-gray-900">{{ $pm->produk->nama_produk ?? 'Produk Terhapus' }}</span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#FAE2E2] text-[#842529]">
                                    {{ number_format($pm->jumlah, 2, ',', '.') }} kg
                                </span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-white border border-border-muted text-gray-700">
                                    {{ $pm->jagal->nama_jagal ?? '-' }}
                                </span>
                            </div>

                            <strong class="text-gray-900">Rp {{ number_format($pm->total_harga, 0, ',', '.') }}</strong>
                        </div>

                        @if($pm->catatan)
                            <p class="text-[11px] italic text-gray-500 truncate">"{{ $pm->catatan }}"</p>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8">
                        <x-lucide-truck class="w-8 h-8 text-gray-300 mx-auto mb-1.5" />
                        <p class="text-xs text-gray-400">Belum ada pemasokan daging hari ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection