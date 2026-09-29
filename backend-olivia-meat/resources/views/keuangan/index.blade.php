@extends('layouts.app')

@section('content')
<div>
    <h2 class="font-bold text-gray-700 text-base mb-3">Menu Keuangan & Laporan</h2>

    <!-- Tab Navigasi Keuangan -->
    <div class="flex border-b border-gray-200 mb-4 overflow-x-auto text-[11px]">
        <a href="{{ route('keuangan.index', ['tab' => 'labarugi']) }}" class="px-3 py-2 font-semibold whitespace-nowrap {{ $tab == 'labarugi' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-500' }}">Laba Rugi</a>
        <a href="{{ route('keuangan.index', ['tab' => 'riwayat']) }}" class="px-3 py-2 font-semibold whitespace-nowrap {{ $tab == 'riwayat' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-500' }}">Riwayat Transaksi</a>
        <a href="{{ route('keuangan.index', ['tab' => 'piutang']) }}" class="px-3 py-2 font-semibold whitespace-nowrap {{ $tab == 'piutang' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-500' }}">Piutang</a>
        <a href="{{ route('keuangan.index', ['tab' => 'pengeluaran']) }}" class="px-3 py-2 font-semibold whitespace-nowrap {{ $tab == 'pengeluaran' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-500' }}">List Pengeluaran</a>
    </div>

    @if($tab == 'labarugi')
        <div class="space-y-3">
            <div class="bg-red-50 border-l-4 border-red-600 p-3 rounded shadow-sm">
                <span class="text-xs text-gray-500">Estimasi Laba Bersih</span>
                <p class="text-xl font-bold text-red-700">Rp {{ number_format($labaRugi, 0, ',', '.') }}</p>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-green-50 p-2.5 rounded border border-green-100">
                    <span class="text-[10px] text-gray-500">Total Pemasukan</span>
                    <p class="text-sm font-bold text-green-700">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                </div>
                <div class="bg-orange-50 p-2.5 rounded border border-orange-100">
                    <span class="text-[10px] text-gray-500">Total Pengeluaran</span>
                    <p class="text-sm font-bold text-orange-700">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    @elseif($tab == 'riwayat')
        <div class="space-y-2">
            @foreach($pesanan as $p)
                <div class="bg-white border rounded p-2.5 text-xs shadow-sm flex justify-between items-center">
                    <div>
                        <span class="font-bold text-red-600">{{ $p->id_pesanan }}</span>
                        <p class="text-gray-500">Tanggal: {{ $p->tgl_order }} | Metode: {{ $p->metode_bayar }}</p>
                    </div>
                    <span class="px-2 py-1 rounded text-[10px] {{ $p->status_bayar == 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ $p->status_bayar }}</span>
                </div>
            @endforeach
        </div>
    @elseif($tab == 'piutang')
        <div class="mb-3">
            <a href="{{ route('piutang.index') }}" class="block w-full bg-blue-50 text-blue-600 border border-blue-200 text-center py-2 rounded shadow-sm text-xs font-bold hover:bg-blue-100">
                Kelola & Bayar Piutang &rarr;
            </a>
        </div>
        <div class="space-y-2">
            @foreach($piutang as $pi)
                <div class="bg-white border rounded p-2.5 text-xs shadow-sm flex justify-between items-center">
                    <div>
                        <span class="font-bold text-blue-600">{{ $pi->id_pesanan }}</span>
                        <p class="text-gray-500">Tagihan: Rp {{ number_format($pi->total_tagihan, 0, ',', '.') }}</p>
                    </div>
                    <span class="px-2 py-1 rounded text-[10px] {{ $pi->status_piutang == 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $pi->status_piutang }}</span>
                </div>
            @endforeach
        </div>
    @else
        <div class="space-y-2">
            @foreach($pengeluaran as $pen)
                <div class="bg-white border rounded p-2.5 text-xs shadow-sm flex justify-between items-center">
                    <div>
                        <span class="font-bold text-orange-600">{{ $pen->kategori_pengeluaran }}</span>
                        <p class="text-gray-500">Rp {{ number_format($pen->nominal, 0, ',', '.') }} - {{ $pen->keterangan }}</p>
                    </div>
                    <span class="text-[10px] text-gray-400">{{ $pen->tgl_pengeluaran }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection