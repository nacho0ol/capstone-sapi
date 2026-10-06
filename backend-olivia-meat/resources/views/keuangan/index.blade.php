@extends('layouts.app')

@section('content')
<div>
    <h2 class="font-bold text-gray-700 text-base mb-3">Menu Keuangan & Laporan</h2>

    <!-- Tab Navigasi Keuangan -->
    <div class="flex border-b border-gray-200 mb-4 overflow-x-auto text-[11px]">
        <a href="{{ route('keuangan.index', ['tab' => 'labarugi']) }}" class="px-3 py-2 font-semibold whitespace-nowrap {{ $tab == 'labarugi' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-500' }}">Laba Rugi</a>
        <a href="{{ route('keuangan.index', ['tab' => 'riwayat']) }}" class="px-3 py-2 font-semibold whitespace-nowrap {{ $tab == 'riwayat' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-500' }}">Riwayat Transaksi</a>
        <a href="{{ route('keuangan.index', ['tab' => 'piutang']) }}" class="px-3 py-2 font-semibold whitespace-nowrap {{ $tab == 'piutang' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-500' }}">Piutang</a>
        <a href="{{ route('keuangan.index', ['tab' => 'pengeluaran']) }}" class="px-3 py-2 font-semibold whitespace-nowrap {{ $tab == 'pengeluaran' ? 'border-b-2 border-red-600 text-red-600' : 'text-gray-500' }}">List Pengeluaran</a>
    </div>

    @if($tab == 'labarugi')
        <div class="space-y-3">
            <div class="bg-red-50 border-l-4 border-red-600 p-3 rounded shadow-sm">
                <span class="text-xs text-gray-500">Estimasi Laba Bersih</span>
                <p class="text-xl font-bold text-red-700">Rp {{ number_format($labaRugi, 0, ',', '.') }}</p>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-green-50 p-2.5 rounded border border-green-100">
                    <span class="text-[10px] text-gray-500">Total Pemasukan</span>
                    <p class="text-sm font-bold text-green-700">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                </div>
                <div class="bg-orange-50 p-2.5 rounded border border-orange-100">
                    <span class="text-[10px] text-gray-500">Total Pengeluaran</span>
                    <p class="text-sm font-bold text-orange-700">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    @elseif($tab == 'riwayat')
        <div class="space-y-2">
            @foreach($pesanan as $p)
                <div x-data="{ showDetail: false }">
                    
                    <div @click="showDetail = true" class="cursor-pointer bg-white border border-gray-200 rounded p-2.5 text-xs shadow-sm flex justify-between items-center hover:bg-red-50 transition-colors">
                        <div>
                            <span class="font-bold text-red-600">{{ $p->id_pesanan }}</span>
                            <p class="text-gray-500">Tanggal: {{ $p->tgl_order }} | Pelanggan: <span class="font-semibold text-gray-700">{{ $p->pelanggan->nama_pelanggan ?? 'Umum' }}</span></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 rounded text-[10px] font-bold {{ $p->status_bayar == 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ $p->status_bayar }}</span>
                            <span class="text-gray-400 font-bold">&rang;</span>
                        </div>
                    </div>

                    <div x-show="showDetail" class="fixed inset-0 bg-black bg-opacity-60 z-[60] flex justify-center items-center p-4" style="display: none;" x-cloak>
                        <div class="bg-white rounded-lg w-full max-w-sm shadow-xl flex flex-col max-h-[90vh]">
                            
                            <!-- Header Modal -->
                            <div class="p-3 border-b border-gray-200 flex justify-between items-center bg-gray-50 rounded-t-lg">
                                <div>
                                    <h3 class="font-bold text-gray-800 text-sm">Detail Pesanan</h3>
                                    <p class="text-[10px] text-gray-500 font-mono">{{ $p->id_pesanan }}</p>
                                </div>
                                <button @click="showDetail = false" class="text-gray-400 hover:text-red-600 font-bold text-2xl leading-none">&times;</button>
                            </div>
                            
                            <!-- Body Modal (Scrollable) -->
                            <div class="p-4 overflow-y-auto text-xs space-y-3">
                                <!-- Info Dasar -->
                                <div class="grid grid-cols-2 gap-2 text-[11px] bg-gray-50 p-2.5 rounded border border-gray-200">
                                    <div>
                                        <span class="text-gray-500 block">Pelanggan:</span>
                                        <span class="font-bold text-gray-800">{{ $p->pelanggan->nama_pelanggan ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block">Tanggal Order:</span>
                                        <span class="font-bold text-gray-800">{{ $p->tgl_order }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block">Metode Bayar:</span>
                                        <span class="font-bold text-gray-800">{{ $p->metode_bayar }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block">Status:</span>
                                        <span class="font-bold {{ $p->status_bayar == 'Lunas' ? 'text-green-600' : 'text-yellow-600' }}">{{ $p->status_bayar }}</span>
                                    </div>
                                </div>

                                <!-- List Item Produk -->
                                <div>
                                    <h4 class="font-bold text-gray-700 border-b border-gray-200 pb-1 mb-2 text-[11px] uppercase tracking-wider">Item Produk</h4>
                                    <div class="space-y-2">
                                        @php $totalTagihan = 0; @endphp
                                        @foreach($p->detail as $det)
                                            @php $totalTagihan += $det->subtotal; @endphp
                                            <div class="flex justify-between items-center bg-white border border-gray-100 p-2 rounded shadow-sm">
                                                <div>
                                                    <p class="font-bold text-gray-800">{{ $det->produk->nama_produk ?? 'Unknown' }}</p>
                                                    <p class="text-[10px] text-gray-500">{{ $det->qty }} kg x Rp {{ number_format($det->harga_jual_saat_ini, 0, ',', '.') }}</p>
                                                </div>
                                                <span class="font-bold text-red-600">Rp {{ number_format($det->subtotal, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <!-- Kolom Catatan (Jika Ada) -->
                                @if($p->catatan)
                                    <div class="bg-yellow-50 p-2.5 border border-yellow-200 rounded">
                                        <span class="font-bold text-[10px] text-yellow-700 uppercase">Catatan Pesanan:</span>
                                        <p class="text-[11px] text-yellow-800 italic mt-0.5">{{ $p->catatan }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Footer Modal (Total) -->
                            <div class="p-4 border-t border-gray-200 bg-gray-50 rounded-b-lg flex justify-between items-center">
                                <span class="font-bold text-gray-600 text-sm">Total Tagihan:</span>
                                <span class="font-bold text-red-700 text-lg">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                            </div>

                            <!-- Form Ekstra untuk Cicilan Piutang -->
                            @if($p->metode_bayar == 'Tempo' && $p->status_bayar != 'Lunas' && $p->piutang)
                                <div class="p-4 bg-blue-50 border-t border-blue-200 rounded-b-lg">
                                    <h4 class="text-xs font-bold text-blue-800 mb-2 uppercase tracking-wider">Update Pembayaran Piutang</h4>
                                    
                                    <div class="flex justify-between text-[11px] text-blue-700 mb-2">
                                        <span>Telah Dibayar: <strong class="text-blue-900">Rp {{ number_format($p->piutang->jumlah_terbayar, 0, ',', '.') }}</strong></span>
                                        <span>Sisa Tagihan: <strong class="text-red-600">Rp {{ number_format($p->piutang->total_tagihan - $p->piutang->jumlah_terbayar, 0, ',', '.') }}</strong></span>
                                    </div>

                                    <form action="{{ route('piutang.bayar', $p->id_pesanan) }}" method="POST" class="flex space-x-2 mt-2">
                                        @csrf
                                        <input type="number" name="nominal_bayar" min="1" max="{{ $p->piutang->total_tagihan - $p->piutang->jumlah_terbayar }}" class="flex-1 border border-blue-300 rounded p-2 text-xs focus:outline-none focus:border-blue-500" placeholder="Masukkan nominal bayar..." required>
                                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-xs font-bold hover:bg-blue-700 shadow transition-colors">Bayar</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    @elseif($tab == 'piutang')
        <div class="mb-3">
            <a href="{{ route('piutang.index') }}" class="block w-full bg-blue-50 text-blue-600 border border-blue-200 text-center py-2 rounded shadow-sm text-xs font-bold hover:bg-blue-100">
                Kelola & Bayar Piutang &rarr;
            </a>
        </div>
        <div class="space-y-2">
            @foreach($piutang as $pi)
                <div class="bg-white border rounded p-2.5 text-xs shadow-sm flex justify-between items-center">
                    <div>
                        <span class="font-bold text-blue-600">{{ $pi->id_pesanan }}</span>
                        <p class="text-gray-500">Tagihan: Rp {{ number_format($pi->total_tagihan, 0, ',', '.') }}</p>
                    </div>
                    <span class="px-2 py-1 rounded text-[10px] {{ $pi->status_piutang == 'Lunas' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $pi->status_piutang }}</span>
                </div>
            @endforeach
        </div>
        
    @else
        <div x-data="{ 
            showEdit: false, 
            editData: { id_pengeluaran: '', tgl_pengeluaran: '', kategori_pengeluaran: '', nominal: '', keterangan: '' } 
        }">
            
            <div class="space-y-3">
                @forelse($pengeluaran as $pen)
                    <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm flex flex-col hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="text-xs font-bold text-orange-600">{{ $pen->kategori_pengeluaran }}</span>
                                <p class="text-sm font-semibold text-gray-800">Rp {{ number_format($pen->nominal, 0, ',', '.') }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">{{ $pen->keterangan }}</p>
                            </div>
                            <div class="text-right text-xs text-gray-400 font-semibold">
                                {{ date('d M Y', strtotime($pen->tgl_pengeluaran)) }}
                            </div>
                        </div>
                        
                        <!-- Tombol Aksi (Edit & Hapus) -->
                        <div class="flex justify-end space-x-2 border-t pt-2 mt-1">
                            <button @click="editData = {{ json_encode($pen) }}; showEdit = true;" class="text-[10px] bg-yellow-50 text-yellow-700 border border-yellow-200 px-3 py-1 rounded hover:bg-yellow-100 font-bold">
                                Edit
                            </button>
                            
                            <!-- PERBAIKAN: Menggunakan url() agar tidak nyasar ke route API -->
                            <form action="{{ url('/pengeluaran/' . $pen->id_pengeluaran) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus catatan pengeluaran ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-[10px] bg-red-50 text-red-600 border border-red-200 px-3 py-1 rounded hover:bg-red-100 font-bold">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center bg-white rounded-lg border py-10 shadow-sm">
                        <p class="text-gray-400 text-sm">Belum ada catatan pengeluaran.</p>
                    </div>
                @endforelse
            </div>

            <!-- MODAL EDIT PENGELUARAN -->
            <div x-show="showEdit" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center p-4" style="display: none;" x-cloak>
                <div class="bg-white rounded-lg w-full max-w-md p-4 shadow-xl relative">
                    <button @click="showEdit = false" class="absolute top-3 right-3 text-gray-500 hover:text-red-500 font-bold text-xl">&times;</button>
                    <h3 class="font-bold text-gray-800 mb-4 text-lg border-b pb-2">Edit Pengeluaran</h3>
                    
                    <!-- PERBAIKAN: Menggunakan url() untuk Update -->
                    <form :action="'{{ url('/pengeluaran') }}/' + editData.id_pengeluaran" method="POST" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs text-gray-500 font-semibold mb-1">Tanggal</label>
                            <input type="date" name="tgl_pengeluaran" x-model="editData.tgl_pengeluaran" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 font-semibold mb-1">Kategori</label>
                            <select name="kategori_pengeluaran" x-model="editData.kategori_pengeluaran" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
                                <option value="Makan">Makan</option>
                                <option value="Pembelian Daging">Pembelian Daging</option>
                                <option value="Operasional">Operasional</option>
                                <option value="Transportasi">Transportasi</option>
                                <option value="Lain-lain">Lain-lain</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 font-semibold mb-1">Nominal (Rp)</label>
                            <input type="number" name="nominal" x-model="editData.nominal" min="1000" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 font-semibold mb-1">Keterangan</label>
                            <textarea name="keterangan" x-model="editData.keterangan" rows="2" class="w-full border border-gray-300 rounded p-2 text-sm focus:outline-none focus:border-red-500" required></textarea>
                        </div>
                        <div class="pt-4 flex space-x-2">
                            <button type="button" @click="showEdit = false" class="flex-1 border border-gray-300 text-gray-700 py-2 rounded font-semibold text-sm hover:bg-gray-50">Batal</button>
                            <button type="submit" class="flex-1 bg-yellow-500 text-white py-2 rounded font-semibold text-sm hover:bg-yellow-600 shadow">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection