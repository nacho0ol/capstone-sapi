@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Stok Barang</h1>
            <p class="text-xs text-gray-500 mt-0.5">Monitoring jenis daging, berat stok masuk, dan tanggal keluar stok</p>
        </div>

        <!-- Modal Tambah Stok Barang -->
        <div x-data="{
            openAdd: {{ $errors->any() ? 'true' : 'false' }},
            statusStok: '{{ old('status_stok', 'Tersedia') }}'
        }">
            <button @click="openAdd = true" 
                    class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-[#842529] hover:bg-red-900 text-white text-xs py-2.5 px-4 rounded-xl font-bold shadow-sm transition-all active:scale-95">
                <x-lucide-plus-circle class="w-4 h-4 text-white" />
                <span>Tambah Stok Barang</span>
            </button>

            <div x-show="openAdd" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div @click.away="openAdd = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-border-muted">
                    <div class="flex justify-between items-center border-b border-border-muted p-4">
                        <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                            <x-lucide-package class="w-4 h-4 text-[#842529]" />
                            <span>Tambah Stok Barang</span>
                        </h3>
                        <button @click="openAdd = false" type="button" class="text-gray-400 hover:text-[#842529] p-1 rounded-lg">
                            <x-lucide-x class="w-5 h-5" />
                        </button>
                    </div>

                    <form action="{{ route('stok.store') }}" method="POST" class="p-4 space-y-3">
                        @csrf

                        @if($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl text-xs">
                                <ul class="list-disc pl-5 space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Jenis Daging</label>
                            <input type="text" name="jenis_daging" list="listJenisDaging" value="{{ old('jenis_daging') }}" placeholder="Pilih atau ketik jenis daging..." required 
                                   class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                            <datalist id="listJenisDaging">
                                @foreach($produks as $pr)
                                    <option value="{{ $pr->nama_produk }}">
                                @endforeach
                            </datalist>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Berat (kg)</label>
                                <input type="number" step="0.01" min="0.01" name="berat" value="{{ old('berat') }}" placeholder="Cth: 15.5" required 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Status Stok</label>
                                <select name="status_stok" x-model="statusStok" class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                                    <option value="Tersedia">Belum Keluar (Tersedia)</option>
                                    <option value="Sudah Keluar">Sudah Keluar</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Stok Masuk</label>
                                <input type="date" name="tgl_stok" value="{{ old('tgl_stok', date('Y-m-d')) }}" required 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Keluar Stok</label>
                                <input type="date" name="tgl_keluar_stok" value="{{ old('tgl_keluar_stok') }}" :disabled="statusStok === 'Tersedia'"
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] disabled:opacity-50">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea name="catatan" rows="2" placeholder="Keterangan lokasi freezer, batch, dll..." 
                                      class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">{{ old('catatan') }}</textarea>
                        </div>

                        <button type="submit" class="w-full bg-[#842529] hover:bg-red-900 text-white text-xs py-2.5 rounded-xl font-bold shadow-md flex items-center justify-center gap-2">
                            <x-lucide-save class="w-4 h-4" />
                            <span>Simpan Stok</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Total Berat Tersedia -->
    <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Total Berat Stok Tersedia (Belum Keluar)</p>
            <p class="text-xl font-extrabold text-[#842529]">{{ number_format($totalBeratTersedia, 2, ',', '.') }} kg</p>
        </div>
        <div class="w-11 h-11 bg-[#FAE2E2] rounded-xl flex items-center justify-center text-[#842529] shrink-0">
            <x-lucide-package class="w-5 h-5" />
        </div>
    </div>

    <!-- Form Filter Compact -->
    <form action="{{ route('stok.index') }}" method="GET" class="bg-white border border-border-muted p-3 rounded-2xl shadow-sm space-y-2">
        <div class="flex items-center gap-2">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <x-lucide-search class="w-3.5 h-3.5" />
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari jenis daging atau catatan..." 
                       class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-border-muted rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#842529]">
            </div>

            <button type="submit" class="bg-[#842529] hover:bg-red-900 text-white text-xs px-4 py-2 rounded-xl font-bold flex items-center gap-1.5 shadow-sm shrink-0">
                <x-lucide-filter class="w-3.5 h-3.5" />
                <span>Filter</span>
            </button>

            @if(request()->anyFilled(['search', 'filter_status', 'filter_tgl_stok']))
                <a href="{{ route('stok.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs px-3 py-2 rounded-xl font-semibold flex items-center gap-1 shrink-0">
                    <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                    <span class="hidden sm:inline">Reset</span>
                </a>
            @endif
        </div>

        <div class="grid grid-cols-2 gap-2">
            <select name="filter_status" class="w-full bg-gray-50 border border-border-muted rounded-xl px-3 py-2 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#842529]">
                <option value="">Semua Status</option>
                <option value="Tersedia" {{ request('filter_status') == 'Tersedia' ? 'selected' : '' }}>Belum Keluar (Tersedia)</option>
                <option value="Sudah Keluar" {{ request('filter_status') == 'Sudah Keluar' ? 'selected' : '' }}>Sudah Keluar</option>
            </select>

            <input type="date" name="filter_tgl_stok" value="{{ request('filter_tgl_stok') }}" 
                   class="w-full bg-gray-50 border border-border-muted rounded-xl px-3 py-2 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#842529]">
        </div>
    </form>

    <!-- Daftar Stok Barang -->
    <div class="space-y-3">
        @forelse($stokBarangs as $st)
            @php $isKeluar = $st->status_stok === 'Sudah Keluar'; @endphp
            <div x-data="{ openEdit: false, editStatus: '{{ $st->status_stok }}' }"
                 class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-bold text-gray-900 text-sm">{{ $st->jenis_daging }}</span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#FAE2E2] text-[#842529]">
                            {{ number_format($st->berat, 2, ',', '.') }} kg
                        </span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $isKeluar ? 'bg-gray-100 border-gray-200 text-gray-600' : 'bg-emerald-50 border-emerald-200 text-emerald-700' }}">
                            {{ $isKeluar ? 'Sudah Keluar' : 'Belum Keluar (Tersedia)' }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                        <span class="flex items-center gap-1">
                            <x-lucide-calendar class="w-3.5 h-3.5 text-gray-400" />
                            <span>Tgl Stok: <strong class="text-gray-700">{{ date('d M Y', strtotime($st->tgl_stok)) }}</strong></span>
                        </span>
                        <span>•</span>
                        <span>
                            Tgl Keluar: 
                            <strong class="text-gray-700">{{ $st->tgl_keluar_stok ? date('d M Y', strtotime($st->tgl_keluar_stok)) : '-' }}</strong>
                        </span>
                        @if($st->catatan)
                            <span>•</span>
                            <span class="italic text-gray-500">"{{ $st->catatan }}"</span>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-border-muted justify-end">
                    @if(!$isKeluar)
                        <form action="{{ route('stok.keluar', $st->id_stok) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="inline-flex items-center gap-1 text-emerald-700 font-semibold px-3 py-1.5 bg-emerald-50 border border-emerald-200 rounded-xl hover:bg-emerald-100 text-xs">
                                <x-lucide-check-circle-2 class="w-3.5 h-3.5" />
                                <span>Tandai Keluar</span>
                            </button>
                        </form>
                    @endif

                    <button @click="openEdit = true" type="button" 
                            class="inline-flex items-center gap-1 text-amber-700 font-semibold px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 text-xs">
                        <x-lucide-square-pen class="w-3.5 h-3.5" />
                        <span>Edit</span>
                    </button>

                    <button type="button" 
                            @click="$dispatch('confirm-action', { 
                                action: '{{ route('stok.destroy', $st->id_stok) }}', 
                                title: 'Hapus Stok Barang', 
                                message: 'Yakin ingin menghapus catatan stok {{ $st->jenis_daging }}?', 
                                method: 'DELETE',
                                confirmText: 'Ya, Hapus',
                                confirmClass: 'bg-rose-600 hover:bg-rose-700'
                            })"
                            class="inline-flex items-center gap-1 text-rose-700 font-semibold px-3 py-1.5 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 text-xs">
                        <x-lucide-trash-2 class="w-3.5 h-3.5" />
                        <span>Hapus</span>
                    </button>
                </div>

                <!-- MODAL EDIT STOK -->
                <div x-show="openEdit" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div @click.away="openEdit = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-border-muted text-left">
                        <div class="flex justify-between items-center border-b border-border-muted p-4">
                            <h3 class="text-sm font-bold text-gray-800">Edit Stok Barang</h3>
                            <button @click="openEdit = false" type="button" class="text-gray-400 hover:text-[#842529] p-1">
                                <x-lucide-x class="w-5 h-5" />
                            </button>
                        </div>

                        <form action="{{ route('stok.update', $st->id_stok) }}" method="POST" class="p-4 space-y-3">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Jenis Daging</label>
                                <input type="text" name="jenis_daging" list="listJenisDaging" value="{{ $st->jenis_daging }}" required 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Berat (kg)</label>
                                    <input type="number" step="0.01" min="0.01" name="berat" value="{{ $st->berat }}" required 
                                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status Stok</label>
                                    <select name="status_stok" x-model="editStatus" class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl">
                                        <option value="Tersedia">Belum Keluar (Tersedia)</option>
                                        <option value="Sudah Keluar">Sudah Keluar</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Stok Masuk</label>
                                    <input type="date" name="tgl_stok" value="{{ $st->tgl_stok }}" required 
                                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl">
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Keluar Stok</label>
                                    <input type="date" name="tgl_keluar_stok" value="{{ $st->tgl_keluar_stok }}" :disabled="editStatus === 'Tersedia'"
                                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl disabled:opacity-50">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan</label>
                                <textarea name="catatan" rows="2" class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl">{{ $st->catatan }}</textarea>
                            </div>

                            <button type="submit" class="w-full bg-[#842529] hover:bg-red-900 text-white py-2.5 rounded-xl font-bold text-xs shadow-md">
                                Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center bg-white rounded-2xl border border-border-muted py-12 shadow-sm">
                <x-lucide-package class="w-10 h-10 text-gray-300 mx-auto mb-2" />
                <p class="text-gray-400 text-xs">Belum ada catatan stok barang.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection