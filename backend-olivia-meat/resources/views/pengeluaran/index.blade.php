@extends('layouts.app')

@section('content')
<div x-data="{ 
    showForm: false, 
    showEdit: false, 
    editData: { id_pengeluaran: '', tgl_pengeluaran: '', kategori_pengeluaran: '', nominal: '', keterangan: '' } 
}">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-bold text-gray-700 text-lg">Catatan Pengeluaran Harian</h2>
        <button @click="showForm = true" class="bg-red-600 text-white px-3 py-1.5 rounded text-xs font-bold shadow hover:bg-red-700">+ Tambah</button>
    </div>

    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-3 rounded mb-4 text-sm font-semibold border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div x-show="showForm" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center p-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-lg w-full max-w-md p-4 shadow-xl relative">
            <button @click="showForm = false" class="absolute top-3 right-3 text-gray-500 hover:text-red-500 font-bold text-xl">&times;</button>
            <h3 class="font-bold text-gray-800 mb-4 text-lg border-b pb-2">Catat Pengeluaran</h3>
            
            <form action="{{ url('/pengeluaran/store') }}" method="POST" class="space-y-3">
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
                    <textarea name="keterangan" rows="2" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required></textarea>
                </div>
                <div class="pt-4 flex space-x-2">
                    <button type="button" @click="showForm = false" class="flex-1 border border-gray-300 text-gray-700 py-2 rounded font-semibold text-sm hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 bg-red-600 text-white py-2 rounded font-semibold text-sm hover:bg-red-700 shadow">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PENGELUARAN -->
    <div x-show="showEdit" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center p-4" style="display: none;" x-cloak>
        <div class="bg-white rounded-lg w-full max-w-md p-4 shadow-xl relative">
            <button @click="showEdit = false" class="absolute top-3 right-3 text-gray-500 hover:text-red-500 font-bold text-xl">&times;</button>
            <h3 class="font-bold text-gray-800 mb-4 text-lg border-b pb-2">Edit Pengeluaran</h3>
            
            <form :action="'{{ url('/pengeluaran') }}/' + editData.id_pengeluaran" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">Tanggal</label>
                    <input type="date" name="tgl_pengeluaran" x-model="editData.tgl_pengeluaran" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">Kategori</label>
                    <select name="kategori_pengeluaran" x-model="editData.kategori_pengeluaran" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
                        <option value="Makan">Makan</option>
                        <option value="Pembelian Daging">Pembelian Daging</option>
                        <option value="Operasional">Operasional</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">Nominal (Rp)</label>
                    <input type="number" name="nominal" x-model="editData.nominal" min="1000" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 font-semibold mb-1">Keterangan</label>
                    <textarea name="keterangan" x-model="editData.keterangan" rows="2" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required></textarea>
                </div>
                <div class="pt-4 flex space-x-2">
                    <button type="button" @click="showEdit = false" class="flex-1 border border-gray-300 text-gray-700 py-2 rounded font-semibold text-sm hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 bg-yellow-500 text-white py-2 rounded font-semibold text-sm hover:bg-yellow-600 shadow">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-3">
        @forelse($pengeluaran as $pen)
            <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm flex flex-col hover:bg-gray-50 transition-colors">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="text-xs font-bold text-orange-600">{{ $pen->kategori_pengeluaran }}</span>
                        <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($pen->nominal, 0, ',', '.') }}</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $pen->keterangan }}</p>
                    </div>
                    <div class="text-right text-xs text-gray-400 font-semibold">
                        {{ date('d M Y', strtotime($pen->tgl_pengeluaran)) }}
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2 border-t pt-2 mt-1">
                    <button @click="editData = {{ json_encode($pen) }}; showEdit = true;" class="text-[10px] bg-yellow-50 text-yellow-700 border border-yellow-200 px-3 py-1 rounded hover:bg-yellow-100 font-bold">
                        Edit
                    </button>
                    
                    <form action="{{ route('pengeluaran.destroy', $pen->id_pengeluaran) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus catatan pengeluaran ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-[10px] bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded hover:bg-red-100 font-bold">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center bg-white rounded-lg border py-10 shadow-sm">
                <p class="text-gray-400 text-sm">Belum ada catatan pengeluaran.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection