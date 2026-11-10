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
    $filterWaktuLaba = $request->get('filter_laba_waktu', '');
    $filterTanggalLaba = $request->get('filter_laba_tanggal', ''); // <-- Tambahan filter tanggal spesifik

    // Base Query Pemasukan (Pesanan Lunas & Tidak Dibatalkan)
    $pemasukanQuery = Pesanan::where('pesanans.is_deleted', 0)
        ->where('pesanans.status_bayar', 'Lunas')
        ->whereNotIn('pesanans.status_pemesanan', ['Ditolak', 'Dibatalkan', 'ditolak', 'dibatalkan'])
        ->join('pesanan_details', 'pesanans.id_pesanan', '=', 'pesanan_details.id_pesanan');

    // Base Query Pengeluaran
    $pengeluaranQuery = PengeluaranHarian::where('is_deleted', 0);

    // Prioritas 1: Jika user memilih tanggal spesifik
    if (!empty($filterTanggalLaba)) {
        $pemasukanQuery->whereDate('pesanans.tgl_order', $filterTanggalLaba);
        $pengeluaranQuery->whereDate('tgl_pengeluaran', $filterTanggalLaba);
    } 
    // Prioritas 2: Jika user memilih rentang waktu cepat (hari/minggu/bulan)
    elseif (!empty($filterWaktuLaba)) {
        if ($filterWaktuLaba == 'hari_ini') {
            $pemasukanQuery->whereDate('pesanans.tgl_order', \Carbon\Carbon::today());
            $pengeluaranQuery->whereDate('tgl_pengeluaran', \Carbon\Carbon::today());
        } elseif ($filterWaktuLaba == 'minggu_ini') {
            $pemasukanQuery->whereBetween('pesanans.tgl_order', [
                \Carbon\Carbon::now()->startOfWeek(), 
                \Carbon\Carbon::now()->endOfWeek()
            ]);
            $pengeluaranQuery->whereBetween('tgl_pengeluaran', [
                \Carbon\Carbon::now()->startOfWeek(), 
                \Carbon\Carbon::now()->endOfWeek()
            ]);
        } elseif ($filterWaktuLaba == 'bulan_ini') {
            $pemasukanQuery->whereMonth('pesanans.tgl_order', \Carbon\Carbon::now()->month)
                           ->whereYear('pesanans.tgl_order', \Carbon\Carbon::now()->year);
            $pengeluaranQuery->whereMonth('tgl_pengeluaran', \Carbon\Carbon::now()->month)
                             ->whereYear('tgl_pengeluaran', \Carbon\Carbon::now()->year);
        }
    }

    $totalPemasukan = $pemasukanQuery->sum(\DB::raw('pesanan_details.subtotal'));
    $totalPengeluaran = $pengeluaranQuery->sum('nominal');
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

    // --- TAMBAHAN FILTER BARU ---
    // Filter Waktu (Hari, Minggu, Bulan Ini)
    if ($request->filled('filter_waktu')) {
        $waktu = $request->filter_waktu;
        if ($waktu == 'hari_ini') {
            $pesananQuery->whereDate('tgl_order', \Carbon\Carbon::today());
        } elseif ($waktu == 'minggu_ini') {
            $pesananQuery->whereBetween('tgl_order', [
                \Carbon\Carbon::now()->startOfWeek(), 
                \Carbon\Carbon::now()->endOfWeek()
            ]);
        } elseif ($waktu == 'bulan_ini') {
            $pesananQuery->whereMonth('tgl_order', \Carbon\Carbon::now()->month)
                         ->whereYear('tgl_order', \Carbon\Carbon::now()->year);
        }
    }

    // Filter Pelanggan Spesifik (Dropdown)
    if ($request->filled('filter_pelanggan')) {
        $pesananQuery->where('id_pelanggan', $request->filter_pelanggan);
    }

    // Filter Status Pembayaran
    if ($request->filled('filter_status_bayar')) {
        $pesananQuery->where('status_bayar', $request->filter_status_bayar);
    }

    // Filter Status Pesanan
    if ($request->filled('filter_status_pesanan')) {
        $pesananQuery->where('status_pemesanan', $request->filter_status_pesanan);
    }

    $pesanan = $pesananQuery->orderBy('tgl_order', 'desc')->orderBy('id_pesanan', 'desc')->get();

    $pesanan = $pesananQuery->orderBy('tgl_order', 'desc')->orderBy('id_pesanan', 'desc')->get();
    //
    // --- QUERY PIUTANG DENGAN FILTER ---
    $piutangQuery = Piutang::with(['pesanan.pelanggan'])->where('is_deleted', 0);

    // 1. Filter Pelanggan
    if ($request->filled('filter_piutang_pelanggan')) {
        $idPelanggan = $request->filter_piutang_pelanggan;
        $piutangQuery->whereHas('pesanan', function($q) use ($idPelanggan) {
            $q->where('id_pelanggan', $idPelanggan);
        });
    }

    // 2. Filter Status Piutang (Belum Lunas / Lunas)
    if ($request->filled('filter_status_piutang')) {
        $piutangQuery->where('status_piutang', $request->filter_status_piutang);
    }

    // 3. Filter Kategori Jatuh Tempo (Overdue / Hari Ini)
    if ($request->filled('filter_jatuh_tempo')) {
        $kategoriTempo = $request->filter_jatuh_tempo;
        $today = \Carbon\Carbon::today();

        if ($kategoriTempo == 'hari_ini') {
            $piutangQuery->whereDate('tgl_jatuh_tempo', $today);
        } elseif ($kategoriTempo == 'overdue') {
            $piutangQuery->whereDate('tgl_jatuh_tempo', '<', $today)
                         ->where('status_piutang', '!=', 'Lunas');
        } elseif ($kategoriTempo == 'aman') {
            $piutangQuery->whereDate('tgl_jatuh_tempo', '>', $today)
                         ->where('status_piutang', '!=', 'Lunas');
        }
    }

    $piutang = $piutangQuery->orderBy('tgl_jatuh_tempo', 'asc')->get();$piutang = Piutang::where('is_deleted', 0)->get();
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