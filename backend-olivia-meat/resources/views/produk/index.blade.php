@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Data Produk & Jagal</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola daftar produk daging, foto katalog, serta mitra jagal / RPH</p>
        </div>
    </div>

    <!-- Tab Switcher (Produk vs Jagal) -->
    <div class="flex border-b border-border-muted">
        <a href="{{ route('produk.index', ['tab' => 'produk']) }}" 
           class="flex-1 sm:flex-none text-center py-3 px-6 text-xs font-bold transition-all flex items-center justify-center gap-2 {{ $tab == 'produk' ? 'border-b-2 border-[#842529] text-[#842529]' : 'text-gray-500 hover:text-gray-700' }}">
            <x-lucide-beef class="w-4 h-4" />
            <span>Data Produk</span>
        </a>
        <a href="{{ route('produk.index', ['tab' => 'jagal']) }}" 
           class="flex-1 sm:flex-none text-center py-3 px-6 text-xs font-bold transition-all flex items-center justify-center gap-2 {{ $tab == 'jagal' ? 'border-b-2 border-[#842529] text-[#842529]' : 'text-gray-500 hover:text-gray-700' }}">
            <x-lucide-store class="w-4 h-4" />
            <span>Data Jagal</span>
        </a>
    </div>

    @if($tab == 'produk')
        <!-- ================= KONTEN TAB PRODUK ================= -->
        <div class="space-y-4">
            <!-- Form Search Produk -->
            <form action="{{ route('produk.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2">
                <input type="hidden" name="tab" value="produk">
                
                <div class="relative w-full sm:flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <x-lucide-search class="w-4 h-4" />
                    </span>
                    <input type="text" 
                        name="search_produk" 
                        value="{{ request('search_produk') }}" 
                        placeholder="Cari nama produk, kategori, atau nama jagal..." 
                        class="w-full pl-9 pr-4 py-2.5 bg-white border border-border-muted rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#842529]">
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto bg-[#842529] hover:bg-red-900 text-white text-xs px-4 py-2.5 rounded-xl font-semibold shadow-sm transition-all active:scale-95 flex items-center justify-center gap-1.5">
                        <x-lucide-search class="w-3.5 h-3.5" />
                        <span>Cari</span>
                    </button>

                    @if(request()->filled('search_produk'))
                        <a href="{{ route('produk.index', ['tab' => 'produk']) }}" 
                           class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs px-3 py-2.5 rounded-xl font-semibold transition-all flex items-center justify-center gap-1">
                            <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </form>

            <!-- Modal Tambah Produk -->
            <div x-data="{ 
                openAdd: {{ old('form_type') == 'create_produk' && $errors->any() ? 'true' : 'false' }},
                hargaAwalRaw: '{{ old('form_type') == 'create_produk' ? old('harga_awal') : '' }}',
                hargaAwalFormatted: '',
                hargaJualRaw: '{{ old('form_type') == 'create_produk' ? old('harga_jual') : '' }}',
                hargaJualFormatted: '',
                previewFoto: null,
                fotoName: '',

                formatRupiah(val) {
                    let number = String(val).replace(/\D/g, '');
                    if (!number) return { raw: '', formatted: '' };
                    let formatted = new Intl.NumberFormat('id-ID').format(number);
                    return { raw: number, formatted: formatted };
                },

                onFileChange(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.previewFoto = URL.createObjectURL(file);
                        this.fotoName = file.name;
                    }
                },

                clearAddFoto() {
                    this.previewFoto = null;
                    this.fotoName = '';
                    if (this.$refs.fotoAddInput) {
                        this.$refs.fotoAddInput.value = '';
                    }
                },

                init() {
                    if (this.hargaAwalRaw) this.hargaAwalFormatted = this.formatRupiah(this.hargaAwalRaw).formatted;
                    if (this.hargaJualRaw) this.hargaJualFormatted = this.formatRupiah(this.hargaJualRaw).formatted;
                }
            }">
                <button @click="openAdd = true" 
                        class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-[#842529] hover:bg-red-900 text-white text-xs py-2.5 px-4 rounded-xl font-semibold shadow-sm transition-all active:scale-95">
                    <x-lucide-plus-circle class="w-4 h-4 text-white" />
                    <span>Tambah Produk Baru</span>
                </button>

                <div x-show="openAdd" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div @click.away="openAdd = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-border-muted">
                        <div class="flex justify-between items-center border-b border-border-muted p-4">
                            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <x-lucide-package-plus class="w-4 h-4 text-[#842529]" />
                                <span>Tambah Produk Baru</span>
                            </h3>
                            <button @click="openAdd = false" type="button" class="text-gray-400 hover:text-[#842529] p-1 rounded-lg transition-colors">
                                <x-lucide-x class="w-5 h-5" />
                            </button>
                        </div>
                        <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-3">
                            @csrf
                            <input type="hidden" name="form_type" value="create_produk">
                            
                            @if(old('form_type') == 'create_produk' && $errors->any())
                                <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl text-xs space-y-1">
                                    <ul class="list-disc pl-5 space-y-0.5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Upload & Preview Foto Produk -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Foto Produk (Opsional)</label>
                                <div class="flex items-center gap-3 bg-gray-50 border border-border-muted p-2.5 rounded-xl">
                                    <div style="width: 76px; height: 76px; min-width: 76px;" class="rounded-xl bg-white border border-border-muted flex items-center justify-center overflow-hidden">
                                        <template x-if="previewFoto">
                                            <img :src="previewFoto" alt="Preview Foto" style="width: 100%; height: 100%; object-fit: cover;">
                                        </template>
                                        <template x-if="!previewFoto">
                                            <div class="flex flex-col items-center justify-center text-gray-400">
                                                <x-lucide-image class="w-6 h-6" />
                                                <span class="text-[9px] mt-0.5">No Foto</span>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="flex-1 min-w-0 space-y-1.5">
                                        <input type="file" x-ref="fotoAddInput" name="foto" accept="image/jpeg,image/png,image/webp" @change="onFileChange($event)" style="display: none;">
                                        
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            <button type="button" @click="$refs.fotoAddInput.click()"
                                                    class="inline-flex items-center gap-1.5 bg-[#FAE2E2] hover:bg-red-100 text-[#842529] px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                                                <x-lucide-camera class="w-3.5 h-3.5" />
                                                <span x-text="previewFoto ? 'Ganti Foto' : 'Pilih Foto'"></span>
                                            </button>

                                            <button type="button" x-show="previewFoto" @click="clearAddFoto()"
                                                    class="inline-flex items-center gap-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors">
                                                <x-lucide-x class="w-3 h-3" />
                                                <span>Hapus</span>
                                            </button>
                                        </div>

                                        <p class="text-[11px] text-gray-500 truncate" x-text="fotoName ? fotoName : 'JPG, PNG, WEBP (Maks. 2MB)'"></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Produk</label>
                                <input type="text" name="nama_produk" value="{{ old('form_type') == 'create_produk' ? old('nama_produk') : '' }}" placeholder="Cth: Daging Sirloin / Iga Sapi" required 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                                @php $katOld = old('form_type') == 'create_produk' ? old('kategori') : ''; @endphp
                                <select name="kategori" class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white">
                                    <option value="Daging" {{ $katOld == 'Daging' ? 'selected' : '' }}>Daging</option>
                                    <option value="Tulang" {{ $katOld == 'Tulang' ? 'selected' : '' }}>Tulang</option>
                                    <option value="Buntut" {{ $katOld == 'Buntut' ? 'selected' : '' }}>Buntut</option>
                                    <option value="Jeroan" {{ $katOld == 'Jeroan' ? 'selected' : '' }}>Jeroan</option>
                                    <option value="Kaki" {{ $katOld == 'Kaki' ? 'selected' : '' }}>Kaki</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Harga Modal / Jagal</label>
                                    <input type="text" 
                                           x-model="hargaAwalFormatted"
                                           @input="let res = formatRupiah($event.target.value); hargaAwalRaw = res.raw; hargaAwalFormatted = res.formatted;"
                                           placeholder="Rp 0" required 
                                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white">
                                    <input type="hidden" name="harga_awal" :value="hargaAwalRaw">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Harga Jual</label>
                                    <input type="text" 
                                           x-model="hargaJualFormatted"
                                           @input="let res = formatRupiah($event.target.value); hargaJualRaw = res.raw; hargaJualFormatted = res.formatted;"
                                           placeholder="Rp 0" required 
                                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white">
                                    <input type="hidden" name="harga_jual" :value="hargaJualRaw">
                                </div>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-semibold text-gray-700">Jagal / RPH</label>
                                    <a href="{{ route('produk.index', ['tab' => 'jagal']) }}" class="text-[10px] text-[#842529] font-bold hover:underline">+ Kelola Jagal</a>
                                </div>
                                <select name="id_jagal" required class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white">
                                    <option value="">-- Pilih Jagal --</option>
                                    @foreach($listJagal as $jg)
                                        <option value="{{ $jg->id_jagal }}" {{ (old('form_type') == 'create_produk' && old('id_jagal') == $jg->id_jagal) ? 'selected' : '' }}>
                                            {{ $jg->nama_jagal }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <button type="submit" class="w-full bg-[#842529] hover:bg-red-900 text-white text-xs py-2.5 rounded-xl font-bold transition-all shadow-md mt-2 flex items-center justify-center gap-2">
                                <x-lucide-save class="w-4 h-4" />
                                <span>Simpan Produk</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Daftar Produk -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Daftar Produk Terdaftar</h3>
                @forelse($produk as $p)
                    @php
                        $fotoUrl = $p->foto ? asset('storage/' . $p->foto) : '';
                    @endphp
                    <div x-data="{ 
                        openEdit: {{ old('id_produk') == $p->id_produk && $errors->any() ? 'true' : 'false' }},
                        hargaAwalRaw: '{{ old('id_produk') == $p->id_produk ? old('harga_awal') : $p->harga_awal }}',
                        hargaAwalFormatted: '',
                        hargaJualRaw: '{{ old('id_produk') == $p->id_produk ? old('harga_jual') : $p->harga_jual }}',
                        hargaJualFormatted: '',
                        originalFoto: '{{ $fotoUrl }}',
                        previewEditFoto: '{{ $fotoUrl }}',
                        editFotoName: '',
                        isFotoChanged: false,

                        formatRupiah(val) {
                            let number = String(val).replace(/\D/g, '');
                            if (!number) return { raw: '', formatted: '' };
                            let formatted = new Intl.NumberFormat('id-ID').format(number);
                            return { raw: number, formatted: formatted };
                        },

                        onEditFileChange(e) {
                            const file = e.target.files[0];
                            if (file) {
                                this.previewEditFoto = URL.createObjectURL(file);
                                this.editFotoName = file.name;
                                this.isFotoChanged = true;
                            }
                        },

                        resetEditFoto() {
                            this.previewEditFoto = this.originalFoto;
                            this.editFotoName = '';
                            this.isFotoChanged = false;
                            if (this.$refs.fotoEditInput) {
                                this.$refs.fotoEditInput.value = '';
                            }
                        },

                        init() {
                            if (this.hargaAwalRaw) this.hargaAwalFormatted = this.formatRupiah(this.hargaAwalRaw).formatted;
                            if (this.hargaJualRaw) this.hargaJualFormatted = this.formatRupiah(this.hargaJualRaw).formatted;
                        }
                    }" 
                         class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm hover:border-gray-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        
                        <div class="flex items-center gap-3">
                            <!-- Thumbnail Foto Produk -->
                            <div style="width: 56px; height: 56px; min-width: 56px;" class="rounded-xl bg-gray-100 border border-border-muted flex items-center justify-center overflow-hidden">
                                @if($p->foto)
                                    <img src="{{ asset('storage/' . $p->foto) }}" alt="{{ $p->nama_produk }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <x-lucide-beef class="w-6 h-6 text-[#842529]/50" />
                                @endif
                            </div>

                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-bold text-gray-900 text-sm">{{ $p->nama_produk }}</span>
                                    <span class="text-[10px] bg-[#FAE2E2] text-[#842529] font-semibold px-2 py-0.5 rounded-md">
                                        {{ $p->kategori }}
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                                    <span>Harga Jual: <strong class="text-emerald-700">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</strong></span>
                                    <span class="text-gray-300">•</span>
                                    <span>Modal Jagal: Rp {{ number_format($p->harga_awal, 0, ',', '.') }}</span>
                                    <span class="text-gray-300">•</span>
                                    <span>Jagal: <strong class="text-gray-700">{{ $p->jagal->nama_jagal ?? $p->nama_jagal ?? '-' }}</strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-center">
                            <button @click="openEdit = true" type="button" 
                                    class="inline-flex items-center gap-1 text-amber-700 font-semibold px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition-colors text-xs">
                                <x-lucide-square-pen class="w-3.5 h-3.5" />
                                <span>Edit</span>
                            </button>
                            <button type="button" 
                                    @click="$dispatch('confirm-action', { 
                                        action: '{{ route('produk.destroy', $p->id_produk) }}', 
                                        title: 'Hapus Produk', 
                                        message: 'Apakah Anda yakin ingin menghapus {{ $p->nama_produk }}?', 
                                        method: 'DELETE',
                                        confirmText: 'Ya, Hapus',
                                        confirmClass: 'bg-rose-600 hover:bg-rose-700'
                                    })"
                                    class="inline-flex items-center gap-1 text-rose-700 font-semibold px-3 py-1.5 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition-colors text-xs">
                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                <span>Hapus</span>
                            </button>
                        </div>

                        <!-- MODAL EDIT PRODUK -->
                        <div x-show="openEdit" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                            <div @click.away="openEdit = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-border-muted text-left">
                                <div class="flex justify-between items-center border-b border-border-muted p-4">
                                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                        <x-lucide-square-pen class="w-4 h-4 text-amber-600" />
                                        <span>Edit Produk</span>
                                    </h3>
                                    <button @click="openEdit = false" type="button" class="text-gray-400 hover:text-[#842529] p-1 rounded-lg">
                                        <x-lucide-x class="w-5 h-5" />
                                    </button>
                                </div>
                                <form action="{{ route('produk.update', $p->id_produk) }}" method="POST" enctype="multipart/form-data" class="p-4 space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id_produk" value="{{ $p->id_produk }}">

                                    <!-- Preview & Ganti Foto Produk -->
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Foto Produk</label>
                                        <div class="flex items-center gap-3 bg-gray-50 border border-border-muted p-2.5 rounded-xl">
                                            <div style="width: 76px; height: 76px; min-width: 76px;" class="rounded-xl bg-white border border-border-muted flex items-center justify-center overflow-hidden">
                                                <template x-if="previewEditFoto">
                                                    <img :src="previewEditFoto" alt="Foto Produk" style="width: 100%; height: 100%; object-fit: cover;">
                                                </template>
                                                <template x-if="!previewEditFoto">
                                                    <div class="flex flex-col items-center justify-center text-gray-400">
                                                        <x-lucide-image class="w-6 h-6" />
                                                        <span class="text-[9px] mt-0.5">No Foto</span>
                                                    </div>
                                                </template>
                                            </div>

                                            <div class="flex-1 min-w-0 space-y-1.5">
                                                <input type="file" x-ref="fotoEditInput" name="foto" accept="image/jpeg,image/png,image/webp" @change="onEditFileChange($event)" style="display: none;">
                                                
                                                <div class="flex flex-wrap items-center gap-1.5">
                                                    <button type="button" @click="$refs.fotoEditInput.click()"
                                                            class="inline-flex items-center gap-1.5 bg-[#FAE2E2] hover:bg-red-100 text-[#842529] px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                                                        <x-lucide-camera class="w-3.5 h-3.5" />
                                                        <span x-text="previewEditFoto ? 'Ganti Foto' : 'Upload Foto'"></span>
                                                    </button>

                                                    <button type="button" x-show="isFotoChanged" @click="resetEditFoto()"
                                                            class="inline-flex items-center gap-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors">
                                                        <x-lucide-rotate-ccw class="w-3 h-3" />
                                                        <span>Batal Ganti</span>
                                                    </button>
                                                </div>

                                                <p class="text-[11px] text-gray-500 truncate" 
                                                   x-text="isFotoChanged ? ('Foto baru: ' + editFotoName) : (previewEditFoto ? 'Foto saat ini terpasang' : 'Belum ada foto (Maks. 2MB)')"></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Produk</label>
                                        <input type="text" name="nama_produk" value="{{ old('id_produk') == $p->id_produk ? old('nama_produk') : $p->nama_produk }}" required 
                                               class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                                        @php $kat = old('id_produk') == $p->id_produk ? old('kategori') : $p->kategori; @endphp
                                        <select name="kategori" class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                                            <option value="Daging" {{ $kat == 'Daging' ? 'selected' : '' }}>Daging</option>
                                            <option value="Tulang" {{ $kat == 'Tulang' ? 'selected' : '' }}>Tulang</option>
                                            <option value="Buntut" {{ $kat == 'Buntut' ? 'selected' : '' }}>Buntut</option>
                                            <option value="Jeroan" {{ $kat == 'Jeroan' ? 'selected' : '' }}>Jeroan</option>
                                            <option value="Kaki" {{ $kat == 'Kaki' ? 'selected' : '' }}>Kaki</option>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Harga Modal / Jagal</label>
                                            <input type="text" 
                                                   x-model="hargaAwalFormatted"
                                                   @input="let res = formatRupiah($event.target.value); hargaAwalRaw = res.raw; hargaAwalFormatted = res.formatted;"
                                                   required 
                                                   class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                                            <input type="hidden" name="harga_awal" :value="hargaAwalRaw">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Harga Jual</label>
                                            <input type="text" 
                                                   x-model="hargaJualFormatted"
                                                   @input="let res = formatRupiah($event.target.value); hargaJualRaw = res.raw; hargaJualFormatted = res.formatted;"
                                                   required 
                                                   class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                                            <input type="hidden" name="harga_jual" :value="hargaJualRaw">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Jagal / RPH</label>
                                        @php $selectedJagal = old('id_produk') == $p->id_produk ? old('id_jagal') : $p->id_jagal; @endphp
                                        <select name="id_jagal" required class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                                            @foreach($listJagal as $jg)
                                                <option value="{{ $jg->id_jagal }}" {{ $selectedJagal == $jg->id_jagal ? 'selected' : '' }}>
                                                    {{ $jg->nama_jagal }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="w-full bg-[#842529] hover:bg-red-900 text-white py-2.5 rounded-xl font-bold text-xs shadow-md mt-2 flex items-center justify-center gap-2">
                                        <x-lucide-save class="w-4 h-4" />
                                        <span>Simpan Perubahan</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center bg-white rounded-2xl border border-border-muted py-12 shadow-sm">
                        <p class="text-gray-400 text-xs">Belum ada data produk.</p>
                    </div>
                @endforelse
            </div>
        </div>

    @else
        <!-- ================= KONTEN TAB JAGAL ================= -->
        <div class="space-y-4">
            <!-- Form Search Jagal -->
            <form action="{{ route('produk.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2">
                <input type="hidden" name="tab" value="jagal">
                
                <div class="relative w-full sm:flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <x-lucide-search class="w-4 h-4" />
                    </span>
                    <input type="text" 
                        name="search_jagal" 
                        value="{{ request('search_jagal') }}" 
                        placeholder="Cari nama jagal atau keterangan..." 
                        class="w-full pl-9 pr-4 py-2.5 bg-white border border-border-muted rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#842529]">
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto bg-[#842529] hover:bg-red-900 text-white text-xs px-4 py-2.5 rounded-xl font-semibold shadow-sm flex items-center justify-center gap-1.5">
                        <x-lucide-search class="w-3.5 h-3.5" />
                        <span>Cari</span>
                    </button>

                    @if(request()->filled('search_jagal'))
                        <a href="{{ route('produk.index', ['tab' => 'jagal']) }}" 
                           class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs px-3 py-2.5 rounded-xl font-semibold flex items-center justify-center gap-1">
                            <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </form>

            <!-- Modal Tambah Jagal -->
            <div x-data="{ openAddJagal: {{ old('form_type') == 'create_jagal' && $errors->any() ? 'true' : 'false' }} }">
                <button @click="openAddJagal = true" 
                        class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-[#842529] hover:bg-red-900 text-white text-xs py-2.5 px-4 rounded-xl font-semibold shadow-sm transition-all active:scale-95">
                    <x-lucide-plus-circle class="w-4 h-4 text-white" />
                    <span>Tambah Jagal Baru</span>
                </button>

                <div x-show="openAddJagal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div @click.away="openAddJagal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md border border-border-muted">
                        <div class="flex justify-between items-center border-b border-border-muted p-4">
                            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <x-lucide-store class="w-4 h-4 text-[#842529]" />
                                <span>Tambah Jagal Baru</span>
                            </h3>
                            <button @click="openAddJagal = false" type="button" class="text-gray-400 hover:text-[#842529] p-1 rounded-lg">
                                <x-lucide-x class="w-5 h-5" />
                            </button>
                        </div>
                        <form action="{{ route('jagal.store') }}" method="POST" class="p-4 space-y-3">
                            @csrf
                            <input type="hidden" name="form_type" value="create_jagal">

                            @if(old('form_type') == 'create_jagal' && $errors->any())
                                <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl text-xs">
                                    <ul class="list-disc pl-5 space-y-0.5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Jagal (Maks. 30 Karakter)</label>
                                <input type="text" name="nama_jagal" maxlength="30" value="{{ old('form_type') == 'create_jagal' ? old('nama_jagal') : '' }}" placeholder="Cth: Jagal Pak Ahmad" required 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan (Opsional)</label>
                                <textarea name="keterangan" rows="3" placeholder="Alamat RPH, nomor kontak, atau catatan pembayaran ke jagal..." 
                                          class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">{{ old('form_type') == 'create_jagal' ? old('keterangan') : '' }}</textarea>
                            </div>

                            <button type="submit" class="w-full bg-[#842529] hover:bg-red-900 text-white text-xs py-2.5 rounded-xl font-bold shadow-md flex items-center justify-center gap-2">
                                <x-lucide-save class="w-4 h-4" />
                                <span>Simpan Jagal</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Daftar Jagal -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Daftar Jagal / RPH Terdaftar</h3>
                @forelse($jagals as $jg)
                    <div x-data="{ openEditJagal: false }" 
                         class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm hover:border-gray-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <x-lucide-store class="w-4 h-4 text-[#842529]" />
                                <span class="font-bold text-gray-900 text-sm">{{ $jg->nama_jagal }}</span>
                            </div>
                            <p class="text-xs text-gray-500">{{ $jg->keterangan ?: 'Tidak ada keterangan tambahan.' }}</p>
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-center">
                            <button @click="openEditJagal = true" type="button" 
                                    class="inline-flex items-center gap-1 text-amber-700 font-semibold px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 text-xs">
                                <x-lucide-square-pen class="w-3.5 h-3.5" />
                                <span>Edit</span>
                            </button>
                            <button type="button" 
                                    @click="$dispatch('confirm-action', { 
                                        action: '{{ route('jagal.destroy', $jg->id_jagal) }}', 
                                        title: 'Hapus Jagal', 
                                        message: 'Apakah Anda yakin ingin menghapus {{ $jg->nama_jagal }}?', 
                                        method: 'DELETE',
                                        confirmText: 'Ya, Hapus',
                                        confirmClass: 'bg-rose-600 hover:bg-rose-700'
                                    })"
                                    class="inline-flex items-center gap-1 text-rose-700 font-semibold px-3 py-1.5 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 text-xs">
                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                <span>Hapus</span>
                            </button>
                        </div>

                        <!-- MODAL EDIT JAGAL -->
                        <div x-show="openEditJagal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                            <div @click.away="openEditJagal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md border border-border-muted text-left">
                                <div class="flex justify-between items-center border-b border-border-muted p-4">
                                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                        <x-lucide-square-pen class="w-4 h-4 text-amber-600" />
                                        <span>Edit Data Jagal</span>
                                    </h3>
                                    <button @click="openEditJagal = false" type="button" class="text-gray-400 hover:text-[#842529] p-1 rounded-lg">
                                        <x-lucide-x class="w-5 h-5" />
                                    </button>
                                </div>
                                <form action="{{ route('jagal.update', $jg->id_jagal) }}" method="POST" class="p-4 space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Jagal (Maks. 30 Karakter)</label>
                                        <input type="text" name="nama_jagal" maxlength="30" value="{{ $jg->nama_jagal }}" required 
                                               class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan</label>
                                        <textarea name="keterangan" rows="3" 
                                                  class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">{{ $jg->keterangan }}</textarea>
                                    </div>
                                    <button type="submit" class="w-full bg-[#842529] hover:bg-red-900 text-white py-2.5 rounded-xl font-bold text-xs shadow-md flex items-center justify-center gap-2">
                                        <x-lucide-save class="w-4 h-4" />
                                        <span>Simpan Perubahan</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center bg-white rounded-2xl border border-border-muted py-12 shadow-sm">
                        <p class="text-gray-400 text-xs">Belum ada data jagal terdaftar.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>
@endsection