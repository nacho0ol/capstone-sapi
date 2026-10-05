<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Piutang;
use App\Models\PengeluaranHarian;

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'labarugi');

        // Data Laba Rugi
        $totalPemasukan = Pesanan::where('pesanans.is_deleted', 0)
            ->where('pesanans.status_bayar', 'Lunas')
            ->join('pesanan_details', 'pesanans.id_pesanan', '=', 'pesanan_details.id_pesanan')
            ->sum(\DB::raw('pesanan_details.subtotal'));

        $totalPengeluaran = PengeluaranHarian::sum('nominal'); // No is_deleted on pengeluaran_harian
        $labaRugi = $totalPemasukan - $totalPengeluaran;

        // Data Riwayat Transaksi & Piutang & Pengeluaran
        $pesanan = Pesanan::with(['pelanggan', 'detail.produk'])->where('is_deleted', 0)->orderBy('tgl_order', 'desc')->get();
        $piutang = Piutang::where('is_deleted', 0)->get();
        $pengeluaran = PengeluaranHarian::orderBy('tgl_pengeluaran', 'desc')->get(); // No is_deleted

        return view('keuangan.index', compact('tab', 'totalPemasukan', 'totalPengeluaran', 'labaRugi', 'pesanan', 'piutang', 'pengeluaran'));
    }
}