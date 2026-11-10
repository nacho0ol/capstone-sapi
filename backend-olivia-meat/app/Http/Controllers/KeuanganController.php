<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Piutang;
use App\Models\PengeluaranHarian;
use App\Models\PengaturanSistem; 

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'labarugi');

    // 1. Data Laba Rugi
    $totalPemasukan = Pesanan::where('pesanans.is_deleted', 0)
        ->where('pesanans.status_bayar', 'Lunas')
        ->whereNotIn('pesanans.status_pemesanan', ['Ditolak', 'Dibatalkan', 'ditolak', 'dibatalkan'])
        ->join('pesanan_details', 'pesanans.id_pesanan', '=', 'pesanan_details.id_pesanan')
        ->sum(\DB::raw('pesanan_details.subtotal'));

    $totalPengeluaran = PengeluaranHarian::sum('nominal');
    $labaRugi = $totalPemasukan - $totalPengeluaran;

    // 2. Query Pesanan + Filter Search
    $pesananQuery = Pesanan::with(['pelanggan', 'detail.produk', 'piutang'])->where('is_deleted', 0);

    if ($request->filled('search_pesanan')) {
        $search = $request->search_pesanan;
        $pesananQuery->where(function ($q) use ($search) {
            $q->where('id_pesanan', 'LIKE', '%' . $search . '%')
              ->orWhere('status_pemesanan', 'LIKE', '%' . $search . '%')
              ->orWhere('status_bayar', 'LIKE', '%' . $search . '%')
              ->orWhereHas('pelanggan', function ($sub) use ($search) {
                  $sub->where('nama_pelanggan', 'LIKE', '%' . $search . '%');
              });
        });
    }

    $pesanan = $pesananQuery->orderBy('tgl_order', 'desc')->orderBy('id_pesanan', 'desc')->get();
    $piutang = Piutang::where('is_deleted', 0)->get();
    // LOGIKA FILTER PENGELUARAN DI KEUANGAN CONTROLLER
    $pengeluaranQuery = PengeluaranHarian::where('is_deleted', 0);

    if ($request->filled('filter_tanggal')) {
        $pengeluaranQuery->whereDate('tgl_pengeluaran', $request->filter_tanggal);
    }

    if ($request->filled('filter_kategori')) {
        $pengeluaranQuery->where('kategori_pengeluaran', $request->filter_kategori);
    }

    $pengeluaran = $pengeluaranQuery->orderBy('tgl_pengeluaran', 'desc')->get();
    $pengaturan = PengaturanSistem::first();

    // 3. Data Produk & Pelanggan untuk Modal Buat Pesanan Baru
    $produks = \App\Models\Produk::where('is_deleted', 0)->get();
    $pelanggans = \App\Models\Pelanggan::where('is_deleted', 0)->get();

    return view('keuangan.index', compact(
        'tab', 'totalPemasukan', 'totalPengeluaran', 'labaRugi', 
        'pesanan', 'piutang', 'pengeluaran', 'pengaturan', 'produks', 'pelanggans'
    ));
    }

    public function updateNotifikasi(Request $request)
    {
        $request->validate([
            'is_notif_aktif' => 'required|boolean',
            'telegram_bot_token' => 'nullable|string',
            'telegram_chat_id' => 'nullable|string',
            'h_minus_notifikasi' => 'nullable|array',
            'template_tagihan' => 'required|string',
            'batas_toleransi_global' => 'required|integer|min:0' // Tambahan baru
        ]);

        $pengaturan = PengaturanSistem::first() ?? new PengaturanSistem();

        $pengaturan->is_notif_aktif = $request->is_notif_aktif;
        $pengaturan->telegram_bot_token = $request->telegram_bot_token;
        $pengaturan->telegram_chat_id = $request->telegram_chat_id;
        $pengaturan->h_minus_notifikasi = $request->h_minus_notifikasi; 
        $pengaturan->template_tagihan = $request->template_tagihan;
        $pengaturan->batas_toleransi_global = $request->batas_toleransi_global; // Simpan nilai global
        
        $pengaturan->save();

        return redirect()->back()->with('success', 'Pengaturan notifikasi Telegram berhasil disimpan!');
    }

    public function testNotifikasi()
    {
        $pengaturan = PengaturanSistem::first();
        if (!$pengaturan || empty($pengaturan->telegram_bot_token) || empty($pengaturan->telegram_chat_id)) {
            return redirect()->back()->with('error', 'Silakan isi dan simpan Token Bot & Chat ID terlebih dahulu sebelum tes.');
        }

        $pesan = "🎉 <b>TES KONEKSI TELEGRAM BERHASIL!</b>\n\nSistem Olivia Meat N Fresh telah berhasil terhubung dengan Telegram Anda pada " . date('d-m-Y H:i:s') . ".";
        $hasil = \App\Services\TelegramService::sendMessage($pengaturan->telegram_chat_id, $pesan, $pengaturan->telegram_bot_token);

        if ($hasil['success']) {
            return redirect()->back()->with('success', '✅ Sukses! Pesan tes berhasil dikirim ke akun Telegram Anda.');
        } else {
            return redirect()->back()->with('error', '❌ ' . $hasil['message'] . '. Pastikan Anda sudah membuka bot di Telegram dan menekan tombol START.');
        }
    }

    public function kirimPengingatManual()
    {
        $hasil = \App\Services\TelegramService::kirimPengingatJatuhTempo();
        if ($hasil['success']) {
            if (isset($hasil['terkirim']) && $hasil['terkirim'] > 0) {
                return redirect()->back()->with('success', "✅ {$hasil['message']}");
            }
            return redirect()->back()->with('info', "ℹ️ {$hasil['message']}");
        }
        return redirect()->back()->with('error', "❌ {$hasil['message']}");
    }

    public function storePengeluaran(Request $request)
    {
        $request->validate([
            'tgl_pengeluaran' => 'required|date',
            'kategori'        => 'required|string',
            'nominal'         => 'required|numeric|min:1',
            'keterangan'      => 'nullable|string'
        ], [
            'tgl_pengeluaran.required' => 'Tanggal pengeluaran wajib diisi.',
            'kategori.required'        => 'Kategori pengeluaran wajib diisi.',
            'nominal.required'         => 'Nominal pengeluaran wajib diisi.',
            'nominal.min'              => 'Nominal minimal Rp 1.'
        ]);

        PengeluaranHarian::create([
            'tgl_pengeluaran' => $request->tgl_pengeluaran,
            'kategori'        => $request->kategori,
            'nominal'         => $request->nominal,
            'keterangan'      => $request->keterangan,
        ]);

        return redirect()->route('keuangan.index', ['tab' => 'pengeluaran'])->with('success', 'Catatan pengeluaran berhasil disimpan!');
    }
}