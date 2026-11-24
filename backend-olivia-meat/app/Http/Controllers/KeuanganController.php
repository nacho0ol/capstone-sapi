<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pesanan;
use App\Models\Piutang;
use App\Models\PengeluaranHarian;
use App\Models\PengaturanSistem;
use App\Models\Produk;
use App\Models\Pelanggan;
use Carbon\Carbon;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'labarugi');

        // ==========================================
        // 1. DATA LABA RUGI (3 KOMPONEN)
        // ==========================================
        $filterWaktuLaba = $request->get('filter_laba_waktu', '');
        $filterTanggalLaba = $request->get('filter_laba_tanggal', '');

        // A. Uang Masuk dari Orderan (Pesanan Lunas & Tidak Dibatalkan)
        $pemasukanQuery = Pesanan::where('pesanans.is_deleted', 0)
            ->where('pesanans.status_bayar', 'Lunas')
            ->whereNotIn('pesanans.status_pemesanan', ['Ditolak', 'Dibatalkan', 'ditolak', 'dibatalkan'])
            ->join('pesanan_details', 'pesanans.id_pesanan', '=', 'pesanan_details.id_pesanan')
            ->where('pesanan_details.is_deleted', 0);

        // B. Yang Harus Dibayarkan ke Jagal (Otomatis dari qty x harga_awal orderan aktif)
        $jagalQuery = Pesanan::where('pesanans.is_deleted', 0)
            ->whereNotIn('pesanans.status_pemesanan', ['Ditolak', 'Dibatalkan', 'ditolak', 'dibatalkan'])
            ->join('pesanan_details', 'pesanans.id_pesanan', '=', 'pesanan_details.id_pesanan')
            ->join('produks', 'pesanan_details.id_produk', '=', 'produks.id_produk')
            ->where('pesanan_details.is_deleted', 0);

        // C. Pengeluaran Operasional
        $pengeluaranQuery = PengeluaranHarian::where('is_deleted', 0);

        // Terapkan Filter Tanggal Spesifik atau Rentang Waktu
        if (!empty($filterTanggalLaba)) {
            $pemasukanQuery->whereDate('pesanans.tgl_order', $filterTanggalLaba);
            $jagalQuery->whereDate('pesanans.tgl_order', $filterTanggalLaba);
            $pengeluaranQuery->whereDate('tgl_pengeluaran', $filterTanggalLaba);
        } elseif (!empty($filterWaktuLaba)) {
            if ($filterWaktuLaba == 'hari_ini') {
                $pemasukanQuery->whereDate('pesanans.tgl_order', Carbon::today());
                $jagalQuery->whereDate('pesanans.tgl_order', Carbon::today());
                $pengeluaranQuery->whereDate('tgl_pengeluaran', Carbon::today());
            } elseif ($filterWaktuLaba == 'minggu_ini') {
                $range = [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
                $pemasukanQuery->whereBetween('pesanans.tgl_order', $range);
                $jagalQuery->whereBetween('pesanans.tgl_order', $range);
                $pengeluaranQuery->whereBetween('tgl_pengeluaran', $range);
            } elseif ($filterWaktuLaba == 'bulan_ini') {
                $pemasukanQuery->whereMonth('pesanans.tgl_order', Carbon::now()->month)
                               ->whereYear('pesanans.tgl_order', Carbon::now()->year);
                $jagalQuery->whereMonth('pesanans.tgl_order', Carbon::now()->month)
                           ->whereYear('pesanans.tgl_order', Carbon::now()->year);
                $pengeluaranQuery->whereMonth('tgl_pengeluaran', Carbon::now()->month)
                                 ->whereYear('tgl_pengeluaran', Carbon::now()->year);
            }
        }

        $totalPemasukan = $pemasukanQuery->sum(DB::raw('pesanan_details.subtotal'));
        $totalBayarJagal = $jagalQuery->sum(DB::raw('pesanan_details.qty * produks.harga_awal'));
        $totalPengeluaran = $pengeluaranQuery->sum('nominal');
        $labaRugi = $totalPemasukan - $totalBayarJagal - $totalPengeluaran;

        // ==========================================
        // 2. QUERY DAFTAR PESANAN + FILTER
        // ==========================================
        $pesananQuery = Pesanan::with(['pelanggan', 'detail.produk', 'piutang'])->where('is_deleted', 0);

        if ($request->filled('search_pesanan')) {
            $search = $request->search_pesanan;
            $pesananQuery->where(function ($q) use ($search) {
                $q->where('id_pesanan', 'LIKE', '%' . $search . '%')
                  ->orWhere('status_pemesanan', 'LIKE', '%' . $search . '%')
                  ->orWhere('status_bayar', 'LIKE', '%' . $search . '%')
                  ->orWhere('catatan', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('pelanggan', function ($sub) use ($search) {
                      $sub->where('nama_pelanggan', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('filter_waktu')) {
            $waktu = $request->filter_waktu;
            if ($waktu == 'hari_ini') {
                $pesananQuery->whereDate('tgl_order', Carbon::today());
            } elseif ($waktu == 'minggu_ini') {
                $pesananQuery->whereBetween('tgl_order', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($waktu == 'bulan_ini') {
                $pesananQuery->whereMonth('tgl_order', Carbon::now()->month)
                             ->whereYear('tgl_order', Carbon::now()->year);
            }
        }

        if ($request->filled('filter_pelanggan')) {
            $pesananQuery->where('id_pelanggan', $request->filter_pelanggan);
        }

        if ($request->filled('filter_status_bayar')) {
            $pesananQuery->where('status_bayar', $request->filter_status_bayar);
        }

        if ($request->filled('filter_status_pesanan')) {
            $pesananQuery->where('status_pemesanan', $request->filter_status_pesanan);
        }

        $pesanan = $pesananQuery->orderBy('tgl_order', 'desc')->orderBy('id_pesanan', 'desc')->get();

        // ==========================================
        // 3. QUERY TAGIHAN (TABEL PIUTANG) + FILTER
        // ==========================================
        $piutangQuery = Piutang::with(['pesanan.pelanggan'])->where('is_deleted', 0);

        if ($request->filled('filter_piutang_pelanggan')) {
            $idPelanggan = $request->filter_piutang_pelanggan;
            $piutangQuery->whereHas('pesanan', function ($q) use ($idPelanggan) {
                $q->where('id_pelanggan', $idPelanggan);
            });
        }

        if ($request->filled('filter_status_piutang')) {
            if ($request->filter_status_piutang === 'Belum Lunas') {
                $piutangQuery->where('status_piutang', '!=', 'Lunas');
            } else {
                $piutangQuery->where('status_piutang', $request->filter_status_piutang);
            }
        }

        if ($request->filled('filter_jatuh_tempo')) {
            $kategoriTempo = $request->filter_jatuh_tempo;
            $today = Carbon::today();

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

        $piutang = $piutangQuery->orderBy('tgl_jatuh_tempo', 'asc')->get();

        // ==========================================
        // 4. QUERY PENGELUARAN OPERASIONAL + FILTER
        // ==========================================
        $pengeluaranFilterQuery = PengeluaranHarian::where('is_deleted', 0);

        if ($request->filled('filter_tanggal')) {
            $pengeluaranFilterQuery->whereDate('tgl_pengeluaran', $request->filter_tanggal);
        }

        if ($request->filled('filter_kategori')) {
            $pengeluaranFilterQuery->where('kategori_pengeluaran', $request->filter_kategori);
        }

        $pengeluaran = $pengeluaranFilterQuery->orderBy('tgl_pengeluaran', 'desc')->get();
        $pengaturan = PengaturanSistem::first();

        $produks = Produk::where('is_deleted', 0)->get();
        $pelanggans = Pelanggan::where('is_deleted', 0)->get();

        return view('keuangan.index', compact(
            'tab',
            'totalPemasukan',
            'totalBayarJagal',
            'totalPengeluaran',
            'labaRugi',
            'pesanan',
            'piutang',
            'pengeluaran',
            'pengaturan',
            'produks',
            'pelanggans'
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
            'batas_toleransi_global' => 'nullable|integer|min:0'
        ]);

        $pengaturan = PengaturanSistem::first() ?? new PengaturanSistem();

        $pengaturan->is_notif_aktif = $request->is_notif_aktif;
        $pengaturan->telegram_bot_token = $request->telegram_bot_token;
        $pengaturan->telegram_chat_id = $request->telegram_chat_id;
        $pengaturan->h_minus_notifikasi = $request->h_minus_notifikasi;
        $pengaturan->template_tagihan = $request->template_tagihan;
        if ($request->filled('batas_toleransi_global')) {
            $pengaturan->batas_toleransi_global = $request->batas_toleransi_global;
        }

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
            'tgl_pengeluaran'      => 'required|date',
            'kategori_pengeluaran' => 'required|in:Listrik,Makan,Plastik,Bensin,Gaji',
            'nominal'              => 'required|integer|min:1001|max:100000000',
            'keterangan'           => 'required|string|min:1'
        ], [
            'tgl_pengeluaran.required'      => 'Tanggal pengeluaran wajib diisi.',
            'kategori_pengeluaran.required' => 'Kategori pengeluaran wajib dipilih.',
            'nominal.min'                   => 'Nominal minimal Rp 1.001.',
            'keterangan.required'           => 'Keterangan wajib diisi.'
        ]);

        PengeluaranHarian::create([
            'tgl_pengeluaran'      => $request->tgl_pengeluaran,
            'kategori_pengeluaran' => $request->kategori_pengeluaran,
            'id_user'              => auth()->id(),
            'nominal'              => $request->nominal,
            'keterangan'           => $request->keterangan,
            'is_deleted'           => 0,
        ]);

        return redirect()->route('keuangan.index', ['tab' => 'pengeluaran'])->with('success', 'Catatan pengeluaran berhasil disimpan!');
    }
}