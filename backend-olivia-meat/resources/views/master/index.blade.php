@extends('layouts.app')

@section('content')
<div>
    <h2 class="font-bold text-gray-700 text-base mb-3">Kelola Data Master</h2>

    <!-- Switch Tab Button -->
    <div class="flex border-b border-gray-200 mb-4">
        <a href="{{ route('master.index', ['tab' => 'produk']) }}" class="flex-1 text-center py-2 text-xs font-semibold {{ $tab == 'produk' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-500' }}">
            Tab Produk
        </a>
        <a href="{{ route('master.index', ['tab' => 'pelanggan']) }}" class="flex-1 text-center py-2 text-xs font-semibold {{ $tab == 'pelanggan' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-500' }}">
            Tab Pelanggan
        </a>
    </div>

    @if($tab == 'produk')
        <!-- ================= KONTEN TAB PRODUK ================= -->
        <div class="space-y-4">
            
            <!-- Wrapper Alpine.js untuk Modal Tambah Produk -->
            <div x-data="{ openAdd: {{ old('form_type') == 'create_produk' && $errors->any() ? 'true' : 'false' }} }">
                <button @click="openAdd = true" class="w-full bg-red-600 text-white text-xs py-2.5 rounded-lg font-bold shadow-sm hover:bg-red-700 flex justify-center items-center">
                    <span class="mr-1.5 text-base leading-none">+</span> Tambah Produk Baru
                </button>

                <!-- MODAL TAMBAH PRODUK -->
                <div x-show="openAdd" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 p-4">
                    <div @click.away="openAdd = false" class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                        <div class="flex justify-between items-center border-b border-gray-100 p-3">
                            <h3 class="text-sm font-bold text-gray-700">Tambah Produk Baru</h3>
                            <button @click="openAdd = false" type="button" class="text-gray-400 hover:text-red-600 font-bold text-lg">&times;</button>
                        </div>
                        <form action="{{ route('master.produk.store') }}" method="POST" class="p-4 space-y-3">
                            @csrf
                            <input type="hidden" name="form_type" value="create_produk">
                            
                            <!-- ERROR MUNCUL DI DALAM MODAL SINI -->
                            @if(old('form_type') == 'create_produk' && $errors->any())
                                <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded text-[11px] mb-2">
                                    <ul class="list-disc pl-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <input type="text" name="nama_produk" value="{{ old('form_type') == 'create_produk' ? old('nama_produk') : '' }}" placeholder="Nama Produk (Cth: Daging Sirloin)" required class="w-full text-xs border p-2 rounded focus:outline-none focus:border-red-500">
                            
                            @php $katOld = old('form_type') == 'create_produk' ? old('kategori') : ''; @endphp
                            <select name="kategori" class="w-full text-xs border p-2 rounded focus:outline-none focus:border-red-500">
                                <option value="Daging" {{ $katOld == 'Daging' ? 'selected' : '' }}>Daging</option>
                                <option value="Tulang" {{ $katOld == 'Tulang' ? 'selected' : '' }}>Tulang</option>
                                <option value="Buntut" {{ $katOld == 'Buntut' ? 'selected' : '' }}>Buntut</option>
                                <option value="Jeroan" {{ $katOld == 'Jeroan' ? 'selected' : '' }}>Jeroan</option>
                                <option value="Kaki" {{ $katOld == 'Kaki' ? 'selected' : '' }}>Kaki</option>
                            </select>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" name="harga_awal" value="{{ old('form_type') == 'create_produk' ? old('harga_awal') : '' }}" placeholder="Harga Awal" required class="w-full text-xs border p-2 rounded focus:outline-none focus:border-red-500">
                                <input type="number" name="harga_jual" value="{{ old('form_type') == 'create_produk' ? old('harga_jual') : '' }}" placeholder="Harga Jual" required class="w-full text-xs border p-2 rounded focus:outline-none focus:border-red-500">
                            </div>
                            <input type="text" name="nama_jagal" value="{{ old('form_type') == 'create_produk' ? old('nama_jagal') : '' }}" placeholder="Nama Jagal / RPH" required class="w-full text-xs border p-2 rounded focus:outline-none focus:border-red-500">
                            
                            <button type="submit" class="w-full bg-red-600 text-white text-xs py-2 rounded-lg font-semibold hover:bg-red-700 mt-2">Simpan Produk</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Daftar Produk -->
            <div>
                <h3 class="text-xs font-bold text-gray-600 mb-2">Distribusi List Produk</h3>
                @foreach($produk as $p)
                    <div x-data="{ openEdit: {{ old('id_produk') == $p->id_produk && $errors->any() ? 'true' : 'false' }} }" class="bg-white border rounded p-3 text-xs shadow-sm mb-2 flex justify-between items-center">
                        
                        <div>
                            <span class="font-bold text-red-600">{{ $p->nama_produk }}</span> ({{ $p->kategori }})
                            <p class="text-gray-500 mt-0.5">Jual: Rp {{ number_format($p->harga_jual, 0, ',', '.') }} | Jagal: {{ $p->nama_jagal }}</p>
                        </div>
                        <div class="flex space-x-1.5">
                            <button @click="openEdit = true" type="button" class="text-blue-600 font-bold px-2.5 py-1 bg-blue-50 rounded hover:bg-blue-100">Edit</button>
                            <form action="{{ route('master.produk.delete', $p->id_produk) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 font-bold px-2.5 py-1 bg-red-50 rounded hover:bg-red-100">Hapus</button>
                            </form>
                        </div>

                        <!-- MODAL EDIT PRODUK -->
                        <div x-show="openEdit" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 p-4">
                            <div @click.away="openEdit = false" class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                                <div class="flex justify-between items-center border-b border-gray-100 p-3">
                                    <h3 class="text-sm font-bold text-gray-700">Edit Produk</h3>
                                    <button @click="openEdit = false" type="button" class="text-gray-400 hover:text-red-600 font-bold text-lg">&times;</button>
                                </div>
                                <form action="{{ route('master.produk.update', $p->id_produk) }}" method="POST" class="p-4 space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id_produk" value="{{ $p->id_produk }}">
                                    
                                    <!-- ERROR MUNCUL DI DALAM MODAL SINI -->
                                    @if(old('id_produk') == $p->id_produk && $errors->any())
                                        <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded text-[11px] mb-2">
                                            <ul class="list-disc pl-3">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <input type="text" name="nama_produk" value="{{ old('id_produk') == $p->id_produk ? old('nama_produk') : $p->nama_produk }}" required class="w-full text-xs border p-2 rounded focus:outline-none focus:border-red-500">
                                    
                                    @php $kat = old('id_produk') == $p->id_produk ? old('kategori') : $p->kategori; @endphp
                                    <select name="kategori" class="w-full text-xs border p-2 rounded focus:outline-none focus:border-red-500">
                                        <option value="Daging" {{ $kat == 'Daging' ? 'selected' : '' }}>Daging</option>
                                        <option value="Tulang" {{ $kat == 'Tulang' ? 'selected' : '' }}>Tulang</option>
                                        <option value="Buntut" {{ $kat == 'Buntut' ? 'selected' : '' }}>Buntut</option>
                                        <option value="Jeroan" {{ $kat == 'Jeroan' ? 'selected' : '' }}>Jeroan</option>
                                        <option value="Kaki" {{ $kat == 'Kaki' ? 'selected' : '' }}>Kaki</option>
                                    </select>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="number" name="harga_awal" value="{{ old('id_produk') == $p->id_produk ? old('harga_awal') : $p->harga_awal }}" required class="text-xs border p-2 rounded focus:outline-none focus:border-red-500">
                                        <input type="number" name="harga_jual" value="{{ old('id_produk') == $p->id_produk ? old('harga_jual') : $p->harga_jual }}" required class="text-xs border p-2 rounded focus:outline-none focus:border-red-500">
                                    </div>
                                    <input type="text" name="nama_jagal" value="{{ old('id_produk') == $p->id_produk ? old('nama_jagal') : $p->nama_jagal }}" required class="w-full text-xs border p-2 rounded focus:outline-none focus:border-red-500">
                                    
                                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold text-xs hover:bg-blue-700 mt-2">Simpan Perubahan</button>
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
                <button @click="openAdd = true" class="w-full bg-red-600 text-white text-xs py-2.5 rounded-lg font-bold shadow-sm hover:bg-red-700 flex justify-center items-center">
                    <span class="mr-1.5 text-base leading-none">+</span> Tambah Pelanggan Baru
                </button>

                <!-- MODAL TAMBAH PELANGGAN -->
                <div x-show="openAdd" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 p-4">
                    <div @click.away="openAdd = false" class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                        <div class="flex justify-between items-center border-b border-gray-100 p-3">
                            <h3 class="text-sm font-bold text-gray-700">Tambah Pelanggan Baru</h3>
                            <button @click="openAdd = false" type="button" class="text-gray-400 hover:text-red-600 font-bold text-lg">&times;</button>
                        </div>
                        <form action="{{ route('master.pelanggan.store') }}" method="POST" class="p-4 space-y-3">
                            @csrf
                            <input type="hidden" name="form_type" value="create_pelanggan">
                            
                            <!-- ERROR MUNCUL DI DALAM MODAL SINI -->
                            @if(old('form_type') == 'create_pelanggan' && $errors->any())
                                <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded text-[11px] mb-2">
                                    <ul class="list-disc pl-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <input type="text" name="nama_pelanggan" value="{{ old('form_type') == 'create_pelanggan' ? old('nama_pelanggan') : '' }}" placeholder="Nama Pelanggan" required class="w-full text-xs border p-2 rounded focus:outline-none focus:border-red-500">
                            <input type="text" name="no_telp" value="{{ old('form_type') == 'create_pelanggan' ? old('no_telp') : '' }}" placeholder="No Telp (Cth: 08123456789)" required class="w-full text-xs border p-2 rounded focus:outline-none focus:border-red-500">
                            <textarea name="alamat" placeholder="Alamat Lengkap" rows="3" required class="w-full text-xs border p-2 rounded focus:outline-none focus:border-red-500">{{ old('form_type') == 'create_pelanggan' ? old('alamat') : '' }}</textarea>
                            
                            <button type="submit" class="w-full bg-red-600 text-white text-xs py-2 rounded-lg font-semibold hover:bg-red-700 mt-2">Simpan Pelanggan</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Daftar Pelanggan -->
            <div>
                <h3 class="text-xs font-bold text-gray-600 mb-2">Daftar Pelanggan</h3>
                @foreach($pelanggan as $pl)
                    <div x-data="{ openEdit: {{ old('id_pelanggan') == $pl->id_pelanggan && $errors->any() ? 'true' : 'false' }} }" class="bg-white border rounded p-3 text-xs shadow-sm mb-2 flex justify-between items-center">
                        
                        <div>
                            <span class="font-bold text-blue-600">{{ $pl->nama_pelanggan }}</span>
                            <p class="text-gray-500 mt-0.5">{{ $pl->no_telp }} - {{ $pl->alamat }}</p>
                        </div>
                        <div class="flex space-x-1.5">
                            <button @click="openEdit = true" type="button" class="text-blue-600 font-bold px-2.5 py-1 bg-blue-50 rounded hover:bg-blue-100">Edit</button>
                            <form action="{{ route('master.pelanggan.delete', $pl->id_pelanggan) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 font-bold px-2.5 py-1 bg-red-50 rounded hover:bg-red-100">Hapus</button>
                            </form>
                        </div>

                        <!-- MODAL EDIT PELANGGAN -->
                        <div x-show="openEdit" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 p-4">
                            <div @click.away="openEdit = false" class="bg-white rounded-xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
                                <div class="flex justify-between items-center border-b border-gray-100 p-3">
                                    <h3 class="text-sm font-bold text-gray-700">Edit Pelanggan</h3>
                                    <button @click="openEdit = false" type="button" class="text-gray-400 hover:text-red-600 font-bold text-lg">&times;</button>
                                </div>
                                <form action="{{ route('master.pelanggan.update', $pl->id_pelanggan) }}" method="POST" class="p-4 space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="id_pelanggan" value="{{ $pl->id_pelanggan }}">
                                    
                                    <!-- ERROR MUNCUL DI DALAM MODAL SINI -->
                                    @if(old('id_pelanggan') == $pl->id_pelanggan && $errors->any())
                                        <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded text-[11px] mb-2">
                                            <ul class="list-disc pl-3">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <input type="text" name="nama_pelanggan" value="{{ old('id_pelanggan') == $pl->id_pelanggan ? old('nama_pelanggan') : $pl->nama_pelanggan }}" required class="w-full text-xs border p-2 rounded focus:outline-none focus:border-red-500">
                                    <input type="text" name="no_telp" value="{{ old('id_pelanggan') == $pl->id_pelanggan ? old('no_telp') : $pl->no_telp }}" required class="w-full text-xs border p-2 rounded focus:outline-none focus:border-red-500">
                                    <textarea name="alamat" rows="3" required class="w-full text-xs border p-2 rounded focus:outline-none focus:border-red-500">{{ old('id_pelanggan') == $pl->id_pelanggan ? old('alamat') : $pl->alamat }}</textarea>
                                    
                                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-semibold text-xs hover:bg-blue-700 mt-2">Simpan Perubahan</button>
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