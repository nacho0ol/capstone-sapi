<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Piutang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::with(['pelanggan', 'user', 'detail.produk', 'piutang'])->where('is_deleted', 0);

        if ($request->has('tanggal')) {
            $query->whereDate('tgl_order', $request->tanggal);
        }
        if ($request->has('pelanggan_id')) {
            $query->where('id_pelanggan', $request->pelanggan_id);
        }
        if ($request->has('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $pesanan = $query->orderBy('tgl_order', 'desc')->get();
        
        if ($request->wantsJson()) {
            return response()->json($pesanan);
        }

        $pelanggans = \App\Models\Pelanggan::orderBy('nama_pelanggan')->get();
        $produks = \App\Models\Produk::orderBy('nama_produk')->get();

        return view('transaksi.index', compact('pesanan', 'pelanggans', 'produks'));
    }

    public function show($id)
    {
        $pesanan = Pesanan::with(['pelanggan', 'user', 'detail.produk', 'piutang'])
                    ->where('is_deleted', 0)
                    ->findOrFail($id);
        return response()->json($pesanan);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
            'id_user'      => 'required|exists:users,id_user',
            'tgl_order'    => 'required|date',
            'metode_bayar' => 'required|in:Tunai,Transfer,Tempo',
            'tgl_antar'    => 'required|date',
            'items'        => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produks,id_produk',
            'items.*.qty'  => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            // Generate ID
            $datePrefix = date('Ymd', strtotime($request->tgl_order));
            $count = Pesanan::whereDate('tgl_order', $request->tgl_order)->count() + 1;
            $id_pesanan = 'ORD-' . $datePrefix . '-' . str_pad($count, 2, '0', STR_PAD_LEFT);

            $status_bayar = ($request->metode_bayar == 'Tempo') ? 'Unpaid' : 'Lunas';

            $pesanan = Pesanan::create([
    'id_pesanan' => $id_pesanan,
    'id_pelanggan' => $request->id_pelanggan,
    'id_user' => $request->id_user,
    'tgl_order' => $request->tgl_order,
    'metode_bayar' => $request->metode_bayar,
    'status_bayar' => $status_bayar,
    'tgl_antar' => $request->tgl_antar,
    'status_pemesanan' => 'Diterima', 
    'is_deleted' => 0
]);

            $total_tagihan = 0;

            foreach ($request->items as $item) {
                $produk = Produk::findOrFail($item['id_produk']);
                $subtotal = $item['qty'] * $produk->harga_jual;
                $total_tagihan += $subtotal;

                PesananDetail::create([
                    'id_pesanan' => $id_pesanan,
                    'id_produk' => $item['id_produk'],
                    'qty' => $item['qty'],
                    'harga_jual_saat_ini' => $produk->harga_jual,
                    //'subtotal' => $subtotal,
                    'is_deleted' => 0
                ]);
            }

            if ($request->metode_bayar == 'Tempo') {
                Piutang::create([
                    'id_pesanan' => $id_pesanan,
                    'tgl_jatuh_tempo' => date('Y-m-d', strtotime($request->tgl_order . ' + 7 days')), // Default 7 days
                    'total_tagihan' => $total_tagihan,
                    'jumlah_terbayar' => 0,
                    'status_piutang' => 'Belum Lunas',
                    'is_deleted' => 0
                ]);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Pesanan berhasil dibuat', 'data' => $pesanan], 201);
            }
            
            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Gagal membuat pesanan', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $pesanan = Pesanan::with(['detail.produk'])->where('is_deleted', 0)->findOrFail($id);
        $pelanggans = \App\Models\Pelanggan::orderBy('nama_pelanggan')->get();
        $produks = \App\Models\Produk::orderBy('nama_produk')->get();
        
        return view('transaksi.edit', compact('pesanan', 'pelanggans', 'produks'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_pelanggan' => 'required|exists:pelanggans,id_pelanggan',
            'tgl_order'    => 'required|date',
            'metode_bayar' => 'required|in:Tunai,Transfer,Tempo',
            'tgl_antar'    => 'required|date',
            'items'        => 'required|array|min:1',
            'items.*.id_produk' => 'required|exists:produks,id_produk',
            'items.*.qty'  => 'required|numeric|min:0.01',
        ]);

        $pesanan = Pesanan::where('is_deleted', 0)->findOrFail($id);

        DB::beginTransaction();
        try {
            $status_bayar = ($request->metode_bayar == 'Tempo') ? 'Unpaid' : 'Lunas';

            $pesanan->update([
                'id_pelanggan' => $request->id_pelanggan,
                'tgl_order' => $request->tgl_order,
                'metode_bayar' => $request->metode_bayar,
                'status_bayar' => $status_bayar,
                'tgl_antar' => $request->tgl_antar,
            ]);

            // Hapus detail lama
            PesananDetail::where('id_pesanan', $pesanan->id_pesanan)->delete();

            $total_tagihan = 0;
            // Masukkan detail baru
            foreach ($request->items as $item) {
                $produk = Produk::findOrFail($item['id_produk']);
                $subtotal = $item['qty'] * $produk->harga_jual;
                $total_tagihan += $subtotal;

                PesananDetail::create([
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_produk' => $item['id_produk'],
                    'qty' => $item['qty'],
                    'harga_jual_saat_ini' => $produk->harga_jual,
                    //'subtotal' => $subtotal,
                    'is_deleted' => 0
                ]);
            }

            // Update Piutang
            $piutang = Piutang::where('id_pesanan', $pesanan->id_pesanan)->first();
            if ($request->metode_bayar == 'Tempo') {
                if ($piutang) {
                    $piutang->update([
                        'total_tagihan' => $total_tagihan,
                        'is_deleted' => 0
                    ]);
                } else {
                    Piutang::create([
                        'id_pesanan' => $pesanan->id_pesanan,
                        'tgl_jatuh_tempo' => date('Y-m-d', strtotime($request->tgl_order . ' + 7 days')),
                        'total_tagihan' => $total_tagihan,
                        'jumlah_terbayar' => 0,
                        'status_piutang' => 'Belum Lunas',
                        'is_deleted' => 0
                    ]);
                }
            } else {
                if ($piutang) {
                    $piutang->update(['is_deleted' => 1]); // Hapus piutang jika diubah jadi Tunai/Transfer
                }
            }

            DB::commit();
            return redirect()->route('transaksi.index')->with('success', 'Pesanan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui pesanan: ' . $e->getMessage());
        }
    }

    public function cancel($id, Request $request)
    {
        $pesanan = Pesanan::where('is_deleted', 0)->findOrFail($id);
        
        DB::beginTransaction();
        try {
            $pesanan->status_pemesanan = 'Ditolak'; // as cancel
            $pesanan->save();

            if ($pesanan->metode_bayar == 'Tempo') {
                $piutang = Piutang::where('id_pesanan', $pesanan->id_pesanan)->first();
                if ($piutang) {
                    $piutang->is_deleted = 1;
                    $piutang->save();
                }
            }
            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Pesanan berhasil dibatalkan']);
            }
            return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Gagal membatalkan pesanan', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }
}