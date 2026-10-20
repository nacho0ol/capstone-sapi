@extends('layouts.app')

@section('content')
<div x-data="{ 
    items: {{ Js::from($pesanan->detail->map(function($d) { return ['id_produk' => $d->id_produk, 'qty' => $d->qty]; })) }}, 
    produks: {{ Js::from($produks) }} 
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
            <p class="text-xs text-gray-500 mt-0.5">Ubah detail transaksi, jadwal antar, dan daftar item pesanan</p>
        </div>
        <a href="{{ route('transaksi.index') }}" 
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-xl transition-all">
            <x-lucide-arrow-left class="w-4 h-4" />
            <span>Kembali</span>
        </a>
    </div>

    <!-- Form Edit Pesanan -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-border-muted">
        <form action="{{ route('transaksi.update', $pesanan->id_pesanan) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Order</label>
                    <input type="date" name="tgl_order" value="{{ $pesanan->tgl_order }}" 
                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Antar</label>
                    <input type="date" name="tgl_antar" value="{{ $pesanan->tgl_antar }}" 
                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Pelanggan</label>
                <select name="id_pelanggan" 
                        class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach($pelanggans as $plg)
                        <option value="{{ $plg->id_pelanggan }}" {{ $pesanan->id_pelanggan == $plg->id_pelanggan ? 'selected' : '' }}>
                            {{ $plg->nama_pelanggan }} - {{ $plg->tipe_pelanggan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Metode Bayar</label>
                <select name="metode_bayar" 
                        class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                    <option value="Tunai" {{ $pesanan->metode_bayar == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="Transfer" {{ $pesanan->metode_bayar == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="Tempo" {{ $pesanan->metode_bayar == 'Tempo' ? 'selected' : '' }}>Tempo (Piutang)</option>
                </select>
                
                @if($pesanan->metode_bayar == 'Tempo' && $pesanan->piutang)
                    <div class="mt-2.5 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 space-y-1">
                        <div class="flex items-center gap-1.5 font-bold text-amber-800">
                            <x-lucide-info class="w-4 h-4 text-amber-600 flex-shrink-0" />
                            <span>Informasi Status Piutang</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-1 text-[11px]">
                            <div><span class="text-amber-700 font-medium">Status:</span> <strong>{{ $pesanan->piutang->status_piutang }}</strong></div>
                            <div><span class="text-amber-700 font-medium">Telah Dibayar:</span> <strong class="text-emerald-700">Rp {{ number_format($pesanan->piutang->jumlah_terbayar, 0, ',', '.') }}</strong></div>
                            <div><span class="text-amber-700 font-medium">Sisa Kurangan:</span> <strong class="text-rose-700">Rp {{ number_format($pesanan->piutang->total_tagihan - $pesanan->piutang->jumlah_terbayar, 0, ',', '.') }}</strong></div>
                        </div>
                    </div>
                @endif
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
                                    <option :value="p.id_produk" x-text="p.nama_produk"></option>
                                </template>
                            </select>

                            <div class="w-24">
                                <input type="number" step="0.01" :name="'items['+index+'][qty]'" x-model="item.qty" 
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