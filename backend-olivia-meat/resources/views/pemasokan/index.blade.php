@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Pemasokan Daging</h1>
            <p class="text-xs text-gray-500 mt-0.5">Pencatatan stok masuk daging dari mitra jagal / RPH</p>
        </div>

        <!-- Tombol & Modal Catat Pemasokan -->
        <div x-data="{
            openAdd: {{ $errors->any() ? 'true' : 'false' }},
            produkList: {{ Js::from($produks->map(fn($p) => [
                'id' => $p->id_produk,
                'harga_awal' => $p->harga_awal,
                'id_jagal' => $p->id_jagal
            ])) }},
            selectedProduk: '{{ old('id_produk', '') }}',
            selectedJagal: '{{ old('id_jagal', '') }}',
            jumlah: '{{ old('jumlah', '') }}',
            totalRaw: '{{ old('total_harga', '') }}',
            totalFormatted: '',

            formatRupiah(val) {
                let number = String(val).replace(/\D/g, '');
                if (!number) return { raw: '', formatted: '' };
                return { raw: number, formatted: new Intl.NumberFormat('id-ID').format(number) };
            },

            onProdukChange() {
                let found = this.produkList.find(item => item.id == this.selectedProduk);
                if (found) {
                    if (found.id_jagal) this.selectedJagal = found.id_jagal;
                    this.hitungTotal();
                }
            },

            hitungTotal() {
                let found = this.produkList.find(item => item.id == this.selectedProduk);
                let qty = parseFloat(this.jumlah);
                if (found && !isNaN(qty) && qty > 0) {
                    let calc = Math.round(qty * found.harga_awal);
                    let res = this.formatRupiah(calc);
                    this.totalRaw = res.raw;
                    this.totalFormatted = res.formatted;
                }
            },

            init() {
                if (this.totalRaw) this.totalFormatted = this.formatRupiah(this.totalRaw).formatted;
            }
        }">
            <button @click="openAdd = true" 
                    class="w-full sm:w-auto inline-flex justify-center items-center gap-2 bg-[#842529] hover:bg-red-900 text-white text-xs py-2.5 px-4 rounded-xl font-bold shadow-sm transition-all active:scale-95">
                <x-lucide-plus-circle class="w-4 h-4" />
                <span>Catat Pemasokan Daging</span>
            </button>

            <!-- MODAL TAMBAH PEMASOKAN -->
            <div x-show="openAdd" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div @click.away="openAdd = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-border-muted">
                    <div class="flex justify-between items-center border-b border-border-muted p-4">
                        <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                            <x-lucide-truck class="w-4 h-4 text-[#842529]" />
                            <span>Catat Pemasokan Daging</span>
                        </h3>
                        <button @click="openAdd = false" type="button" class="text-gray-400 hover:text-[#842529] p-1 rounded-lg">
                            <x-lucide-x class="w-5 h-5" />
                        </button>
                    </div>

                    <form action="{{ route('pemasokan.store') }}" method="POST" class="p-4 space-y-3">
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
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Pemasokan</label>
                            <input type="date" name="tgl_pemasokan" value="{{ old('tgl_pemasokan', date('Y-m-d')) }}" required 
                                   class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Produk Daging</label>
                            <select name="id_produk" x-model="selectedProduk" @change="onProdukChange()" required 
                                    class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                                <option value="">-- Pilih Produk --</option>
                                @foreach($produks as $pr)
                                    <option value="{{ $pr->id_produk }}">
                                        {{ $pr->nama_produk }} (Modal: Rp {{ number_format($pr->harga_awal, 0, ',', '.') }}/kg)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Jagal / RPH</label>
                            <select name="id_jagal" x-model="selectedJagal" required 
                                    class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                                <option value="">-- Pilih Jagal --</option>
                                @foreach($jagals as $jg)
                                    <option value="{{ $jg->id_jagal }}">{{ $jg->nama_jagal }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Jumlah (kg) <span class="text-gray-400 font-normal">Maks 1000</span></label>
                                <input type="number" step="0.01" min="0.01" max="1000" name="jumlah" 
                                       x-model="jumlah" @input="hitungTotal()"
                                       placeholder="Cth: 25.5" required 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Total Harga</label>
                                <input type="text" 
                                       x-model="totalFormatted"
                                       @input="let res = formatRupiah($event.target.value); totalRaw = res.raw; totalFormatted = res.formatted;"
                                       placeholder="Rp 0" required 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">
                                <input type="hidden" name="total_harga" :value="totalRaw">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea name="catatan" rows="2" placeholder="Keterangan kualitas daging, waktu potong, dll..." 
                                      class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]">{{ old('catatan') }}</textarea>
                        </div>

                        <button type="submit" class="w-full bg-[#842529] hover:bg-red-900 text-white text-xs py-2.5 rounded-xl font-bold shadow-md flex items-center justify-center gap-2">
                            <x-lucide-save class="w-4 h-4" />
                            <span>Simpan Pemasokan</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Pemasokan (1 Kartu Total Nilai Pemasokan) -->
    <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Total Nilai Pemasokan</p>
            <p class="text-xl font-extrabold text-[#842529]">Rp {{ number_format($totalNominal, 0, ',', '.') }}</p>
        </div>
        <div class="w-11 h-11 bg-[#FAE2E2] rounded-xl flex items-center justify-center text-[#842529] shrink-0">
            <x-lucide-banknote class="w-5 h-5" />
        </div>
    </div>

    <!-- Form Filter Pemasokan (Compact 2 Baris) -->
    <form action="{{ route('pemasokan.index') }}" method="GET" class="bg-white border border-border-muted p-3 rounded-2xl shadow-sm space-y-2">
        <!-- Baris 1: Pencarian + Tombol Filter & Reset -->
        <div class="flex items-center gap-2">
            <div class="relative flex-1">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                    <x-lucide-search class="w-3.5 h-3.5" />
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk atau catatan..." 
                       class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-border-muted rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-[#842529]">
            </div>

            <button type="submit" class="bg-[#842529] hover:bg-red-900 text-white text-xs px-4 py-2 rounded-xl font-bold flex items-center gap-1.5 shadow-sm shrink-0 transition-all active:scale-95">
                <x-lucide-filter class="w-3.5 h-3.5" />
                <span>Filter</span>
            </button>

            @if(request()->anyFilled(['search', 'filter_jagal', 'filter_waktu', 'filter_tanggal']))
                <a href="{{ route('pemasokan.index') }}" title="Reset Filter"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs px-3 py-2 rounded-xl font-semibold flex items-center gap-1 shrink-0 transition-all">
                    <x-lucide-rotate-ccw class="w-3.5 h-3.5" />
                    <span class="hidden sm:inline">Reset</span>
                </a>
            @endif
        </div>

        <!-- Baris 2: Jagal, Rentang Waktu & Tanggal Spesifik -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            <select name="filter_jagal" class="w-full bg-gray-50 border border-border-muted rounded-xl px-3 py-2 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#842529]">
                <option value="">Semua Jagal</option>
                @foreach($jagals as $jg)
                    <option value="{{ $jg->id_jagal }}" {{ request('filter_jagal') == $jg->id_jagal ? 'selected' : '' }}>
                        {{ $jg->nama_jagal }}
                    </option>
                @endforeach
            </select>

            <select name="filter_waktu" class="w-full bg-gray-50 border border-border-muted rounded-xl px-3 py-2 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#842529]">
                <option value="">Semua Waktu</option>
                <option value="hari_ini" {{ request('filter_waktu') == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                <option value="minggu_ini" {{ request('filter_waktu') == 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                <option value="bulan_ini" {{ request('filter_waktu') == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
            </select>

            <input type="date" name="filter_tanggal" value="{{ request('filter_tanggal') }}" 
                   class="col-span-2 sm:col-span-1 w-full bg-gray-50 border border-border-muted rounded-xl px-3 py-2 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#842529]">
        </div>
    </form>

    <!-- Daftar Pemasokan -->
    <div class="space-y-3">
        @forelse($pemasokan as $pm)
            <div x-data="{
                openEdit: false,
                totalRaw: '{{ $pm->total_harga }}',
                totalFormatted: '',
                formatRupiah(val) {
                    let number = String(val).replace(/\D/g, '');
                    if (!number) return { raw: '', formatted: '' };
                    return { raw: number, formatted: new Intl.NumberFormat('id-ID').format(number) };
                },
                init() {
                    this.totalFormatted = this.formatRupiah(this.totalRaw).formatted;
                }
            }" class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-bold text-gray-900 text-sm">{{ $pm->produk->nama_produk ?? 'Produk Terhapus' }}</span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#FAE2E2] text-[#842529]">
                            {{ number_format($pm->jumlah, 2, ',', '.') }} kg
                        </span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-100 text-gray-700">
                            {{ $pm->jagal->nama_jagal ?? '-' }}
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                        <span class="flex items-center gap-1">
                            <x-lucide-calendar class="w-3.5 h-3.5 text-gray-400" />
                            <span>{{ date('d M Y', strtotime($pm->tgl_pemasokan)) }}</span>
                        </span>
                        <span>•</span>
                        <span>Total: <strong class="text-gray-800">Rp {{ number_format($pm->total_harga, 0, ',', '.') }}</strong></span>
                        @if($pm->catatan)
                            <span>•</span>
                            <span class="italic text-gray-500">"{{ $pm->catatan }}"</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-border-muted justify-end">
                    <button @click="openEdit = true" type="button" 
                            class="inline-flex items-center gap-1 text-amber-700 font-semibold px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 text-xs">
                        <x-lucide-square-pen class="w-3.5 h-3.5" />
                        <span>Edit</span>
                    </button>
                    <button type="button" 
                            @click="$dispatch('confirm-action', { 
                                action: '{{ route('pemasokan.destroy', $pm->id_pemasokan) }}', 
                                title: 'Hapus Pemasokan', 
                                message: 'Yakin ingin menghapus catatan pemasokan ini?', 
                                method: 'DELETE',
                                confirmText: 'Ya, Hapus',
                                confirmClass: 'bg-rose-600 hover:bg-rose-700'
                            })"
                            class="inline-flex items-center gap-1 text-rose-700 font-semibold px-3 py-1.5 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 text-xs">
                        <x-lucide-trash-2 class="w-3.5 h-3.5" />
                        <span>Hapus</span>
                    </button>
                </div>

                <!-- MODAL EDIT PEMASOKAN -->
                <div x-show="openEdit" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                    <div @click.away="openEdit = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-border-muted text-left">
                        <div class="flex justify-between items-center border-b border-border-muted p-4">
                            <h3 class="text-sm font-bold text-gray-800">Edit Pemasokan Daging</h3>
                            <button @click="openEdit = false" type="button" class="text-gray-400 hover:text-[#842529] p-1">
                                <x-lucide-x class="w-5 h-5" />
                            </button>
                        </div>

                        <form action="{{ route('pemasokan.update', $pm->id_pemasokan) }}" method="POST" class="p-4 space-y-3">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Pemasokan</label>
                                <input type="date" name="tgl_pemasokan" value="{{ $pm->tgl_pemasokan }}" required 
                                       class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Produk Daging</label>
                                <select name="id_produk" required class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl">
                                    @foreach($produks as $pr)
                                        <option value="{{ $pr->id_produk }}" {{ $pm->id_produk == $pr->id_produk ? 'selected' : '' }}>
                                            {{ $pr->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Jagal / RPH</label>
                                <select name="id_jagal" required class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl">
                                    @foreach($jagals as $jg)
                                        <option value="{{ $jg->id_jagal }}" {{ $pm->id_jagal == $jg->id_jagal ? 'selected' : '' }}>
                                            {{ $jg->nama_jagal }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Jumlah (kg)</label>
                                    <input type="number" step="0.01" min="0.01" max="1000" name="jumlah" value="{{ $pm->jumlah }}" required 
                                           class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1">Total Harga</label>
                                    <input type="text" x-model="totalFormatted"
                                           @input="let res = formatRupiah($event.target.value); totalRaw = res.raw; totalFormatted = res.formatted;"
                                           required class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl">
                                    <input type="hidden" name="total_harga" :value="totalRaw">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan</label>
                                <textarea name="catatan" rows="2" class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl">{{ $pm->catatan }}</textarea>
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
                <x-lucide-truck class="w-10 h-10 text-gray-300 mx-auto mb-2" />
                <p class="text-gray-400 text-xs">Belum ada catatan pemasokan daging.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection