@extends('layouts.app')

@section('content')
<div x-data="{ 
    //  Otomatis ngebuka modal kalau ada deteksi error dari controller 👇
    showForm: {{ session('error_modal_id') ? 'true' : 'false' }}, 
    selectedId: '{{ session('error_modal_id', '') }}', 
    maxBayar: {{ session('error_modal_sisa', 0) }}, 
    sisaTagihan: {{ session('error_modal_sisa', 0) }},
    minBayar: {{ session('error_modal_sisa', 0) > 0 && session('error_modal_sisa', 0) < 1000 ? session('error_modal_sisa') : 1000 }},

    // Mengambil nominal lama yang bikin error biar nggak hilang
    nominalRaw: '{{ old('nominal_bayar', '') }}',
    nominalFormatted: '',

    init() {
        // Otomatis ubah angka lama jadi format Rupiah saat halaman ke-refresh gara-gara error
        if (this.nominalRaw) {
            this.nominalFormatted = new Intl.NumberFormat('id-ID').format(this.nominalRaw);
        }
    },

    formatRupiah(val) {
        let number = String(val).replace(/\D/g, '');
        if (!number) return { raw: '', formatted: '' };
        let formatted = new Intl.NumberFormat('id-ID').format(number);
        return { raw: number, formatted: formatted };
    },

    onNominalInput(e) {
        let res = this.formatRupiah(e.target.value);
        this.nominalRaw = res.raw;
        this.nominalFormatted = res.formatted;
    },

    openBayarModal(id, sisa) {
        this.selectedId = id;
        this.sisaTagihan = sisa;
        this.maxBayar = sisa;
        this.minBayar = sisa < 1000 ? sisa : 1000; 
        
        this.nominalRaw = '';
        this.nominalFormatted = '';
        this.showForm = true;
    }
}" class="space-y-4">
    
    <!-- Header Halaman -->
    <div class="flex justify-between items-center mb-2">
        <div class="flex items-center gap-3">
            <a href="{{ route('keuangan.index') }}" 
               class="p-2 bg-white border border-border-muted rounded-xl text-gray-600 hover:text-[#842529] hover:border-[#842529] hover:bg-gray-50 transition-all shadow-sm">
                <x-lucide-arrow-left class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Monitoring Piutang Pelanggan</h1>
                <p class="text-xs text-gray-500 mt-0.5">Pantau status tagihan tempo dan pencatatan cicilan</p>
            </div>
        </div>
    </div>

    <!-- ALERT PESAN SUKSES / ERROR SISTEM -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-xs font-bold flex items-center gap-2">
            <x-lucide-check-circle class="w-4 h-4" />
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-xs font-bold flex items-center gap-2">
            <x-lucide-alert-circle class="w-4 h-4 shrink-0" />
            {{ session('error') }}
        </div>
    @endif

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
                    <input type="text" 
                           x-model="nominalFormatted"
                           @input="onNominalInput($event)"
                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all" 
                           placeholder="Contoh: 50.000" required>
                    
                    @error('nominal_bayar')
                        <p class="text-[11px] text-rose-600 mt-1.5 font-bold flex items-center gap-1">
                            <x-lucide-alert-circle class="w-3.5 h-3.5" />
                            {{ $message }}
                        </p>
                    @enderror
                    
                    <p class="text-[10px] text-gray-500 mt-1.5 italic">
                        *Minimal bayar: <span class="font-bold text-gray-700">Rp <span x-text="minBayar.toLocaleString('id-ID')"></span></span> | 
                        Maksimal: <span class="font-bold text-gray-700">Rp <span x-text="sisaTagihan.toLocaleString('id-ID')"></span></span>
                    </p>
                    
                    <input type="hidden" name="nominal_bayar" :value="nominalRaw">
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
                    <button @click="openBayarModal('{{ $p->id_pesanan }}', {{ $p->total_tagihan - $p->jumlah_terbayar }})" 
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