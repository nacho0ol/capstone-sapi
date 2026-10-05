@extends('layouts.app')

@section('content')
<div x-data="{ showForm: false, selectedPelanggan: '', showPelangganForm: false, items: [{id_produk: '', qty: 1}], produks: {{ Js::from($produks) }}, pelanggans: {{ Js::from($pelanggans) }}, newPelanggan: {nama_pelanggan: '', no_telp: '', alamat: ''} }">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-bold text-gray-700 text-lg">Daftar Pesanan</h2>
        <button @click="showForm = true" class="bg-red-600 text-white px-3 py-1.5 rounded text-xs font-bold shadow hover:bg-red-700">+ Tambah</button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4 text-sm font-semibold border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 text-red-600 p-3 rounded mb-4 text-sm font-semibold border border-red-200">
            <strong>Error:</strong> {{ session('error') }}
        </div>
    @endif

    <!-- Modal Form Tambah Pesanan -->
    <div x-show="showForm" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center p-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-lg w-full max-w-md max-h-[90vh] overflow-y-auto p-4 shadow-xl relative">
            <button @click="showForm = false" class="absolute top-3 right-3 text-gray-500 hover:text-red-500 font-bold text-xl">&times;</button>
            <h3 class="font-bold text-gray-800 mb-4 text-lg border-b pb-2">Buat Pesanan Baru</h3>
            
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded text-[11px] mb-3">
                    <strong>Gagal menyimpan pesanan:</strong>
                    <ul class="list-disc pl-4 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('transaksi.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="id_user" value="1"> 
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-gray-500 font-semibold mb-1">Tanggal Order</label>
                        <input type="date" name="tgl_order" value="{{ old('tgl_order', date('Y-m-d')) }}" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 font-semibold mb-1">Tanggal Antar</label>
                        <input type="date" name="tgl_antar" value="{{ old('tgl_antar') }}" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">Pelanggan</label>
                    <select name="id_pelanggan" x-model="selectedPelanggan" @change="if(selectedPelanggan === 'new') { showPelangganForm = true; selectedPelanggan = ''; }" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <template x-for="plg in pelanggans" :key="plg.id_pelanggan">
                            <!-- PERBAIKAN UNDEFINED: Pakai no_telp -->
                            <option :value="plg.id_pelanggan" x-text="plg.nama_pelanggan + ' - ' + plg.no_telp"></option>
                        </template>
                        <option value="new" class="font-bold text-red-600">+ Tambah Pelanggan Baru</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">Metode Bayar</label>
                    <select name="metode_bayar" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
                        <option value="Tunai" {{ old('metode_bayar') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="Transfer" {{ old('metode_bayar') == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                        <option value="Tempo" {{ old('metode_bayar') == 'Tempo' ? 'selected' : '' }}>Tempo (Piutang)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">Catatan (Opsional)</label>
                    <textarea name="catatan" rows="2" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" placeholder="Contoh: Tolong potong dadu kecil-kecil...">{{ old('catatan') }}</textarea>
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
                    <button type="button" @click="showForm = false" class="flex-1 border border-gray-300 text-gray-700 py-2 rounded font-semibold text-sm hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 bg-red-600 text-white py-2 rounded font-semibold text-sm hover:bg-red-700 shadow">Simpan Pesanan</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showPelangganForm" class="fixed inset-0 bg-black bg-opacity-60 z-[60] flex justify-center items-center p-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-lg w-full max-w-sm p-4 shadow-xl relative">
            <button @click="showPelangganForm = false" class="absolute top-3 right-3 text-gray-500 hover:text-red-500 font-bold text-xl">&times;</button>
            <h3 class="font-bold text-gray-800 mb-4 text-base border-b pb-2">Tambah Pelanggan Baru</h3>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">Nama Pelanggan</label>
                    <input type="text" x-model="newPelanggan.nama_pelanggan" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" placeholder="Cth: Budi" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">No. Telp</label>
                    <input type="text" x-model="newPelanggan.no_telp" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" placeholder="08..." required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">Alamat</label>
                    <textarea x-model="newPelanggan.alamat" rows="2" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required></textarea>
                </div>

                <div class="pt-4 flex space-x-2">
                    <button type="button" @click="showPelangganForm = false" class="flex-1 border border-gray-300 text-gray-700 py-2 rounded font-semibold text-sm hover:bg-gray-50">Batal</button>
                    <button type="button" @click="
                        fetch('{{ route('master.pelanggan.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(newPelanggan)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if(data.data) {
                                pelanggans.push(data.data);
                                selectedPelanggan = data.data.id_pelanggan;
                                showPelangganForm = false;
                                newPelanggan = {nama_pelanggan: '', no_telp: '', alamat: ''};
                                alert('Pelanggan berhasil ditambahkan!');
                            } else {
                                alert(data.message || 'Gagal menyimpan pelanggan, cek validasi.');
                            }
                        })
                        .catch(err => alert('Terjadi kesalahan koneksi!'));
                    " class="flex-1 bg-green-600 text-white py-2 rounded font-semibold text-sm hover:bg-green-700 shadow">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($pesanan as $p)
            <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm flex justify-between items-center hover:bg-gray-50 transition-colors">
                <a href="{{ route('transaksi.edit', $p->id_pesanan) }}" class="flex-1 block">
                    <span class="text-xs font-bold text-red-600">{{ $p->id_pesanan }}</span>
                    <span class="ml-2 text-[10px] font-bold px-2 py-0.5 rounded {{ $p->status_pemesanan == 'Diterima' ? 'bg-blue-100 text-blue-700' : ($p->status_pemesanan == 'Selesai' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700') }}">
                        {{ $p->status_pemesanan }}
                    </span>
                    <p class="text-sm font-semibold text-gray-800 mt-1">{{ $p->pelanggan->nama_pelanggan ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-500">Antar: {{ $p->tgl_antar }} | Metode: {{ $p->metode_bayar }}</p>
                    
                    @if($p->catatan)
                        <p class="text-[10px] italic text-orange-600 mt-0.5">Catatan: {{ $p->catatan }}</p>
                    @endif

                    <div class="text-[10px] text-gray-400 mt-1.5 pt-1.5 border-t border-gray-100">
                        @foreach($p->detail as $det)
                            {{ $det->produk->nama_produk ?? 'Unknown' }} ({{ $det->qty }}kg)@if(!$loop->last), @endif
                        @endforeach
                    </div>
                </a>
                <div class="text-right flex flex-col items-end justify-between h-full space-y-2 ml-2 border-l pl-2 min-w-[70px]">
                    <span class="text-[10px] font-bold px-2 py-1 rounded w-full text-center {{ $p->status_bayar == 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ $p->status_bayar }}
                    </span>
                    <form action="{{ route('transaksi.cancel', $p->id_pesanan) }}" method="POST" onsubmit="return confirm('Batalkan pesanan ini?');">
                        @csrf
                        <button type="submit" class="text-[10px] bg-red-50 text-red-600 hover:bg-red-100 px-2 py-1.5 rounded font-semibold w-full text-center border border-red-200">Batal</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center bg-white rounded-lg border py-10 shadow-sm">
                <p class="text-gray-400 text-sm">Belum ada data transaksi.</p>
                <button @click="showForm = true" class="mt-3 text-red-600 font-semibold text-sm hover:underline">Buat Pesanan Pertama</button>
            </div>
        @endforelse
    </div>
</div>
@endsection