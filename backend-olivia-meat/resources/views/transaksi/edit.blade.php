@extends('layouts.app')

@section('content')
<div x-data="{ 
    items: {{ Js::from($pesanan->detail->map(function($d) { return ['id_produk' => $d->id_produk, 'qty' => $d->qty]; })) }}, 
    produks: {{ Js::from($produks) }} 
}">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-bold text-gray-700 text-lg">Edit Pesanan <span class="text-red-600">#{{ $pesanan->id_pesanan }}</span></h2>
        <a href="{{ route('transaksi.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-800">Kembali</a>
    </div>

    <div class="bg-white rounded-lg w-full p-4 shadow-sm border border-gray-200">
        <form action="{{ route('transaksi.update', $pesanan->id_pesanan) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs text-gray-500 font-semibold mb-1">Tanggal Order</label>
                <input type="date" name="tgl_order" value="{{ $pesanan->tgl_order }}" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
            </div>

            <div>
                <label class="block text-xs text-gray-500 font-semibold mb-1">Tanggal Antar</label>
                <input type="date" name="tgl_antar" value="{{ $pesanan->tgl_antar }}" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
            </div>
            
            <div>
                <label class="block text-xs text-gray-500 font-semibold mb-1">Pelanggan</label>
                <select name="id_pelanggan" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach($pelanggans as $plg)
                        <option value="{{ $plg->id_pelanggan }}" {{ $pesanan->id_pelanggan == $plg->id_pelanggan ? 'selected' : '' }}>
                            {{ $plg->nama_pelanggan }} - {{ $plg->tipe_pelanggan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
        <label class="block text-xs text-gray-500 font-semibold mb-1">Metode Bayar</label>
        <select name="metode_bayar" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
            <option value="Tunai" {{ $pesanan->metode_bayar == 'Tunai' ? 'selected' : '' }}>Tunai</option>
            <option value="Transfer" {{ $pesanan->metode_bayar == 'Transfer' ? 'selected' : '' }}>Transfer</option>
            <option value="Tempo" {{ $pesanan->metode_bayar == 'Tempo' ? 'selected' : '' }}>Tempo (Piutang)</option>
    </select>
    
    @if($pesanan->metode_bayar == 'Tempo' && $pesanan->piutang)
        <div class="mt-2 p-2.5 bg-yellow-50 border border-yellow-200 rounded text-[11px] text-yellow-800">
            <strong>Status Piutang:</strong> {{ $pesanan->piutang->status_piutang }} <br>
            <strong>Telah Dibayar:</strong> Rp {{ number_format($pesanan->piutang->jumlah_terbayar, 0, ',', '.') }} <br>
            <strong>Sisa Kurangan:</strong> Rp {{ number_format($pesanan->piutang->total_tagihan - $pesanan->piutang->jumlah_terbayar, 0, ',', '.') }}
        </div>
    @endif
</div>

            <div class="border-t pt-3 mt-3">
                <label class="block text-xs text-gray-500 font-semibold mb-2">Item Produk</label>
                
                <template x-for="(item, index) in items" :key="index">
                    <div class="flex space-x-2 items-center mb-2">
                        <select :name="'items['+index+'][id_produk]'" x-model="item.id_produk" class="flex-1 border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
                            <option value="">Pilih Produk</option>
                            <template x-for="p in produks" :key="p.id_produk">
                                <option :value="p.id_produk" x-text="p.nama_produk"></option>
                            </template>
                        </select>
                        <input type="number" step="0.01" :name="'items['+index+'][qty]'" x-model="item.qty" class="w-20 border border-gray-300 rounded p-2 text-sm text-center focus:outline-none focus:border-red-500" placeholder="Qty" required>
                        <button type="button" @click="items.splice(index, 1)" x-show="items.length > 1" class="text-red-500 font-bold bg-red-50 px-2 py-1 rounded">X</button>
                    </div>
                </template>
                <button type="button" @click="items.push({id_produk: '', qty: 1})" class="text-xs font-bold text-red-600 bg-red-50 px-3 py-1.5 rounded mt-1 hover:bg-red-100">+ Tambah Item</button>
            </div>

            <div class="pt-4 flex space-x-2">
                <a href="{{ route('transaksi.index') }}" class="flex-1 block text-center border border-gray-300 text-gray-700 py-2 rounded font-semibold text-sm hover:bg-gray-50">Batal</a>
                <button type="submit" class="flex-1 bg-red-600 text-white py-2 rounded font-semibold text-sm hover:bg-red-700 shadow">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
