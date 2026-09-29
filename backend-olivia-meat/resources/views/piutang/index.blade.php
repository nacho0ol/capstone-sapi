@extends('layouts.app')

@section('content')
<div x-data="{ showForm: false, selectedId: null, maxBayar: 0, sisaTagihan: 0 }">
    <h2 class="font-bold text-gray-700 text-lg mb-4">Monitoring Piutang Pelanggan</h2>

    <!-- Modal Pembayaran -->
    <div x-show="showForm" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center p-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-lg w-full max-w-sm p-4 shadow-xl relative">
            <button @click="showForm = false" class="absolute top-3 right-3 text-gray-500 hover:text-red-500 font-bold text-xl">&times;</button>
            <h3 class="font-bold text-gray-800 mb-4 text-base border-b pb-2">Bayar Cicilan Piutang</h3>
            
            <form :action="'{{ route('piutang.index') }}/bayar/' + selectedId" method="POST" class="space-y-3">
                @csrf
                <p class="text-sm text-gray-600">Sisa Tagihan: Rp <span x-text="sisaTagihan.toLocaleString('id-ID')"></span></p>

                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">Nominal Pembayaran (Rp)</label>
                    <input type="number" name="nominal_bayar" min="1" :max="maxBayar" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" placeholder="Contoh: 50000" required>
                </div>

                <div class="pt-2 flex space-x-2">
                    <button type="button" @click="showForm = false" class="flex-1 border border-gray-300 text-gray-700 py-2 rounded font-semibold text-sm hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 bg-red-600 text-white py-2 rounded font-semibold text-sm hover:bg-red-700 shadow">Simpan Bayar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($piutang as $p)
            <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="text-xs font-bold text-blue-600">{{ $p->id_pesanan }}</span>
                        <p class="text-sm font-semibold text-gray-800">{{ $p->pesanan->pelanggan->nama_pelanggan ?? 'Unknown' }}</p>
                        <p class="text-xs text-gray-500">Jatuh Tempo: {{ date('d M Y', strtotime($p->tgl_jatuh_tempo)) }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] px-2 py-1 rounded {{ $p->status_piutang == 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $p->status_piutang }}
                        </span>
                    </div>
                </div>

                <div class="bg-gray-50 p-2 rounded text-xs text-gray-600 flex justify-between items-center">
                    <div>
                        <p>Total: Rp {{ number_format($p->total_tagihan, 0, ',', '.') }}</p>
                        <p>Terbayar: Rp {{ number_format($p->jumlah_terbayar, 0, ',', '.') }}</p>
                    </div>
                    @if($p->status_piutang != 'Lunas')
                    <button @click="selectedId = {{ $p->id_piutang }}; maxBayar = {{ $p->total_tagihan - $p->jumlah_terbayar }}; sisaTagihan = maxBayar; showForm = true;" class="bg-green-600 text-white px-3 py-1.5 rounded font-bold hover:bg-green-700 shadow">
                        Bayar
                    </button>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-center text-gray-400 text-sm py-8">Belum ada data piutang tempo.</p>
        @endforelse
    </div>
</div>
@endsection