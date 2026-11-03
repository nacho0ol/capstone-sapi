@extends('layouts.app')

@section('content')
@php
    $defaultItems = $pesanan->detail->map(function($d) {
        return ['id_produk' => (string) $d->id_produk, 'qty' => (float) $d->qty];
    })->values()->all();
@endphp
<div x-data="{ 
    selectedPelanggan: '{{ old('id_pelanggan', $pesanan->id_pelanggan) }}',
    metodeBayar: '{{ old('metode_bayar', $pesanan->metode_bayar) }}',
    items: {{ Js::from(old('items', $defaultItems)) }}, 
    produks: {{ Js::from($produks) }},
    pelanggans: {{ Js::from($pelanggans) }},
    get currentNoTelp() {
        const found = this.pelanggans.find(p => String(p.id_pelanggan) === String(this.selectedPelanggan));
        return found ? found.no_telp : '{{ $pesanan->pelanggan->no_telp ?? '-' }}';
    }
}" class="space-y-4">

    <!-- Header Halaman -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                <span>Edit Pesanan</span>
                <span class="text-xs bg-[#FAE2E2] text-[#842529] px-2.5 py-0.5 rounded-md font-extrabold border border-[#842529]/10">
                    #{{ $pesanan->id_pesanan }}
                </span>
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">Ubah detail pesanan, jadwal antar, dan daftar item pesanan</p>
        </div>
        <a href="{{ route('transaksi.index') }}" 
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-xl transition-all">
            <x-lucide-arrow-left class="w-4 h-4" />
            <span>Kembali</span>
        </a>
    </div>

    <!-- Form Edit Pesanan -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-border-muted">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-3.5 py-2.5 rounded-xl text-xs mb-4 space-y-1">
                <div class="font-bold flex items-center gap-1.5">
                    <x-lucide-alert-circle class="w-4 h-4 text-red-600 flex-shrink-0" />
                    <span>Gagal Memperbarui Pesanan:</span>
                </div>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('transaksi.update', $pesanan->id_pesanan) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Order</label>
                    <input type="date" name="tgl_order" min="2015-01-01" value="{{ old('tgl_order', $pesanan->tgl_order) }}" 
                           class="w-full text-xs bg-gray-50 border {{ $errors->has('tgl_order') ? 'border-red-500' : 'border-border-muted' }} p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                    @error('tgl_order')
                        <p class="text-[10px] text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Antar</label>
                    <input type="date" name="tgl_antar" min="2015-01-01" value="{{ old('tgl_antar', $pesanan->tgl_antar) }}" 
                           class="w-full text-xs bg-gray-50 border {{ $errors->has('tgl_antar') ? 'border-red-500' : 'border-border-muted' }} p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                    @error('tgl_antar')
                        <p class="text-[10px] text-red-600 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Pelanggan</label>
                    <select name="id_pelanggan" x-model="selectedPelanggan"
                            class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        @foreach($pelanggans as $plg)
                            <option value="{{ $plg->id_pelanggan }}" {{ old('id_pelanggan', $pesanan->id_pelanggan) == $plg->id_pelanggan ? 'selected' : '' }}>
                                {{ $plg->nama_pelanggan }} - {{ $plg->no_telp }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor Telepon Pelanggan</label>
                    <input type="text" :value="currentNoTelp" readonly
                           class="w-full text-xs bg-gray-100 border border-border-muted p-2.5 rounded-xl text-gray-700 font-medium cursor-not-allowed">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Metode Bayar</label>
                <select name="metode_bayar" x-model="metodeBayar"
                        class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                    <option value="Tunai" {{ old('metode_bayar', $pesanan->metode_bayar) == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="Transfer" {{ old('metode_bayar', $pesanan->metode_bayar) == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="Tempo" {{ old('metode_bayar', $pesanan->metode_bayar) == 'Tempo' ? 'selected' : '' }}>Tempo (Piutang)</option>
                </select>

                <!-- Box Input Tempo & Piutang -->
                <div x-show="metodeBayar === 'Tempo'" x-cloak class="mt-2.5 bg-sky-50 border border-sky-200 p-3 rounded-xl space-y-2" x-transition>
                    <h4 class="text-[11px] font-bold text-sky-900 uppercase tracking-wider flex items-center gap-1">
                        <x-lucide-receipt class="w-3.5 h-3.5 text-sky-700" />
                        <span>Data Piutang & Jatuh Tempo</span>
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] text-sky-800 font-semibold mb-1">Tanggal Jatuh Tempo</label>
                            <input type="date" name="tgl_jatuh_tempo" min="2015-01-01"
                                   value="{{ old('tgl_jatuh_tempo', $pesanan->piutang->tgl_jatuh_tempo ?? date('Y-m-d', strtotime($pesanan->tgl_order . ' +7 days'))) }}" 
                                   class="w-full text-xs bg-white border border-sky-300 rounded-xl p-2 focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-[10px] text-sky-800 font-semibold mb-1">DP / Telah Dibayar (Rp)</label>
                            <input type="number" name="jumlah_terbayar" min="0"
                                   value="{{ old('jumlah_terbayar', $pesanan->piutang->jumlah_terbayar ?? 0) }}" 
                                   class="w-full text-xs bg-white border border-sky-300 rounded-xl p-2 focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                    </div>
                </div>
                
                @if($pesanan->metode_bayar == 'Tempo' && $pesanan->piutang)
                    <div class="mt-2.5 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 space-y-1">
                        <div class="flex items-center gap-1.5 font-bold text-amber-800">
                            <x-lucide-info class="w-4 h-4 text-amber-600 flex-shrink-0" />
                            <span>Informasi Status Piutang Saat Ini</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1 text-[11px]">
                            <div><span class="text-amber-700 font-medium">Status:</span> <strong>{{ $pesanan->piutang->status_piutang }}</strong></div>
                            <div><span class="text-amber-700 font-medium">Telah Dibayar:</span> <strong class="text-emerald-700">Rp {{ number_format($pesanan->piutang->jumlah_terbayar, 0, ',', '.') }}</strong></div>
                            <div><span class="text-amber-700 font-medium">Sisa Kurangan:</span> <strong class="text-rose-700">Rp {{ number_format($pesanan->piutang->total_tagihan - $pesanan->piutang->jumlah_terbayar, 0, ',', '.') }}</strong></div>
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan (Opsional)</label>
                <textarea name="catatan" rows="2" 
                          class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" 
                          placeholder="Contoh: Potong dadu kecil-kecil...">{{ old('catatan', $pesanan->catatan) }}</textarea>
            </div>

            <!-- Item Produk Section -->
            <div class="border-t border-border-muted pt-4 mt-4 space-y-3">
                <div class="flex justify-between items-center">
                    <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                        <x-lucide-package class="w-4 h-4 text-[#842529]" />
                        <span>Item Produk Pesanan</span>
                    </label>
                </div>
                
                <div class="space-y-2">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex items-center gap-2 bg-gray-50 p-2.5 rounded-xl border border-border-muted">
                            <select :name="'items['+index+'][id_produk]'" x-model="item.id_produk" 
                                    class="flex-1 text-xs bg-white border border-border-muted p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#842529]" required>
                                <option value="">Pilih Produk</option>
                                <template x-for="p in produks" :key="p.id_produk">
                                    <option :value="String(p.id_produk)" :selected="String(item.id_produk) === String(p.id_produk)" x-text="p.nama_produk + ' (Rp ' + Number(p.harga_jual).toLocaleString('id-ID') + '/kg)'"></option>
                                </template>
                            </select>

                            <div class="w-24">
                                <input type="number" step="0.01" min="0.01" :name="'items['+index+'][qty]'" x-model="item.qty" 
                                       class="w-full text-xs bg-white border border-border-muted p-2 rounded-lg text-center focus:outline-none focus:ring-2 focus:ring-[#842529]" placeholder="Qty" required>
                            </div>

                            <button type="button" @click="items.splice(index, 1)" x-show="items.length > 1" 
                                    class="p-2 text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 rounded-lg border border-rose-200 transition-colors flex-shrink-0"
                                    title="Hapus Item">
                                <x-lucide-trash-2 class="w-4 h-4" />
                            </button>
                        </div>
                    </template>
                </div>

                <button type="button" @click="items.push({id_produk: '', qty: 1})" 
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-[#842529] bg-[#FAE2E2]/60 hover:bg-[#FAE2E2] px-3.5 py-2 rounded-xl transition-all border border-[#842529]/10">
                    <x-lucide-plus class="w-4 h-4" />
                    <span>Tambah Item Produk</span>
                </button>
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-4 flex gap-2 border-t border-border-muted">
                <a href="{{ route('transaksi.index') }}" 
                   class="flex-1 text-center border border-border-muted text-gray-700 py-2.5 rounded-xl font-bold text-xs hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="flex-1 bg-[#842529] hover:bg-red-900 text-white py-2.5 rounded-xl font-bold text-xs shadow-md transition-all active:scale-95 inline-flex items-center justify-center gap-1.5">
                    <x-lucide-save class="w-4 h-4" />
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
