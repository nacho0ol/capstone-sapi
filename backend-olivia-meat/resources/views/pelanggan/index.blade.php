@extends('layouts.app')

@section('content')
<div class="space-y-5">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Data Pelanggan</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola daftar pelanggan, riwayat orderan, pembayaran tagihan, dan komplain</p>
        </div>
    </div>

    <!-- Form Search Pelanggan -->
    <form action="{{ route('pelanggan.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-2">
        <div class="relative w-full sm:flex-1">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <x-lucide-search class="w-4 h-4" />
            </span>
            <input type="text" 
                   name="search_pelanggan" 
                   value="{{ request('search_pelanggan') }}" 
                   placeholder="Cari nama, no. telepon, atau alamat..." 
                   class="w-full pl-9 pr-4 py-2.5 bg-white border border-border-muted rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#842529]">
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="submit" class="w-full sm:w-auto bg-[#842529] hover:bg-red-900 text-white text-xs px-4 py-2.5 rounded-xl font-semibold shadow-sm transition-all active:scale-95 flex items-center justify-center gap-1.5">
                <x-lucide-search class="w-3.5 h-3.5" />
                <span>Cari</span>
            </button>

            @if(request()->filled('search_pelanggan'))
                <a href="{{ route('pelanggan.index') }}" 
                   class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs px-3 py-2.5 rounded-xl font-semibold transition-all flex items-center justify-center gap-1">
                    <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                    <span>Reset</span>
                </a>
            @endif
        </div>
    </form>

    <!-- Tombol & Modal Tambah Pelanggan -->
    <div x-data="{ openAdd: {{ old('form_type') == 'create_pelanggan' && $errors->any() ? 'true' : 'false' }} }">
        <button @click="openAdd = true" 
                class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-[#842529] hover:bg-red-900 text-white text-xs py-2.5 px-4 rounded-xl font-semibold shadow-sm transition-all active:scale-95">
            <x-lucide-user-plus class="w-4 h-4 text-white" />
            <span>Tambah Pelanggan Baru</span>
        </button>

        <div x-show="openAdd" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div @click.away="openAdd = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-border-muted">
                <div class="flex justify-between items-center border-b border-border-muted p-4">
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <x-lucide-user-plus class="w-4 h-4 text-[#842529]" />
                        <span>Tambah Pelanggan Baru</span>
                    </h3>
                    <button @click="openAdd = false" type="button" class="text-gray-400 hover:text-[#842529] p-1 rounded-lg">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>
                <form action="{{ route('pelanggan.store') }}" method="POST" class="p-4 space-y-3">
                    @csrf
                    <input type="hidden" name="form_type" value="create_pelanggan">
                    
                    @if(old('form_type') == 'create_pelanggan' && $errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl text-xs space-y-1">
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
                               class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">No. Telepon / WhatsApp</label>
                        <input type="text" name="no_telp" value="{{ old('form_type') == 'create_pelanggan' ? old('no_telp') : '' }}" placeholder="Cth: 08123456789" required 
                               class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" placeholder="Alamat rumah / toko / warung" rows="3" required 
                                  class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">{{ old('form_type') == 'create_pelanggan' ? old('alamat') : '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Batas Toleransi Jatuh Tempo (Hari)</label>
                        <input type="number" name="batas_toleransi_khusus" min="0" value="{{ old('form_type') == 'create_pelanggan' ? old('batas_toleransi_khusus') : '' }}" placeholder="Opsional (Kosongkan = Ikuti Aturan Toko)" 
                               class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                    </div>
                    
                    <button type="submit" class="w-full bg-[#842529] hover:bg-red-900 text-white text-xs py-2.5 rounded-xl font-bold shadow-md mt-2 flex items-center justify-center gap-2">
                        <x-lucide-save class="w-4 h-4" />
                        <span>Simpan Pelanggan</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Daftar Pelanggan -->
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Daftar Pelanggan Terdaftar</h3>
        @forelse($pelanggan as $pl)
            <div x-data="{ openEdit: {{ old('id_pelanggan') == $pl->id_pelanggan && $errors->any() ? 'true' : 'false' }} }" 
                 class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm hover:border-gray-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <x-lucide-user class="w-4 h-4 text-[#842529]" />
                        <a href="{{ route('pelanggan.show', $pl->id_pelanggan) }}" class="font-bold text-gray-900 hover:text-[#842529] text-sm">
                            {{ $pl->nama_pelanggan }}
                        </a>
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
                        <span class="text-gray-300">•</span>
                        <span class="flex items-center gap-1">
                            <x-lucide-clock class="w-3.5 h-3.5 text-gray-400" />
                            <span>Toleransi: 
                                @if($pl->batas_toleransi_khusus !== null)
                                    <strong class="text-[#842529] font-bold">{{ $pl->batas_toleransi_khusus }} Hari</strong>
                                @else
                                    <span class="text-gray-500 italic">Default Toko</span>
                                @endif
                            </span>
                        </span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-border-muted justify-end">
                    <a href="{{ route('pelanggan.show', $pl->id_pelanggan) }}" 
                       class="inline-flex items-center gap-1 text-[#842529] font-bold px-3 py-1.5 bg-[#FAE2E2]/70 border border-red-200 rounded-xl hover:bg-[#FAE2E2] transition-colors text-xs">
                        <x-lucide-history class="w-3.5 h-3.5" />
                        <span>Riwayat & Komplain</span>
                    </a>

                    <button @click="openEdit = true" type="button" 
                            class="inline-flex items-center gap-1 text-amber-700 font-semibold px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition-colors text-xs">
                        <x-lucide-square-pen class="w-3.5 h-3.5" />
                        <span>Edit</span>
                    </button>

                    <button type="button" 
                            @click="$dispatch('confirm-action', { 
                                action: '{{ route('pelanggan.destroy', $pl->id_pelanggan) }}', 
                                title: 'Hapus Pelanggan', 
                                message: 'Apakah Anda yakin ingin menghapus pelanggan {{ $pl->nama_pelanggan }}?', 
                                method: 'DELETE',
                                confirmText: 'Ya, Hapus',
                                confirmClass: 'bg-rose-600 hover:bg-rose-700'
                            })"
                            class="inline-flex items-center gap-1 text-rose-700 font-semibold px-3 py-1.5 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition-colors text-xs">
                        <x-lucide-trash-2 class="w-3.5 h-3.5" />
                        <span>Hapus</span>
                    </button>
                </div>

                <!-- MODAL EDIT PELANGGAN -->
                <div x-show="openEdit" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div @click.away="openEdit = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-border-muted text-left">
                        <div class="flex justify-between items-center border-b border-border-muted p-4">
                            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <x-lucide-square-pen class="w-4 h-4 text-amber-600" />
                                <span>Edit Pelanggan</span>
                            </h3>
                            <button @click="openEdit = false" type="button" class="text-gray-400 hover:text-[#842529] p-1 rounded-lg">
                                <x-lucide-x class="w-5 h-5" />
                            </button>
                        </div>
                        <form action="{{ route('pelanggan.update', $pl->id_pelanggan) }}" method="POST" class="p-4 space-y-3">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id_pelanggan" value="{{ $pl->id_pelanggan }}">
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Pelanggan</label>
                                <input type="text" name="nama_pelanggan" value="{{ old('id_pelanggan') == $pl->id_pelanggan ? old('nama_pelanggan') : $pl->nama_pelanggan }}" required 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">No. Telepon / WhatsApp</label>
                                <input type="text" name="no_telp" value="{{ old('id_pelanggan') == $pl->id_pelanggan ? old('no_telp') : $pl->no_telp }}" required 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Lengkap</label>
                                <textarea name="alamat" rows="3" required 
                                          class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">{{ old('id_pelanggan') == $pl->id_pelanggan ? old('alamat') : $pl->alamat }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Batas Toleransi Jatuh Tempo (Hari)</label>
                                <input type="number" name="batas_toleransi_khusus" min="0" value="{{ old('id_pelanggan') == $pl->id_pelanggan ? old('batas_toleransi_khusus') : $pl->batas_toleransi_khusus }}" placeholder="Opsional" 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
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
                <p class="text-gray-400 text-xs">Belum ada data pelanggan terdaftar.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection