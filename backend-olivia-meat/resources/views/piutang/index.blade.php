@extends('layouts.app')

@section('content')
<div x-data="{ showForm: false, selectedId: null, maxBayar: 0, sisaTagihan: 0 }" class="space-y-4">
    
    <!-- Header Halaman -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Monitoring Piutang Pelanggan</h1>
            <p class="text-xs text-gray-500 mt-0.5">Pantau status tagihan tempo dan pencatatan cicilan</p>
        </div>
    </div>

    <!-- MODAL PEMBAYARAN CICILAN -->
    <div x-show="showForm" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex justify-center items-center p-4" style="display: none;">
        <div @click.away="showForm = false" class="bg-white rounded-2xl w-full max-w-md p-5 shadow-xl relative border border-border-muted">
            <button @click="showForm = false" class="absolute top-4 right-4 text-gray-400 hover:text-[#842529] p-1 rounded-lg transition-colors">
                <x-lucide-x class="w-5 h-5" />
            </button>
            
            <h3 class="font-bold text-gray-900 text-sm mb-4 border-b border-border-muted pb-3 flex items-center gap-2">
                <x-lucide-coins class="w-4 h-4 text-[#842529]" />
                <span>Bayar Cicilan Piutang</span>
            </h3>
            
            <form :action="'{{ url('/piutang/bayar') }}/' + selectedId" method="POST" class="space-y-3">
                @csrf
                <div class="bg-rose-50 border border-rose-200 p-3 rounded-xl flex justify-between items-center text-xs">
                    <span class="font-semibold text-rose-800">Sisa Tagihan:</span>
                    <span class="font-extrabold text-rose-700 text-sm">Rp <span x-text="sisaTagihan.toLocaleString('id-ID')"></span></span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nominal Pembayaran (Rp)</label>
                    <input type="number" name="nominal_bayar" min="1" :max="maxBayar" 
                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" 
                           placeholder="Contoh: 50000" required>
                </div>

                <div class="pt-3 flex gap-2">
                    <button type="button" @click="showForm = false" class="flex-1 border border-border-muted text-gray-700 py-2.5 rounded-xl font-bold text-xs hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 bg-[#842529] hover:bg-red-900 text-white py-2.5 rounded-xl font-bold text-xs shadow-md">Simpan Bayar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- LIST DATA PIUTANG -->
    <div class="space-y-3">
        @forelse($piutang as $p)
            <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm hover:border-gray-300 transition-all flex flex-col">
                <div class="flex justify-between items-start mb-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-[#842529] bg-[#FAE2E2] px-2.5 py-0.5 rounded-md inline-flex items-center gap-1">
                                <x-lucide-receipt-text class="w-3.5 h-3.5" />
                                {{ $p->id_pesanan }}
                            </span>
                            <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-md border {{ $p->status_piutang == 'Lunas' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                                {{ $p->status_piutang }}
                            </span>
                        </div>
                        <p class="text-sm font-bold text-gray-900 flex items-center gap-1.5 pt-1">
                            <x-lucide-user class="w-4 h-4 text-gray-400" />
                            <span>{{ $p->pesanan->pelanggan->nama_pelanggan ?? 'Unknown' }}</span>
                        </p>
                        <p class="text-xs text-gray-400 flex items-center gap-1">
                            <x-lucide-calendar-clock class="w-3.5 h-3.5 text-gray-400" />
                            <span>Jatuh Tempo: {{ date('d M Y', strtotime($p->tgl_jatuh_tempo)) }}</span>
                        </p>
                    </div>
                </div>

                <div class="bg-gray-50 border border-border-muted p-3 rounded-xl text-xs text-gray-600 flex justify-between items-center mt-1">
                    <div class="space-y-0.5">
                        <p class="text-gray-500">Total Tagihan: <span class="font-bold text-gray-800">Rp {{ number_format($p->total_tagihan, 0, ',', '.') }}</span></p>
                        <p class="text-gray-500">Jumlah Terbayar: <span class="font-bold text-emerald-700">Rp {{ number_format($p->jumlah_terbayar, 0, ',', '.') }}</span></p>
                    </div>
                    @if($p->status_piutang != 'Lunas')
                    <button @click="selectedId = '{{ $p->id_pesanan }}'; maxBayar = {{ $p->total_tagihan - $p->jumlah_terbayar }}; sisaTagihan = maxBayar; showForm = true;" 
                            class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-xl font-bold text-xs shadow-sm transition-all active:scale-95">
                        <x-lucide-banknote class="w-4 h-4 text-white" />
                        <span>Bayar</span>
                    </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center bg-white rounded-2xl border border-border-muted py-12 shadow-sm">
                <x-lucide-receipt-text class="w-10 h-10 text-gray-300 mx-auto mb-2" />
                <p class="text-gray-400 text-xs">Belum ada data piutang tempo.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection