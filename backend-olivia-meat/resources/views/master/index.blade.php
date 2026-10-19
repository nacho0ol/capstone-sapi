@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Kelola Data Master</h1>
            <p class="text-xs text-gray-500 mt-0.5">Manajemen data produk daging dan daftar pelanggan</p>
        </div>
    </div>

    <!-- Tab Switcher (Produk vs Pelanggan) -->
    <div class="flex border-b border-border-muted">
        <a href="{{ route('master.index', ['tab' => 'produk']) }}" 
           class="flex-1 sm:flex-none text-center py-3 px-6 text-xs font-bold transition-all flex items-center justify-center gap-2 {{ $tab == 'produk' ? 'border-b-2 border-[#842529] text-[#842529]' : 'text-gray-500 hover:text-gray-700' }}">
            <x-lucide-beef class="w-4 h-4" />
            <span>Data Produk</span>
        </a>
        <a href="{{ route('master.index', ['tab' => 'pelanggan']) }}" 
           class="flex-1 sm:flex-none text-center py-3 px-6 text-xs font-bold transition-all flex items-center justify-center gap-2 {{ $tab == 'pelanggan' ? 'border-b-2 border-[#842529] text-[#842529]' : 'text-gray-500 hover:text-gray-700' }}">
            <x-lucide-users class="w-4 h-4" />
            <span>Data Pelanggan</span>
        </a>
    </div>

    @if($tab == 'produk')
        <!-- ================= KONTEN TAB PRODUK ================= -->
        <div class="space-y-4">
            
            <!-- Wrapper Alpine.js untuk Modal Tambah Produk -->
            <div x-data="{ openAdd: {{ old('form_type') == 'create_produk' && $errors->any() ? 'true' : 'false' }} }">
                <button @click="openAdd = true" 
                        class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-[#842529] hover:bg-red-900 text-white text-xs py-2.5 px-4 rounded-xl font-semibold shadow-sm transition-all active:scale-95">
                    <x-lucide-plus-circle class="w-4 h-4 text-white" />
                    <span>Tambah Produk Baru</span>
                </button>

                <!-- MODAL TAMBAH PRODUK -->
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
                        <form action="{{ route('master.produk.store') }}" method="POST" class="p-4 space-y-3">
                            @csrf
                            <input type="hidden" name="form_type" value="create_produk">
                            
                            <!-- ERROR NOTIFICATION -->
                            @if(old('form_type') == 'create_produk' && $errors->any())
                                <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl text-xs space-y-1">
                                    <div class="font-bold flex items-center gap-1.5">
                                        <x-lucide-alert-circle class="w-4 h-4 text-red-600 flex-shrink-0" />
                                        <span>Periksa Inputan Anda:</span>
                                    </div>
                                    <ul class="list-disc pl-5 space-y-0.5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Produk</label>
                                <input type="text" name="nama_produk" value="{{ old('form_type') == 'create_produk' ? old('nama_produk') : '' }}" placeholder="Cth: Daging Sirloin / Iga Sapi" required 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                                @php $katOld = old('form_type') == 'create_produk' ? old('kategori') : ''; @endphp
                                <select name="kategori" class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                                    <option value="Daging" {{ $katOld == 'Daging' ? 'selected' : '' }}>Daging</option>
                                    <option value="Tulang" {{ $katOld == 'Tulang' ? 'selected' : '' }}>Tulang</option>
                                    <option value="Buntut" {{ $katOld == 'Buntut' ? 'selected' : '' }}>Buntut</option>
                                    <option value="Jeroan" {{ $katOld == 'Jeroan' ? 'selected' : '' }}>Jeroan</option>
                                    <option value="Kaki" {{ $katOld == 'Kaki' ? 'selected' : '' }}>Kaki</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Harga Awal</label>
                                    <input type="number" name="harga_awal" value="{{ old('form_type') == 'create_produk' ? old('harga_awal') : '' }}" placeholder="Rp 0" required 
                                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Harga Jual</label>
                                    <input type="number" name="harga_jual" value="{{ old('form_type') == 'create_produk' ? old('harga_jual') : '' }}" placeholder="Rp 0" required 
                                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Jagal / RPH</label>
                                <input type="text" name="nama_jagal" value="{{ old('form_type') == 'create_produk' ? old('nama_jagal') : '' }}" placeholder="Cth: Jagal Pak Ahmad" required 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
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
                @foreach($produk as $p)
                    <div x-data="{ openEdit: {{ old('id_produk') == $p->id_produk && $errors->any() ? 'true' : 'false' }} }" 
                         class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm hover:border-gray-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-900 text-sm">{{ $p->nama_produk }}</span>
                                <span class="text-[10px] bg-[#FAE2E2] text-[#842529] font-semibold px-2 py-0.5 rounded-md">
                                    {{ $p->kategori }}
                                </span>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                                <span>Harga Jual: <strong class="text-emerald-700">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</strong></span>
                                <span class="text-gray-300">•</span>
                                <span>Modal: Rp {{ number_format($p->harga_awal, 0, ',', '.') }}</span>
                                <span class="text-gray-300">•</span>
                                <span>Jagal: <strong class="text-gray-700">{{ $p->nama_jagal }}</strong></span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 self-end sm:self-center">
                            <button @click="openEdit = true" type="button" 
                                    class="inline-flex items-center gap-1 text-amber-700 font-semibold px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition-colors text-xs">
                                <x-lucide-square-pen class="w-3.5 h-3.5" />
                                <span>Edit</span>
                            </button>
                            <form action="{{ route('master.produk.delete', $p->id_produk) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center gap-1 text-rose-700 font-semibold px-3 py-1.5 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition-colors text-xs">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>

                        <!-- MODAL EDIT PRODUK -->
                        <div x-show="openEdit" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                            <div @click.away="openEdit = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-border-muted text-left">
                                <div class="flex justify-between items-center border-b border-border-muted p-4">
                                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                        <x-lucide-square-pen class="w-4 h-4 text-amber-600" />
                                        <span>Edit Produk</span>
                                    </h3>
                                    <button @click="openEdit = false" type="button" class="text-gray-400 hover:text-[#842529] p-1 rounded-lg transition-colors">
                                        <x-lucide-x class="w-5 h-5" />
                                    </button>
                                </div>
                                <form action="{{ route('master.produk.update', $p->id_produk) }}" method="POST" class="p-4 space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id_produk" value="{{ $p->id_produk }}">
                                    
                                    <!-- ERROR NOTIFICATION -->
                                    @if(old('id_produk') == $p->id_produk && $errors->any())
                                        <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl text-xs space-y-1">
                                            <div class="font-bold flex items-center gap-1.5">
                                                <x-lucide-alert-circle class="w-4 h-4 text-red-600 flex-shrink-0" />
                                                <span>Periksa Inputan Anda:</span>
                                            </div>
                                            <ul class="list-disc pl-5 space-y-0.5">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Produk</label>
                                        <input type="text" name="nama_produk" value="{{ old('id_produk') == $p->id_produk ? old('nama_produk') : $p->nama_produk }}" required 
                                               class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                                        @php $kat = old('id_produk') == $p->id_produk ? old('kategori') : $p->kategori; @endphp
                                        <select name="kategori" class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                                            <option value="Daging" {{ $kat == 'Daging' ? 'selected' : '' }}>Daging</option>
                                            <option value="Tulang" {{ $kat == 'Tulang' ? 'selected' : '' }}>Tulang</option>
                                            <option value="Buntut" {{ $kat == 'Buntut' ? 'selected' : '' }}>Buntut</option>
                                            <option value="Jeroan" {{ $kat == 'Jeroan' ? 'selected' : '' }}>Jeroan</option>
                                            <option value="Kaki" {{ $kat == 'Kaki' ? 'selected' : '' }}>Kaki</option>
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Harga Awal</label>
                                            <input type="number" name="harga_awal" value="{{ old('id_produk') == $p->id_produk ? old('harga_awal') : $p->harga_awal }}" required 
                                                   class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-700 mb-1">Harga Jual</label>
                                            <input type="number" name="harga_jual" value="{{ old('id_produk') == $p->id_produk ? old('harga_jual') : $p->harga_jual }}" required 
                                                   class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Jagal / RPH</label>
                                        <input type="text" name="nama_jagal" value="{{ old('id_produk') == $p->id_produk ? old('nama_jagal') : $p->nama_jagal }}" required 
                                               class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                                    </div>
                                    
                                    <button type="submit" class="w-full bg-[#842529] hover:bg-red-900 text-white py-2.5 rounded-xl font-bold text-xs transition-all shadow-md mt-2 flex items-center justify-center gap-2">
                                        <x-lucide-save class="w-4 h-4" />
                                        <span>Simpan Perubahan</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>

    @else
        <!-- ================= KONTEN TAB PELANGGAN ================= -->
        <div class="space-y-4">
            
            <!-- Wrapper Alpine.js untuk Modal Tambah Pelanggan -->
            <div x-data="{ openAdd: {{ old('form_type') == 'create_pelanggan' && $errors->any() ? 'true' : 'false' }} }">
                <button @click="openAdd = true" 
                        class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-[#842529] hover:bg-red-900 text-white text-xs py-2.5 px-4 rounded-xl font-semibold shadow-sm transition-all active:scale-95">
                    <x-lucide-user-plus class="w-4 h-4 text-white" />
                    <span>Tambah Pelanggan Baru</span>
                </button>

                <!-- MODAL TAMBAH PELANGGAN -->
                <div x-show="openAdd" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div @click.away="openAdd = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-border-muted">
                        <div class="flex justify-between items-center border-b border-border-muted p-4">
                            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <x-lucide-user-plus class="w-4 h-4 text-[#842529]" />
                                <span>Tambah Pelanggan Baru</span>
                            </h3>
                            <button @click="openAdd = false" type="button" class="text-gray-400 hover:text-[#842529] p-1 rounded-lg transition-colors">
                                <x-lucide-x class="w-5 h-5" />
                            </button>
                        </div>
                        <form action="{{ route('master.pelanggan.store') }}" method="POST" class="p-4 space-y-3">
                            @csrf
                            <input type="hidden" name="form_type" value="create_pelanggan">
                            
                            <!-- ERROR NOTIFICATION -->
                            @if(old('form_type') == 'create_pelanggan' && $errors->any())
                                <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl text-xs space-y-1">
                                    <div class="font-bold flex items-center gap-1.5">
                                        <x-lucide-alert-circle class="w-4 h-4 text-red-600 flex-shrink-0" />
                                        <span>Periksa Inputan Anda:</span>
                                    </div>
                                    <ul class="list-disc pl-5 space-y-0.5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" value="{{ old('form_type') == 'create_pelanggan' ? old('nama_pelanggan') : '' }}" placeholder="Nama Lengkap" required 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">No. Telepon / WhatsApp</label>
                                <input type="text" name="no_telp" value="{{ old('form_type') == 'create_pelanggan' ? old('no_telp') : '' }}" placeholder="Cth: 08123456789" required 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
                                <textarea name="alamat" placeholder="Alamat rumah / toko / warung" rows="3" required 
                                          class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">{{ old('form_type') == 'create_pelanggan' ? old('alamat') : '' }}</textarea>
                            </div>
                            
                            <button type="submit" class="w-full bg-[#842529] hover:bg-red-900 text-white text-xs py-2.5 rounded-xl font-bold transition-all shadow-md mt-2 flex items-center justify-center gap-2">
                                <x-lucide-save class="w-4 h-4" />
                                <span>Simpan Pelanggan</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Daftar Pelanggan -->
            <div class="space-y-2">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Daftar Pelanggan Terdaftar</h3>
                @foreach($pelanggan as $pl)
                    <div x-data="{ openEdit: {{ old('id_pelanggan') == $pl->id_pelanggan && $errors->any() ? 'true' : 'false' }} }" 
                         class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm hover:border-gray-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <x-lucide-user class="w-4 h-4 text-[#842529]" />
                                <span class="font-bold text-gray-900 text-sm">{{ $pl->nama_pelanggan }}</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                                <span class="flex items-center gap-1">
                                    <x-lucide-phone class="w-3.5 h-3.5 text-gray-400" />
                                    <span>{{ $pl->no_telp }}</span>
                                </span>
                                <span class="text-gray-300">•</span>
                                <span class="flex items-center gap-1">
                                    <x-lucide-map-pin class="w-3.5 h-3.5 text-gray-400" />
                                    <span>{{ $pl->alamat }}</span>
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 self-end sm:self-center">
                            <button @click="openEdit = true" type="button" 
                                    class="inline-flex items-center gap-1 text-amber-700 font-semibold px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition-colors text-xs">
                                <x-lucide-square-pen class="w-3.5 h-3.5" />
                                <span>Edit</span>
                            </button>
                            <form action="{{ route('master.pelanggan.delete', $pl->id_pelanggan) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center gap-1 text-rose-700 font-semibold px-3 py-1.5 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition-colors text-xs">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>

                        <!-- MODAL EDIT PELANGGAN -->
                        <div x-show="openEdit" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                            <div @click.away="openEdit = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-border-muted text-left">
                                <div class="flex justify-between items-center border-b border-border-muted p-4">
                                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                        <x-lucide-square-pen class="w-4 h-4 text-amber-600" />
                                        <span>Edit Pelanggan</span>
                                    </h3>
                                    <button @click="openEdit = false" type="button" class="text-gray-400 hover:text-[#842529] p-1 rounded-lg transition-colors">
                                        <x-lucide-x class="w-5 h-5" />
                                    </button>
                                </div>
                                <form action="{{ route('master.pelanggan.update', $pl->id_pelanggan) }}" method="POST" class="p-4 space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id_pelanggan" value="{{ $pl->id_pelanggan }}">
                                    
                                    <!-- ERROR NOTIFICATION -->
                                    @if(old('id_pelanggan') == $pl->id_pelanggan && $errors->any())
                                        <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl text-xs space-y-1">
                                            <div class="font-bold flex items-center gap-1.5">
                                                <x-lucide-alert-circle class="w-4 h-4 text-red-600 flex-shrink-0" />
                                                <span>Periksa Inputan Anda:</span>
                                            </div>
                                            <ul class="list-disc pl-5 space-y-0.5">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Pelanggan</label>
                                        <input type="text" name="nama_pelanggan" value="{{ old('id_pelanggan') == $pl->id_pelanggan ? old('nama_pelanggan') : $pl->nama_pelanggan }}" required 
                                               class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">No. Telepon / WhatsApp</label>
                                        <input type="text" name="no_telp" value="{{ old('id_pelanggan') == $pl->id_pelanggan ? old('no_telp') : $pl->no_telp }}" required 
                                               class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
                                        <textarea name="alamat" rows="3" required 
                                                  class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white transition-all">{{ old('id_pelanggan') == $pl->id_pelanggan ? old('alamat') : $pl->alamat }}</textarea>
                                    </div>
                                    
                                    <button type="submit" class="w-full bg-[#842529] hover:bg-red-900 text-white py-2.5 rounded-xl font-bold text-xs transition-all shadow-md mt-2 flex items-center justify-center gap-2">
                                        <x-lucide-save class="w-4 h-4" />
                                        <span>Simpan Perubahan</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection