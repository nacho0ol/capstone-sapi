@extends('layouts.app')

@section('content')
<div x-data="{ showForm: false }">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-bold text-gray-700 text-lg">Catatan Pengeluaran Harian</h2>
        <button @click="showForm = true" class="bg-red-600 text-white px-3 py-1.5 rounded text-xs font-bold shadow hover:bg-red-700">+ Tambah</button>
    </div>

    <!-- Modal Form Tambah Pengeluaran -->
    <div x-show="showForm" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center p-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-lg w-full max-w-md p-4 shadow-xl relative">
            <button @click="showForm = false" class="absolute top-3 right-3 text-gray-500 hover:text-red-500 font-bold text-xl">&times;</button>
            <h3 class="font-bold text-gray-800 mb-4 text-lg border-b pb-2">Catat Pengeluaran</h3>
            
            <form action="{{ route('pengeluaran.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">Tanggal</label>
                    <input type="date" name="tgl_pengeluaran" value="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
                </div>
                
                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">Kategori</label>
                    <select name="kategori_pengeluaran" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Makan">Makan</option>
                        <option value="Pembelian Daging">Pembelian Daging</option>
                        <option value="Operasional">Operasional</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">Nominal (Rp)</label>
                    <input type="number" name="nominal" min="1000" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" placeholder="Contoh: 50000" required>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="2" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" placeholder="Detail pengeluaran..." required></textarea>
                </div>

                <div class="pt-4 flex space-x-2">
                    <button type="button" @click="showForm = false" class="flex-1 border border-gray-300 text-gray-700 py-2 rounded font-semibold text-sm hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 bg-red-600 text-white py-2 rounded font-semibold text-sm hover:bg-red-700 shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($pengeluaran as $pen)
            <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm flex justify-between items-center">
                <div>
                    <span class="text-xs font-bold text-orange-600">{{ $pen->kategori_pengeluaran }}</span>
                    <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($pen->nominal, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">{{ $pen->keterangan }}</p>
                </div>
                <div class="text-right text-xs text-gray-400">
                    {{ $pen->tgl_pengeluaran }}
                </div>
            </div>
        @empty
            <p class="text-center text-gray-400 text-sm py-8">Belum ada catatan pengeluaran.</p>
        @endforelse
    </div>
</div>
@endsection