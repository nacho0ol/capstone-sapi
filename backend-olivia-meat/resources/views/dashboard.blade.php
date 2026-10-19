@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Dashboard -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Ringkasan Keuangan</h1>
            <p class="text-xs text-gray-500 mt-0.5">Monitoring transaksi & piutang Olivia Meat N Fresh</p>
        </div>
        <a href="{{ route('transaksi.index') }}" 
           class="inline-flex items-center justify-center gap-2 bg-[#842529] hover:bg-red-900 text-white py-2.5 px-4 rounded-xl text-xs font-semibold shadow-sm transition-all duration-150 active:scale-95">
            <x-lucide-plus-circle class="w-4 h-4 text-white" />
            <span>Catat Pesanan Baru</span>
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

    <!-- Hero Card 2: Piutang Belum Lunas (Digedein) -->
    <div class="bg-white border border-border-muted rounded-2xl p-5 shadow-sm relative overflow-hidden">
        <div class="flex items-center justify-between">
            <div>
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                    <x-lucide-clock class="w-4 h-4 text-sky-600" />
                    Total Piutang Belum Lunas
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

    <!-- Tombol Catat Pesanan Baru (Khusus Tampilan Mobile) -->
    <div class="sm:hidden pt-2">
        <a href="{{ route('transaksi.index') }}" 
           class="w-full flex items-center justify-center gap-2 bg-[#842529] hover:bg-red-900 text-white py-3 rounded-xl font-bold text-xs shadow-md transition-all active:scale-95">
            <x-lucide-plus class="w-4 h-4 text-white" />
            <span>Catat Pesanan Baru</span>
        </a>
    </div>
</div>
@endsection