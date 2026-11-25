@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Tombol Kembali & Profil Singkat Pelanggan -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('pelanggan.index') }}" class="p-2 bg-white border border-border-muted rounded-xl text-gray-600 hover:text-[#842529] shadow-sm">
                <x-lucide-arrow-left class="w-4 h-4" />
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $pelanggan->nama_pelanggan }}</h1>
                <p class="text-xs text-gray-500">{{ $pelanggan->no_telp }} • {{ $pelanggan->alamat }}</p>
            </div>
        </div>
    </div>

    <!-- Ringkasan Transaksi & Komplain Pelanggan -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-medium">Total Transaksi Belanja</p>
            <p class="text-lg font-extrabold text-emerald-700 mt-1">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $riwayatPesanan->count() }} riwayat orderan</p>
        </div>

        <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-medium">Sisa Tagihan Aktif</p>
            <p class="text-lg font-extrabold {{ $sisaTagihan > 0 ? 'text-rose-600' : 'text-gray-800' }} mt-1">
                Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
            </p>
            <p class="text-[10px] text-gray-400 mt-0.5">Tagihan belum lunas</p>
        </div>

        <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm">
            <p class="text-xs text-gray-500 font-medium">Catatan / Komplain Tercatat</p>
            <p class="text-lg font-extrabold text-amber-700 mt-1">{{ $jumlahKomplain }} Orderan</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Tersimpan di catatan orderan</p>
        </div>
    </div>

    <!-- Daftar Riwayat Orderan, Pembayaran, & Komplain -->
    <div class="space-y-3">
        <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Riwayat Orderan, Pembayaran & Komplain</h2>

        @forelse($riwayatPesanan as $order)
            @php
                $totalOrder = $order->detail->sum('subtotal');
                $statusLower = strtolower($order->status_pemesanan);
                $isCancelled = in_array($statusLower, ['ditolak', 'dibatalkan']);
            @endphp

            <div class="bg-white border border-border-muted rounded-2xl p-4 shadow-sm space-y-3 text-xs">
                <!-- Header Orderan -->
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-border-muted pb-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-bold text-[#842529] font-mono text-sm">{{ $order->id_pesanan }}</span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $isCancelled ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-sky-50 border-sky-200 text-sky-700' }}">
                            {{ ucfirst($order->status_pemesanan) }}
                        </span>
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $order->status_bayar == 'Lunas' ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}">
                            {{ $order->status_bayar }} ({{ $order->metode_bayar }})
                        </span>
                    </div>
                    <span class="text-gray-500 flex items-center gap-1">
                        <x-lucide-calendar class="w-3.5 h-3.5 text-gray-400" />
                        <span>Order: {{ $order->tgl_order }} | Antar: {{ $order->tgl_antar }}</span>
                    </span>
                </div>

                <!-- Rincian Produk -->
                <div class="space-y-1.5">
                    @foreach($order->detail as $det)
                        <div class="flex justify-between items-center text-gray-700">
                            <span>{{ $det->produk->nama_produk ?? 'Produk Terhapus' }} <span class="text-gray-400">({{ $det->qty }} kg × Rp {{ number_format($det->harga_jual_saat_ini, 0, ',', '.') }})</span></span>
                            <span class="font-semibold">Rp {{ number_format($det->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Info Pembayaran & Tagihan -->
                <div class="bg-gray-50 rounded-xl p-3 border border-border-muted flex flex-wrap justify-between items-center gap-2">
                    <div>
                        <span class="text-gray-500">Total Order:</span>
                        <strong class="text-gray-900 ml-1">Rp {{ number_format($totalOrder, 0, ',', '.') }}</strong>
                    </div>

                    @if($order->piutang)
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="text-emerald-700">Terbayar: <strong>Rp {{ number_format($order->piutang->jumlah_terbayar, 0, ',', '.') }}</strong></span>
                            <span class="text-rose-700">Sisa Tagihan: <strong>Rp {{ number_format(max(0, $order->piutang->total_tagihan - $order->piutang->jumlah_terbayar), 0, ',', '.') }}</strong></span>
                            @if($order->piutang->tgl_jatuh_tempo)
                                <span class="text-gray-500">Jatuh Tempo: <strong>{{ date('d M Y', strtotime($order->piutang->tgl_jatuh_tempo)) }}</strong></span>
                            @endif
                        </div>
                    @else
                        <span class="text-emerald-700 font-semibold">Pembayaran Tunai / Lunas</span>
                    @endif
                </div>

                <!-- Komplain / Catatan Orderan -->
                <form action="{{ route('transaksi.komplain', $order->id_pesanan) }}" method="POST" class="bg-amber-50/70 border border-amber-200 rounded-xl p-3 space-y-2">
                    @csrf
                    @method('PUT')
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-amber-900 flex items-center gap-1.5 text-[11px] uppercase">
                            <x-lucide-message-square-warning class="w-3.5 h-3.5 text-amber-700" />
                            <span>Komplain / Catatan Orderan</span>
                        </label>
                        @if(!empty($order->catatan))
                            <span class="text-[10px] font-bold text-rose-700 bg-rose-100 px-2 py-0.5 rounded">Tercatat</span>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text" name="catatan" value="{{ $order->catatan }}" 
                               placeholder="Tulis komplain pelanggan atau catatan orderan ini..."
                               class="flex-1 bg-white border border-amber-200 rounded-xl px-3 py-2 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#842529]">
                        <button type="submit" class="bg-[#842529] hover:bg-red-900 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-sm transition-all inline-flex items-center justify-center gap-1.5 shrink-0">
                            <x-lucide-save class="w-3.5 h-3.5" />
                            <span>Simpan</span>
                        </button>
                    </div>
                </form>
            </div>
        @empty
            <div class="text-center bg-white rounded-2xl border border-border-muted py-12 shadow-sm">
                <p class="text-gray-400 text-xs">Pelanggan ini belum memiliki riwayat orderan.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection