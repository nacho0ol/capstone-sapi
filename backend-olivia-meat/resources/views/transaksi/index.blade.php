@extends('layouts.app')

@section('content')
<div x-data="{ 
    showForm: {{ $errors->any() ? 'true' : 'false' }}, 
    selectedPelanggan: '{{ old('id_pelanggan', '') }}', 
    showPelangganForm: false, 
    metodeBayar: '{{ old('metode_bayar', 'Tunai') }}', 
    items: {{ Js::from(old('items', [['id_produk' => '', 'qty' => 1]])) }}, 
    produks: {{ Js::from($produks) }}, 
    pelanggans: {{ Js::from($pelanggans) }}, 
    newPelanggan: {nama_pelanggan: '', no_telp: '', alamat: ''},

    // Format Ribuan DP / Awal
    dpRaw: '{{ old('jumlah_terbayar', 0) }}',
    dpFormatted: '',

    formatRupiah(val) {
        let number = String(val).replace(/\D/g, '');
        if (!number) return { raw: '', formatted: '' };
        let formatted = new Intl.NumberFormat('id-ID').format(number);
        return { raw: number, formatted: formatted };
    },

    onDpInput(e) {
        let res = this.formatRupiah(e.target.value);
        this.dpRaw = res.raw;
        this.dpFormatted = res.formatted;
    },

    init() {
        if (this.dpRaw && this.dpRaw != '0') {
            this.dpFormatted = this.formatRupiah(this.dpRaw).formatted;
        }
    }
}" class="space-y-4">

    <!-- Header Halaman -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Daftar Pesanan</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola data dan status pengiriman pesanan</p>
        </div>
        <button @click="showForm = true" 
                class="inline-flex items-center gap-1.5 bg-[#842529] hover:bg-red-900 text-white px-3.5 py-2 rounded-xl text-xs font-bold shadow-sm transition-all active:scale-95">
            <x-lucide-plus-circle class="w-4 h-4 text-white" />
            <span>Tambah</span>
        </button>
    </div>

    <!-- MODAL BUAT PESANAN BARU -->
    <div x-show="showForm" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex justify-center items-center p-4" style="display: none;">
        <div @click.away="showForm = false" class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto p-5 shadow-xl relative border border-border-muted">
            <button @click="showForm = false" class="absolute top-4 right-4 text-gray-400 hover:text-[#842529] p-1 rounded-lg transition-colors">
                <x-lucide-x class="w-5 h-5" />
            </button>
            
            <h3 class="font-bold text-gray-900 text-sm mb-4 border-b border-border-muted pb-3 flex items-center gap-2">
                <x-lucide-shopping-bag class="w-4 h-4 text-[#842529]" />
                <span>Buat Pesanan Baru</span>
            </h3>
            
            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl text-xs mb-3 space-y-1">
                    <div class="font-bold flex items-center gap-1.5">
                        <x-lucide-alert-circle class="w-4 h-4 text-red-600 flex-shrink-0" />
                        <span>Gagal Menyimpan Pesanan:</span>
                    </div>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('transaksi.store') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="id_user" value="{{ auth()->id() ?? 1 }}"> 
                
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Order</label>
                        <input type="date" name="tgl_order" min="2015-01-01" value="{{ old('tgl_order', date('Y-m-d')) }}" 
                               class="w-full text-xs bg-gray-50 border {{ $errors->has('tgl_order') ? 'border-red-500' : 'border-border-muted' }} p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                        @error('tgl_order')
                            <p class="text-[10px] text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal Antar</label>
                        <input type="date" name="tgl_antar" min="2015-01-01" value="{{ old('tgl_antar') }}" 
                               class="w-full text-xs bg-gray-50 border {{ $errors->has('tgl_antar') ? 'border-red-500' : 'border-border-muted' }} p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                        @error('tgl_antar')
                            <p class="text-[10px] text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Pelanggan</label>
                    <select name="id_pelanggan" x-model="selectedPelanggan" @change="if(selectedPelanggan === 'new') { showPelangganForm = true; selectedPelanggan = ''; }" 
                            class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                        <option value="">-- Pilih Pelanggan --</option>
                        <template x-for="plg in pelanggans" :key="plg.id_pelanggan">
                            <option :value="String(plg.id_pelanggan)" :selected="String(selectedPelanggan) === String(plg.id_pelanggan)" x-text="plg.nama_pelanggan + ' - ' + plg.no_telp"></option>
                        </template>
                        <option value="new" class="font-bold text-[#842529]">+ Tambah Pelanggan Baru</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Metode Bayar</label>
                    <select name="metode_bayar" x-model="metodeBayar" 
                            class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                        <option value="Tunai">Tunai</option>
                        <option value="Transfer">Transfer</option>
                        <option value="Tempo">Tempo (Piutang)</option>
                    </select>
                </div>

                <!-- Box Tempo & Piutang dengan Format Ribuan Titik -->
                <div x-show="metodeBayar === 'Tempo'" x-cloak class="bg-sky-50 border border-sky-200 p-3 rounded-xl space-y-2" x-transition>
                    <h4 class="text-[11px] font-bold text-sky-900 uppercase tracking-wider flex items-center gap-1">
                        <x-lucide-receipt class="w-3.5 h-3.5 text-sky-700" />
                        <span>Data Piutang & DP</span>
                    </h4>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] text-sky-800 font-semibold mb-1">Jatuh Tempo</label>
                            <input type="date" name="tgl_jatuh_tempo" min="2015-01-01" value="{{ old('tgl_jatuh_tempo', date('Y-m-d', strtotime('+7 days'))) }}" 
                                   class="w-full text-xs bg-white border border-sky-300 rounded-xl p-2 focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-[10px] text-sky-800 font-semibold mb-1">DP / Awal (Rp)</label>
                            <input type="text" 
                                   x-model="dpFormatted" 
                                   @input="onDpInput($event)" 
                                   placeholder="0" 
                                   class="w-full text-xs bg-white border border-sky-300 rounded-xl p-2 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <input type="hidden" name="jumlah_terbayar" :value="dpRaw">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan (Opsional)</label>
                    <textarea name="catatan" rows="2" 
                              class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" 
                              placeholder="Contoh: Potong dadu kecil-kecil...">{{ old('catatan') }}</textarea>
                </div>

                <div class="border-t border-border-muted pt-3 mt-2">
                    <label class="block text-xs font-semibold text-gray-700 mb-2">Item Produk</label>
                    
                    <template x-for="(item, index) in items" :key="index">
                        <div class="flex items-center gap-2 mb-2">
                            <select :name="'items['+index+'][id_produk]'" x-model="item.id_produk" 
                                     class="flex-1 text-xs bg-gray-50 border border-border-muted p-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]" required>
                                <option value="">Pilih Produk</option>
                                <template x-for="p in produks" :key="p.id_produk">
                                    <option :value="String(p.id_produk)" :selected="String(item.id_produk) === String(p.id_produk)" x-text="p.nama_produk + ' (Rp ' + Number(p.harga_jual).toLocaleString('id-ID') + '/kg)'"></option>
                                </template>
                            </select>
                            <input type="number" step="0.01" min="0.01" :name="'items['+index+'][qty]'" x-model="item.qty" 
                                   class="w-20 text-xs bg-gray-50 border border-border-muted p-2 rounded-xl text-center focus:outline-none focus:ring-2 focus:ring-[#842529]" placeholder="Qty (kg)" required>
                            <button type="button" @click="items.splice(index, 1)" x-show="items.length > 1" 
                                    class="text-rose-600 bg-rose-50 border border-rose-200 p-2 rounded-xl hover:bg-rose-100 transition-colors">
                                <x-lucide-trash-2 class="w-4 h-4" />
                            </button>
                        </div>
                    </template>
                    <button type="button" @click="items.push({id_produk: '', qty: 1})" 
                            class="inline-flex items-center gap-1 text-xs font-bold text-[#842529] bg-[#FAE2E2]/60 hover:bg-[#FAE2E2] px-3 py-1.5 rounded-xl transition-all border border-[#842529]/10">
                        <x-lucide-plus class="w-3.5 h-3.5" />
                        <span>Tambah Item</span>
                    </button>
                </div>

                <div class="pt-3 flex gap-2">
                    <button type="button" @click="showForm = false" class="flex-1 border border-border-muted text-gray-700 py-2.5 rounded-xl font-bold text-xs hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 bg-[#842529] hover:bg-red-900 text-white py-2.5 rounded-xl font-bold text-xs shadow-md">Simpan Pesanan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL QUICK TAMBAH PELANGGAN -->
    <div x-show="showPelangganForm" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] flex justify-center items-center p-4" style="display: none;">
        <div @click.away="showPelangganForm = false" class="bg-white rounded-2xl w-full max-w-sm p-5 shadow-xl relative border border-border-muted">
            <button @click="showPelangganForm = false" class="absolute top-4 right-4 text-gray-400 hover:text-[#842529] p-1 rounded-lg transition-colors">
                <x-lucide-x class="w-5 h-5" />
            </button>
            <h3 class="font-bold text-gray-900 text-sm mb-3 border-b border-border-muted pb-2 flex items-center gap-2">
                <x-lucide-user-plus class="w-4 h-4 text-[#842529]" />
                <span>Tambah Pelanggan Baru</span>
            </h3>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Pelanggan</label>
                    <input type="text" x-model="newPelanggan.nama_pelanggan" class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]" placeholder="Cth: Pak Budi" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">No. Telp / WA</label>
                    <input type="text" x-model="newPelanggan.no_telp" class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]" placeholder="Cth: 08123456789" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat</label>
                    <textarea x-model="newPelanggan.alamat" rows="2" class="w-full text-xs bg-gray-50 border border-border-muted p-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#842529]" placeholder="Alamat rumah / toko" required></textarea>
                </div>

                <div class="pt-2 flex gap-2">
                    <button type="button" @click="showPelangganForm = false" class="flex-1 border border-border-muted text-gray-700 py-2.5 rounded-xl font-bold text-xs hover:bg-gray-50">Batal</button>
                    <button type="button" @click="
                        fetch('{{ route('master.pelanggan.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(newPelanggan)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if(data.data) {
                                pelanggans.push(data.data);
                                selectedPelanggan = String(data.data.id_pelanggan);
                                showPelangganForm = false;
                                newPelanggan = {nama_pelanggan: '', no_telp: '', alamat: ''};
                                alert('Pelanggan berhasil ditambahkan!');
                            } else {
                                alert(data.message || 'Gagal menyimpan pelanggan, cek validasi.');
                            }
                        })
                        .catch(err => alert('Terjadi kesalahan koneksi!'));
                    " class="flex-1 bg-[#842529] hover:bg-red-900 text-white py-2.5 rounded-xl font-bold text-xs shadow-md">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- LIST DATA PESANAN -->
    <div class="space-y-3">
        @forelse($pesanan as $p)
            @php
                $statusLower = strtolower($p->status_pemesanan);
                $isCancelled = in_array($statusLower, ['ditolak', 'dibatalkan']);
                $isCompleted = $statusLower === 'selesai';
                $statusLabel = $isCancelled ? 'Dibatalkan' : ucfirst($p->status_pemesanan);
                $badgeClass = $statusLower === 'diterima'
                    ? 'bg-sky-50 border-sky-200 text-sky-700'
                    : ($isCompleted
                        ? 'bg-emerald-50 border-emerald-200 text-emerald-700'
                        : ($isCancelled
                            ? 'bg-rose-50 border-rose-200 text-rose-700'
                            : 'bg-amber-50 border-amber-200 text-amber-700'));
            @endphp
            <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm hover:border-gray-300 transition-all flex justify-between items-start gap-3 {{ $isCancelled ? 'opacity-75' : '' }}">
                <div class="flex-1 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-[#842529] font-mono">{{ $p->id_pesanan }}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md border {{ $badgeClass }}">
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div>
                        <h2 class="text-sm font-bold text-gray-900">
                            {{ $p->pelanggan->nama_pelanggan ?? 'Umum' }}
                            @if(!empty($p->pelanggan->no_telp))
                                <span class="text-xs font-normal text-gray-500">({{ $p->pelanggan->no_telp }})</span>
                            @endif
                        </h2>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 mt-0.5">
                            <span class="flex items-center gap-1">
                                <x-lucide-calendar class="w-3.5 h-3.5 text-gray-400" />
                                <span>Order: {{ $p->tgl_order }}</span>
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1">
                                <x-lucide-truck class="w-3.5 h-3.5 text-gray-400" />
                                <span>Antar: {{ $p->tgl_antar }}</span>
                            </span>
                            <span>•</span>
                            <span class="flex items-center gap-1">
                                <x-lucide-credit-card class="w-3.5 h-3.5 text-gray-400" />
                                <span>{{ $p->metode_bayar }}</span>
                            </span>
                        </div>
                    </div>
                    
                    @if($p->catatan)
                        <p class="text-[11px] italic text-amber-800 bg-amber-50 border border-amber-100 p-2 rounded-xl">
                            <strong>Catatan:</strong> {{ $p->catatan }}
                        </p>
                    @endif

                    <div class="text-xs text-gray-500 pt-2 border-t border-border-muted flex flex-wrap gap-1">
                        @foreach($p->detail as $det)
                            <span class="bg-gray-50 border border-border-muted px-2 py-0.5 rounded-md text-[11px]">
                                {{ $det->produk->nama_produk ?? 'Produk Terhapus' }} (<strong>{{ $det->qty }}kg</strong>)
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Right Status & Action -->
                <div class="flex flex-col items-end justify-between self-stretch pl-3 border-l border-border-muted space-y-2 min-w-[90px]">
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-md border w-full text-center {{ $p->status_bayar == 'Lunas' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : ($p->status_bayar == 'DP' ? 'bg-sky-50 border-sky-200 text-sky-700' : 'bg-amber-50 border-amber-200 text-amber-700') }}">
                        {{ $p->status_bayar }}
                    </span>

                    @if(!$isCancelled && !$isCompleted)
                        <div class="w-full space-y-1.5">
                            <a href="{{ route('transaksi.edit', $p->id_pesanan) }}" 
                               class="inline-flex items-center justify-center gap-1 text-[10px] bg-amber-50 text-amber-700 hover:bg-amber-100 px-2.5 py-1.5 rounded-xl font-bold w-full text-center border border-amber-200 transition-colors">
                                <x-lucide-square-pen class="w-3.5 h-3.5" />
                                <span>Edit</span>
                            </a>

                            <!-- Tombol Batal Menggunakan Global Dynamic Confirm Modal -->
                            <button type="button" 
                                    @click="$dispatch('confirm-action', { 
                                        action: '{{ route('transaksi.cancel', $p->id_pesanan) }}', 
                                        title: 'Batalkan Pesanan', 
                                        message: 'Apakah Anda yakin ingin membatalkan pesanan #{{ $p->id_pesanan }}?', 
                                        method: 'POST',
                                        confirmText: 'Ya, Batalkan',
                                        confirmClass: 'bg-rose-600 hover:bg-rose-700'
                                    })"
                                    class="inline-flex items-center justify-center gap-1 text-[10px] bg-rose-50 text-rose-700 hover:bg-rose-100 px-2.5 py-1.5 rounded-xl font-bold w-full text-center border border-rose-200 transition-colors">
                                <x-lucide-x-circle class="w-3.5 h-3.5" />
                                <span>Batal</span>
                            </button>
                        </div>
                    @elseif($isCancelled)
                        <span class="inline-flex items-center justify-center gap-1 text-[10px] bg-gray-100 text-gray-400 px-2.5 py-1.5 rounded-xl font-bold w-full text-center border border-gray-200 cursor-not-allowed" title="Pesanan sudah dibatalkan">
                            <x-lucide-ban class="w-3.5 h-3.5" />
                            <span>Dibatalkan</span>
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center bg-white rounded-2xl border border-border-muted py-12 shadow-sm">
                <x-lucide-shopping-bag class="w-10 h-10 text-gray-300 mx-auto mb-2" />
                <p class="text-gray-400 text-xs">Belum ada data pesanan.</p>
                <button @click="showForm = true" class="mt-2 text-[#842529] font-bold text-xs hover:underline">Buat Pesanan Pertama</button>
            </div>
        @endforelse
    </div>
</div>
@endsection