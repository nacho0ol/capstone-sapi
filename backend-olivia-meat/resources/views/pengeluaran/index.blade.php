@extends('layouts.app')

@section('content')
<div x-data="{ 
    showForm: false, 
    showEdit: false, 
    editData: { id_pengeluaran: '', tgl_pengeluaran: '', kategori_pengeluaran: '', nominal: '', keterangan: '' } 
}" class="space-y-4">

    <!-- Header Halaman -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Catatan Pengeluaran Harian</h1>
            <p class="text-xs text-gray-500 mt-0.5">Pencatatan dan pemantauan pengeluaran operasional toko</p>
        </div>
        <button @click="showForm = true" 
                class="inline-flex items-center gap-1.5 bg-[#842529] hover:bg-red-900 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow-sm transition-all active:scale-95">
            <x-lucide-plus-circle class="w-4 h-4 text-white" />
            <span>Tambah</span>
        </button>
    </div>

    <!-- Flash Message Success -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-3.5 py-2.5 rounded-xl text-xs font-medium flex items-center gap-2 shadow-sm">
            <x-lucide-check-circle-2 class="w-4 h-4 text-emerald-600 flex-shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- MODAL TAMBAH PENGELUARAN -->
    <div x-show="showForm" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex justify-center items-center p-4" style="display: none;">
        <div @click.away="showForm = false" class="bg-white rounded-2xl w-full max-w-md p-5 shadow-xl relative border border-border-muted">
            <button @click="showForm = false" class="absolute top-4 right-4 text-gray-400 hover:text-[#842529] p-1 rounded-lg transition-colors">
                <x-lucide-x class="w-5 h-5" />
            </button>
            <h3 class="font-bold text-gray-900 text-sm mb-4 border-b border-border-muted pb-3 flex items-center gap-2">
                <x-lucide-wallet class="w-4 h-4 text-[#842529]" />
                <span>Catat Pengeluaran Baru</span>
            </h3>
            
            <form action="{{ url('/pengeluaran/store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tgl_pengeluaran" value="{{ date('Y-m-d') }}" 
                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                    <select name="kategori_pengeluaran" 
                            class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Makan">Makan</option>
                        <option value="Pembelian Daging">Pembelian Daging</option>
                        <option value="Operasional">Operasional</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nominal (Rp)</label>
                    <input type="number" name="nominal" min="1000" 
                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" placeholder="Contoh: 50000" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="2" 
                              class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" placeholder="Catatan singkat pengeluaran..." required></textarea>
                </div>
                <div class="pt-3 flex gap-2">
                    <button type="button" @click="showForm = false" class="flex-1 border border-border-muted text-gray-700 py-2.5 rounded-xl font-bold text-xs hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 bg-[#842529] hover:bg-red-900 text-white py-2.5 rounded-xl font-bold text-xs shadow-md">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PENGELUARAN -->
    <div x-show="showEdit" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex justify-center items-center p-4" style="display: none;">
        <div @click.away="showEdit = false" class="bg-white rounded-2xl w-full max-w-md p-5 shadow-xl relative border border-border-muted">
            <button @click="showEdit = false" class="absolute top-4 right-4 text-gray-400 hover:text-[#842529] p-1 rounded-lg transition-colors">
                <x-lucide-x class="w-5 h-5" />
            </button>
            <h3 class="font-bold text-gray-900 text-sm mb-4 border-b border-border-muted pb-3 flex items-center gap-2">
                <x-lucide-square-pen class="w-4 h-4 text-amber-600" />
                <span>Edit Pengeluaran</span>
            </h3>
            
            <form :action="'{{ url('/pengeluaran') }}/' + editData.id_pengeluaran" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tgl_pengeluaran" x-model="editData.tgl_pengeluaran" 
                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                    <select name="kategori_pengeluaran" x-model="editData.kategori_pengeluaran" 
                            class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                        <option value="Makan">Makan</option>
                        <option value="Pembelian Daging">Pembelian Daging</option>
                        <option value="Operasional">Operasional</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Lain-lain">Lain-lain</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nominal (Rp)</label>
                    <input type="number" name="nominal" x-model="editData.nominal" min="1000" 
                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" x-model="editData.keterangan" rows="2" 
                              class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required></textarea>
                </div>
                <div class="pt-3 flex gap-2">
                    <button type="button" @click="showEdit = false" class="flex-1 border border-border-muted text-gray-700 py-2.5 rounded-xl font-bold text-xs hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 bg-[#842529] hover:bg-red-900 text-white py-2.5 rounded-xl font-bold text-xs shadow-md">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- LIST CATATAN PENGELUARAN -->
    <div class="space-y-3">
        @forelse($pengeluaran as $pen)
            <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm hover:border-gray-300 transition-all flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <span class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-md inline-block mb-1">
                            {{ $pen->kategori_pengeluaran }}
                        </span>
                        <p class="text-lg font-bold text-gray-900">Rp {{ number_format($pen->nominal, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $pen->keterangan }}</p>
                    </div>
                    <div class="text-right text-xs text-gray-400 font-semibold flex items-center gap-1">
                        <x-lucide-calendar class="w-3.5 h-3.5 text-gray-400" />
                        <span>{{ date('d M Y', strtotime($pen->tgl_pengeluaran)) }}</span>
                    </div>
                </div>
                
                <div class="flex justify-end gap-2 border-t border-border-muted pt-3 mt-1">
                    <button @click="editData = {{ json_encode($pen) }}; showEdit = true;" 
                            class="inline-flex items-center gap-1 text-amber-700 font-semibold px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition-colors text-xs">
                        <x-lucide-square-pen class="w-3.5 h-3.5" />
                        <span>Edit</span>
                    </button>
                    
                    <form action="{{ route('pengeluaran.destroy', $pen->id_pengeluaran) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus catatan pengeluaran ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center gap-1 text-rose-700 font-semibold px-3 py-1.5 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition-colors text-xs">
                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                            <span>Hapus</span>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center bg-white rounded-2xl border border-border-muted py-12 shadow-sm">
                <x-lucide-wallet class="w-10 h-10 text-gray-300 mx-auto mb-2" />
                <p class="text-gray-400 text-xs">Belum ada catatan pengeluaran.</p>
                <button @click="showForm = true" class="mt-2 text-[#842529] font-bold text-xs hover:underline">Tambah Catatan Pertama</button>
            </div>
        @endforelse
    </div>
</div>
@endsection