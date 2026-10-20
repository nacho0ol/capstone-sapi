@extends('layouts.app')

@section('content')
<div x-data="{ showSetting: false }" class="space-y-6">
    
    <!-- Header Halaman & Tombol Pengaturan Notifikasi -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Keuangan & Laporan</h1>
            <p class="text-xs text-gray-500 mt-0.5">Analisis laba rugi, riwayat transaksi, dan pengeluaran</p>
        </div>
        <button @click="showSetting = true" 
                class="inline-flex items-center gap-2 bg-white hover:bg-[#FAE2E2] text-[#842529] border border-border-muted px-3 py-2 rounded-xl text-xs font-semibold shadow-sm transition-all active:scale-95 focus:outline-none" 
                title="Pengaturan Notifikasi Telegram">
            <x-lucide-bell class="w-4 h-4 text-[#842529]" />
        </button>
    </div>

    <!-- Tab Navigasi Keuangan -->
    <div class="flex border-b border-border-muted overflow-x-auto no-scrollbar">
        <a href="{{ route('keuangan.index', ['tab' => 'labarugi']) }}" 
           class="py-3 px-4 sm:px-6 text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2 {{ $tab == 'labarugi' ? 'border-b-2 border-[#842529] text-[#842529]' : 'text-gray-500 hover:text-gray-700' }}">
            <x-lucide-chart-pie class="w-4 h-4" />
            <span>Laba Rugi</span>
        </a>
        <a href="{{ route('keuangan.index', ['tab' => 'riwayat']) }}" 
           class="py-3 px-4 sm:px-6 text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2 {{ $tab == 'riwayat' ? 'border-b-2 border-[#842529] text-[#842529]' : 'text-gray-500 hover:text-gray-700' }}">
            <x-lucide-history class="w-4 h-4" />
            <span>Riwayat Transaksi</span>
        </a>
        <a href="{{ route('keuangan.index', ['tab' => 'piutang']) }}" 
           class="py-3 px-4 sm:px-6 text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2 {{ $tab == 'piutang' ? 'border-b-2 border-[#842529] text-[#842529]' : 'text-gray-500 hover:text-gray-700' }}">
            <x-lucide-receipt class="w-4 h-4" />
            <span>Piutang</span>
        </a>
        <a href="{{ route('keuangan.index', ['tab' => 'pengeluaran']) }}" 
           class="py-3 px-4 sm:px-6 text-xs font-bold whitespace-nowrap transition-all flex items-center gap-2 {{ $tab == 'pengeluaran' ? 'border-b-2 border-[#842529] text-[#842529]' : 'text-gray-500 hover:text-gray-700' }}">
            <x-lucide-wallet-cards class="w-4 h-4" />
            <span>List Pengeluaran</span>
        </a>
    </div>

    <!-- ================= ISI TAB 1: LABA RUGI ================= -->
    @if($tab == 'labarugi')
        <div class="space-y-4">
            <!-- Hero Card: Estimasi Laba Bersih -->
            <div class="bg-white border border-border-muted rounded-2xl p-5 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                            <x-lucide-scale class="w-4 h-4 text-[#842529]" />
                            Estimasi Laba Bersih
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-[#842529] mt-1">
                            Rp {{ number_format($labaRugi, 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="w-12 h-12 bg-[#FAE2E2] rounded-2xl flex items-center justify-center flex-shrink-0">
                        <x-lucide-trending-up class="w-6 h-6 text-[#842529]" />
                    </div>
                </div>
            </div>

            <!-- Grid: Pemasukan vs Pengeluaran -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">Total Pemasukan</p>
                        <p class="text-lg font-bold text-emerald-700">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-10 h-10 bg-emerald-50 border border-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <x-lucide-arrow-down-left class="w-5 h-5" />
                    </div>
                </div>
                <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 mb-1">Total Pengeluaran</p>
                        <p class="text-lg font-bold text-amber-700">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                    </div>
                    <div class="w-10 h-10 bg-amber-50 border border-amber-100 rounded-xl flex items-center justify-center text-amber-600 flex-shrink-0">
                        <x-lucide-arrow-up-right class="w-5 h-5" />
                    </div>
                </div>
            </div>
        </div>
        
    <!-- ================= ISI TAB 2: RIWAYAT TRANSAKSI ================= -->
    @elseif($tab == 'riwayat')
        <div class="space-y-3">
            @foreach($pesanan as $p)
                <div x-data="{ showDetail: false }">
                    <div @click="showDetail = true" 
                         class="cursor-pointer bg-white border border-border-muted rounded-2xl p-4 text-xs shadow-sm flex justify-between items-center hover:border-gray-400 hover:shadow-md transition-all">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-[#842529] font-mono text-sm">{{ $p->id_pesanan }}</span>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $p->status_bayar == 'Lunas' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}">
                                    {{ $p->status_bayar }}
                                </span>
                            </div>
                            <p class="text-gray-500 flex flex-wrap items-center gap-x-3 gap-y-1">
                                <span class="flex items-center gap-1">
                                    <x-lucide-calendar class="w-3.5 h-3.5 text-gray-400" />
                                    <span>{{ $p->tgl_order }}</span>
                                </span>
                                <span>•</span>
                                <span class="flex items-center gap-1">
                                    <x-lucide-user class="w-3.5 h-3.5 text-gray-400" />
                                    <span class="font-semibold text-gray-800">{{ $p->pelanggan->nama_pelanggan ?? 'Umum' }}</span>
                                </span>
                            </p>
                        </div>
                        <div class="flex items-center gap-2 text-gray-400">
                            <x-lucide-chevron-right class="w-5 h-5" />
                        </div>
                    </div>

                    <!-- MODAL DETAIL PESANAN -->
                    <div x-show="showDetail" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex justify-center items-center p-4" style="display: none;">
                        <div @click.away="showDetail = false" class="bg-white rounded-2xl w-full max-w-md shadow-xl flex flex-col max-h-[90vh] border border-border-muted overflow-hidden">
                            <!-- Header Modal -->
                            <div class="p-4 border-b border-border-muted flex justify-between items-center bg-gray-50">
                                <div class="flex items-center gap-2">
                                    <x-lucide-file-text class="w-4 h-4 text-[#842529]" />
                                    <div>
                                        <h3 class="font-bold text-gray-800 text-sm">Detail Pesanan</h3>
                                        <p class="text-[10px] text-gray-500 font-mono">{{ $p->id_pesanan }}</p>
                                    </div>
                                </div>
                                <button @click="showDetail = false" class="text-gray-400 hover:text-[#842529] p-1 rounded-lg transition-colors">
                                    <x-lucide-x class="w-5 h-5" />
                                </button>
                            </div>
                            
                            <!-- Body Modal (Scrollable) -->
                            <div class="p-4 overflow-y-auto text-xs space-y-4">
                                <div class="grid grid-cols-2 gap-3 text-xs bg-gray-50 p-3 rounded-xl border border-border-muted">
                                    <div>
                                        <span class="text-gray-500 block text-[11px]">Pelanggan</span>
                                        <span class="font-bold text-gray-800">{{ $p->pelanggan->nama_pelanggan ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block text-[11px]">Tanggal Order</span>
                                        <span class="font-bold text-gray-800">{{ $p->tgl_order }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block text-[11px]">Metode Bayar</span>
                                        <span class="font-bold text-gray-800">{{ $p->metode_bayar }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block text-[11px]">Status</span>
                                        <span class="font-bold {{ $p->status_bayar == 'Lunas' ? 'text-emerald-700' : 'text-amber-700' }}">{{ $p->status_bayar }}</span>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="font-bold text-gray-700 border-b border-border-muted pb-1 mb-2 text-xs uppercase tracking-wider">Item Produk</h4>
                                    <div class="space-y-2">
                                        @php $totalTagihan = 0; @endphp
                                        @foreach($p->detail as $det)
                                            @php $totalTagihan += $det->subtotal; @endphp
                                            <div class="flex justify-between items-center bg-white border border-border-muted p-3 rounded-xl">
                                                <div>
                                                    <p class="font-bold text-gray-800">{{ $det->produk->nama_produk ?? 'Unknown' }}</p>
                                                    <p class="text-[11px] text-gray-500">{{ $det->qty }} kg x Rp {{ number_format($det->harga_jual_saat_ini, 0, ',', '.') }}</p>
                                                </div>
                                                <span class="font-bold text-[#842529]">Rp {{ number_format($det->subtotal, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                @if($p->catatan)
                                    <div class="bg-amber-50 p-3 border border-amber-200 rounded-xl">
                                        <span class="font-bold text-xs text-amber-800 uppercase flex items-center gap-1 mb-0.5">
                                            <x-lucide-info class="w-3.5 h-3.5 text-amber-700" />
                                            Catatan Pesanan:
                                        </span>
                                        <p class="text-xs text-amber-900 italic">{{ $p->catatan }}</p>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4 border-t border-border-muted bg-gray-50 flex justify-between items-center">
                                <span class="font-bold text-gray-600 text-xs">Total Tagihan:</span>
                                <span class="font-extrabold text-[#842529] text-base">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                            </div>

                            @if($p->metode_bayar == 'Tempo' && $p->status_bayar != 'Lunas' && $p->piutang)
                                <div class="p-4 bg-sky-50 border-t border-sky-200 space-y-2">
                                    <h4 class="text-xs font-bold text-sky-900 uppercase tracking-wider flex items-center gap-1.5">
                                        <x-lucide-credit-card class="w-4 h-4 text-sky-700" />
                                        <span>Update Pembayaran Piutang Direct</span>
                                    </h4>
                                    
                                    <div class="flex justify-between text-xs text-sky-800">
                                        <span>Telah Dibayar: <strong>Rp {{ number_format($p->piutang->jumlah_terbayar, 0, ',', '.') }}</strong></span>
                                        <span>Sisa Tagihan: <strong class="text-[#842529]">Rp {{ number_format($p->piutang->total_tagihan - $p->piutang->jumlah_terbayar, 0, ',', '.') }}</strong></span>
                                    </div>

                                    <form action="{{ route('piutang.bayar', $p->id_pesanan) }}" method="POST" class="flex gap-2 pt-1">
                                        @csrf
                                        <input type="number" name="nominal_bayar" min="1" max="{{ $p->piutang->total_tagihan - $p->piutang->jumlah_terbayar }}" 
                                               class="flex-1 bg-white border border-sky-300 rounded-xl p-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500" 
                                               placeholder="Masukkan nominal bayar..." required>
                                        <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold shadow transition-colors flex items-center gap-1">
                                            <x-lucide-check-check class="w-4 h-4" />
                                            <span>Bayar</span>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    <!-- ================= ISI TAB 3: PIUTANG ================= -->
    @elseif($tab == 'piutang')
        <div class="space-y-4">
            <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#FAE2E2] rounded-xl flex items-center justify-center text-[#842529]">
                        <x-lucide-receipt class="w-5 h-5" />
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-gray-800">Manajemen Halaman Piutang</h3>
                        <p class="text-[11px] text-gray-500">Kelola rincian jatuh tempo dan riwayat cicilan piutang</p>
                    </div>
                </div>
                <a href="{{ route('piutang.index') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#842529] hover:bg-red-900 text-white text-xs px-4 py-2.5 rounded-xl font-bold shadow-sm transition-all active:scale-95">
                    <span>Kelola & Bayar Piutang</span>
                    <x-lucide-arrow-right class="w-4 h-4" />
                </a>
            </div>

            <div class="space-y-2">
                @foreach($piutang as $pi)
                    <div class="bg-white border border-border-muted rounded-2xl p-4 text-xs shadow-sm flex justify-between items-center">
                        <div class="space-y-1">
                            <span class="font-bold text-gray-900 font-mono">{{ $pi->id_pesanan }}</span>
                            <p class="text-gray-500">Total Tagihan: <strong class="text-gray-800">Rp {{ number_format($pi->total_tagihan, 0, ',', '.') }}</strong></p>
                        </div>
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold border {{ $pi->status_piutang == 'Lunas' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-rose-50 border-rose-200 text-rose-700' }}">
                            {{ $pi->status_piutang }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
        
    <!-- ================= ISI TAB 4: PENGELUARAN ================= -->
    @else
        <div x-data="{ 
            showEdit: false, 
            editData: { id_pengeluaran: '', tgl_pengeluaran: '', kategori_pengeluaran: '', nominal: '', keterangan: '' } 
        }" class="space-y-4">
            
            <div class="space-y-3">
                @forelse($pengeluaran as $pen)
                    <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm flex flex-col hover:border-gray-300 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md inline-block mb-1">
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
                            
                            <form action="{{ url('/pengeluaran/' . $pen->id_pengeluaran) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus catatan pengeluaran ini?');">
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
                    </div>
                @endforelse
            </div>

            <!-- MODAL EDIT PENGELUARAN -->
            <div x-show="showEdit" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex justify-center items-center p-4" style="display: none;">
                <div @click.away="showEdit = false" class="bg-white rounded-2xl w-full max-w-md p-5 shadow-xl relative border border-border-muted">
                    <button @click="showEdit = false" class="absolute top-4 right-4 text-gray-400 hover:text-[#842529] p-1 rounded-lg transition-colors">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                    <h3 class="font-bold text-gray-800 mb-4 text-sm border-b border-border-muted pb-3 flex items-center gap-2">
                        <x-lucide-square-pen class="w-4 h-4 text-amber-600" />
                        <span>Edit Pengeluaran</span>
                    </h3>
                    
                    <form :action="'{{ url('/pengeluaran') }}/' + editData.id_pengeluaran" method="POST" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs text-gray-700 font-semibold mb-1">Tanggal</label>
                            <input type="date" name="tgl_pengeluaran" x-model="editData.tgl_pengeluaran" 
                                   class="w-full bg-gray-50 border border-border-muted rounded-xl p-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-700 font-semibold mb-1">Kategori</label>
                            <select name="kategori_pengeluaran" x-model="editData.kategori_pengeluaran" 
                                    class="w-full bg-gray-50 border border-border-muted rounded-xl p-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                                <option value="Makan">Makan</option>
                                <option value="Pembelian Daging">Pembelian Daging</option>
                                <option value="Operasional">Operasional</option>
                                <option value="Transportasi">Transportasi</option>
                                <option value="Lain-lain">Lain-lain</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-700 font-semibold mb-1">Nominal (Rp)</label>
                            <input type="number" name="nominal" x-model="editData.nominal" min="1000" 
                                   class="w-full bg-gray-50 border border-border-muted rounded-xl p-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-700 font-semibold mb-1">Keterangan</label>
                            <textarea name="keterangan" x-model="editData.keterangan" rows="2" 
                                      class="w-full bg-gray-50 border border-border-muted rounded-xl p-2.5 text-xs focus:outline-none focus:ring-2 focus:ring-[#842529] focus:bg-white" required></textarea>
                        </div>
                        <div class="pt-3 flex gap-2">
                            <button type="button" @click="showEdit = false" class="flex-1 border border-border-muted text-gray-700 py-2.5 rounded-xl font-bold text-xs hover:bg-gray-50">Batal</button>
                            <button type="submit" class="flex-1 bg-[#842529] hover:bg-red-900 text-white py-2.5 rounded-xl font-bold text-xs shadow">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- MODAL SETTING NOTIFIKASI TELEGRAM -->
    <div x-show="showSetting" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex justify-center items-center p-4" style="display: none;">
        <div @click.away="showSetting = false" class="bg-white rounded-2xl w-full max-w-md p-5 shadow-xl relative max-h-[90vh] overflow-y-auto border border-border-muted">
            <button @click="showSetting = false" class="absolute top-4 right-4 text-gray-400 hover:text-[#842529] p-1 rounded-lg transition-colors">
                <x-lucide-x class="w-5 h-5" />
            </button>
            
            <h3 class="font-bold text-gray-900 text-sm mb-0.5 flex items-center gap-2">
                <x-lucide-settings class="w-4 h-4 text-[#842529]" />
                <span>Pengaturan Notifikasi Telegram</span>
            </h3>
            <p class="text-[11px] text-gray-500 mb-4 border-b border-border-muted pb-3">Atur bot Telegram dan jadwal pengingat piutang pelanggan.</p>
            
            <form action="{{ url('/pengaturan/notifikasi') }}" method="POST" class="space-y-3 text-xs">
                @csrf
                
                @php
                    $h_minus = $pengaturan->h_minus_notifikasi ?? [];
                    if(is_string($h_minus)) $h_minus = json_decode($h_minus, true) ?? [];
                @endphp

                <div>
                    <label class="block text-gray-700 font-bold mb-1">Status Notifikasi</label>
                    <select name="is_notif_aktif" class="w-full bg-gray-50 border border-border-muted rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-[#842529]">
                        <option value="1" {{ ($pengaturan->is_notif_aktif ?? 0) == 1 ? 'selected' : '' }}>Aktif - Kirim Notifikasi Otomatis</option>
                        <option value="0" {{ ($pengaturan->is_notif_aktif ?? 0) == 0 ? 'selected' : '' }}>Nonaktif - Jangan Kirim</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 font-bold mb-1">Telegram Bot Token</label>
                    <input type="text" name="telegram_bot_token" value="{{ $pengaturan->telegram_bot_token ?? '' }}" 
                           class="w-full bg-gray-50 border border-border-muted rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-[#842529]" 
                           placeholder="Contoh: 123456789:ABC-DEF1234ghIkl...">
                </div>

                <div>
                    <label class="block text-gray-700 font-bold mb-1">Telegram Chat ID (Grup/Personal)</label>
                    <input type="text" name="telegram_chat_id" value="{{ $pengaturan->telegram_chat_id ?? '' }}" 
                           class="w-full bg-gray-50 border border-border-muted rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-[#842529]" 
                           placeholder="Contoh: -1001234567890">
                </div>

                <div>
                    <label class="block text-gray-700 font-bold mb-1">Waktu Pengingat <span class="font-normal italic text-gray-400">(Bisa pilih beberapa)</span></label>
                    <div class="grid grid-cols-2 gap-2 mt-1">
                        <label class="flex items-center gap-2 bg-gray-50 hover:bg-[#FAE2E2]/50 p-2.5 rounded-xl border border-border-muted cursor-pointer transition-colors">
                            <input type="checkbox" name="h_minus_notifikasi[]" value="3" {{ in_array('3', $h_minus) ? 'checked' : '' }} class="accent-[#842529]">
                            <span>H-3 Jatuh Tempo</span>
                        </label>
                        <label class="flex items-center gap-2 bg-gray-50 hover:bg-[#FAE2E2]/50 p-2.5 rounded-xl border border-border-muted cursor-pointer transition-colors">
                            <input type="checkbox" name="h_minus_notifikasi[]" value="2" {{ in_array('2', $h_minus) ? 'checked' : '' }} class="accent-[#842529]">
                            <span>H-2 Jatuh Tempo</span>
                        </label>
                        <label class="flex items-center gap-2 bg-gray-50 hover:bg-[#FAE2E2]/50 p-2.5 rounded-xl border border-border-muted cursor-pointer transition-colors">
                            <input type="checkbox" name="h_minus_notifikasi[]" value="1" {{ in_array('1', $h_minus) ? 'checked' : '' }} class="accent-[#842529]">
                            <span>H-1 Jatuh Tempo</span>
                        </label>
                        <label class="flex items-center gap-2 bg-gray-50 hover:bg-[#FAE2E2]/50 p-2.5 rounded-xl border border-border-muted cursor-pointer transition-colors">
                            <input type="checkbox" name="h_minus_notifikasi[]" value="0" {{ in_array('0', $h_minus) ? 'checked' : '' }} class="accent-[#842529]">
                            <span class="font-bold text-[#842529]">Hari H (Jatuh Tempo)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 font-bold mb-1">Template Pesan Tagihan (WA/Telegram)</label>
                    <p class="text-[10px] text-gray-400 mb-1">Gunakan <b>{nama}</b>, <b>{tagihan}</b>, <b>{jatuh_tempo}</b> untuk variabel otomatis.</p>
                    <textarea name="template_tagihan" rows="3" 
                              class="w-full bg-gray-50 border border-border-muted rounded-xl p-2.5 focus:outline-none focus:ring-2 focus:ring-[#842529]" required>{{ $pengaturan->template_tagihan ?? "Halo {nama},\nTagihan piutang Anda sebesar Rp {tagihan} akan jatuh tempo pada {jatuh_tempo}. Mohon segera melakukan pembayaran." }}</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="bg-[#842529] text-white px-4 py-2.5 rounded-xl font-bold hover:bg-red-900 shadow w-full transition-colors flex items-center justify-center gap-2">
                        <x-lucide-save class="w-4 h-4" />
                        <span>Simpan Pengaturan Notifikasi</span>
                    </button>
                </div>
            </form>

            <!-- TOMBOL UJI COBA NOTIFIKASI -->
            <div class="mt-4 pt-3 border-t border-border-muted">
                <p class="text-xs font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                    <x-lucide-flask-conical class="w-4 h-4 text-emerald-600" />
                    <span>Uji Coba Pengiriman Telegram:</span>
                </p>
                <div class="grid grid-cols-2 gap-2">
                    <form action="{{ route('pengaturan.notifikasi.test') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white py-2.5 px-2 rounded-xl text-xs font-bold shadow text-center transition-colors flex items-center justify-center gap-1.5">
                            <x-lucide-bot class="w-4 h-4" />
                            <span>Tes Bot</span>
                        </button>
                    </form>
                    <form action="{{ route('pengaturan.notifikasi.kirim') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-sky-700 hover:bg-sky-800 text-white py-2.5 px-2 rounded-xl text-xs font-bold shadow text-center transition-colors flex items-center justify-center gap-1.5">
                            <x-lucide-send class="w-4 h-4" />
                            <span>Kirim Tagihan</span>
                        </button>
                    </form>
                </div>
                <p class="text-[10px] text-gray-400 mt-2 italic text-center">Pastikan Anda sudah menekan tombol START di bot Telegram sebelum melakukan pengujian.</p>
            </div>
        </div>
    </div>
</div>
@endsection