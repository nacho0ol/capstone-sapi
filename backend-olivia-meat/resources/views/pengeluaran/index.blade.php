@extends('layouts.app')

@section('content')
<div x-data="{ 
    showForm: {{ $errors->any() && !old('id_pengeluaran') ? 'true' : 'false' }}, 
    showEdit: {{ $errors->any() && old('id_pengeluaran') ? 'true' : 'false' }},
    
    // State Nominal Tambah
    nominalRaw: '',
    nominalFormatted: '',

    // State Nominal Edit
    editData: { id_pengeluaran: '', tgl_pengeluaran: '', kategori_pengeluaran: '', nominal: '', keterangan: '' },
    editNominalRaw: '',
    editNominalFormatted: '',

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

    onEditNominalInput(e) {
        let res = this.formatRupiah(e.target.value);
        this.editNominalRaw = res.raw;
        this.editNominalFormatted = res.formatted;
    },

    openEditModal(pen) {
        this.editData = pen;
        let res = this.formatRupiah(pen.nominal);
        this.editNominalRaw = res.raw;
        this.editNominalFormatted = res.formatted;
        this.showEdit = true;
    }
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
    <div x-show="showForm" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex justify-center items-center p-4">
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
                
                <!-- KOTAK ERROR DI ATAS FORM TAMBAH -->
                @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 px-3 py-2 rounded-xl text-xs mb-2">
                        <ul class="list-disc pl-4 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tgl_pengeluaran" value="{{ old('tgl_pengeluaran', date('Y-m-d')) }}" 
                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                    <select name="kategori_pengeluaran" 
                            class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Makan" {{ old('kategori_pengeluaran') == 'Makan' ? 'selected' : '' }}>Makan</option>
                        <option value="Pembelian Daging" {{ old('kategori_pengeluaran') == 'Pembelian Daging' ? 'selected' : '' }}>Pembelian Daging</option>
                        <option value="Operasional" {{ old('kategori_pengeluaran') == 'Operasional' ? 'selected' : '' }}>Operasional</option>
                        <option value="Transportasi" {{ old('kategori_pengeluaran') == 'Transportasi' ? 'selected' : '' }}>Transportasi</option>
                        <option value="Lain-lain" {{ old('kategori_pengeluaran') == 'Lain-lain' ? 'selected' : '' }}>Lain-lain</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nominal (Rp)</label>
                    <input type="text" 
                           x-model="nominalFormatted"
                           @input="onNominalInput($event)"
                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" 
                           placeholder="Contoh: 50.000" required>
                    <input type="hidden" name="nominal" :value="nominalRaw">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="2" 
                              class="w-full text-xs bg-gray-50 border {{ $errors->has('keterangan') ? 'border-red-500' : 'border-border-muted' }} p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" placeholder="Catatan singkat pengeluaran..." required>{{ old('keterangan') }}</textarea>
                </div>
                <div class="pt-3 flex gap-2">
                    <button type="button" @click="showForm = false" class="flex-1 border border-border-muted text-gray-700 py-2.5 rounded-xl font-bold text-xs hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 bg-[#842529] hover:bg-red-900 text-white py-2.5 rounded-xl font-bold text-xs shadow-md">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PENGELUARAN -->
    <div x-show="showEdit" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex justify-center items-center p-4">
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
                <input type="hidden" name="id_pengeluaran" :value="editData.id_pengeluaran">
                
                <!-- KOTAK ERROR DI ATAS FORM EDIT -->
                @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 px-3 py-2 rounded-xl text-xs mb-2">
                        <ul class="list-disc pl-4 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
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
                    <input type="text" 
                           x-model="editNominalFormatted" 
                           @input="onEditNominalInput($event)"
                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                    <input type="hidden" name="nominal" :value="editNominalRaw">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" x-model="editData.keterangan" rows="2" 
                              class="w-full text-xs bg-gray-50 border {{ $errors->has('keterangan') ? 'border-red-500' : 'border-border-muted' }} p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required></textarea>
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
                    <button @click="openEditModal({{ json_encode($pen) }})" 
                            class="inline-flex items-center gap-1 text-amber-700 font-semibold px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition-colors text-xs">
                        <x-lucide-square-pen class="w-3.5 h-3.5" />
                        <span>Edit</span>
                    </button>
                    
                    <button type="button" 
        @click="$dispatch('confirm-action', { 
            action: '{{ route('pengeluaran.destroy', $pen->id_pengeluaran) }}', 
            title: 'Hapus Pengeluaran', 
            message: 'Apakah Anda yakin ingin menghapus catatan pengeluaran ini?', 
            method: 'DELETE',
            confirmText: 'Ya, Hapus',
            confirmClass: 'bg-rose-600 hover:bg-rose-700'
        })"
        class="inline-flex items-center gap-1 text-rose-700 font-semibold px-3 py-1.5 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition-colors text-xs">
    <x-lucide-trash-2 class="w-3.5 h-3.5" />
    <span>Hapus</span>
</button>
                </div>
            </div>
        @empty
            <div class="text-center bg-white rounded-2xl border border-border-muted py-12 shadow-sm">
                <x-lucide-wallet class="w-10 h-10 text-gray-300 mx-auto mb-2" />
                <p class="text-gray-400 text-xs">Belum ada catatan pengeluaran.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection