<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\PengeluaranHarian;
use App\Models\Piutang;
use App\Models\PemasokanDaging;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Pemasukan dari Pesanan Lunas
        $pemasukanLangsung = Pesanan::where('status_bayar', 'Lunas')
                            ->where('metode_bayar', '!=', 'Tempo')
                            ->where('is_deleted', 0)
                            ->whereNotIn('status_pemesanan', ['Ditolak', 'Dibatalkan', 'ditolak', 'dibatalkan'])
                            ->with('detail')
                            ->get()
                            ->sum(function ($pesanan) {
                                return $pesanan->detail->sum('subtotal');
                            });

        // Pemasukan dari cicilan piutang
        $pemasukanPiutang = Piutang::where('is_deleted', 0)
                            ->sum('jumlah_terbayar');
        
        $totalPemasukan = $pemasukanLangsung + $pemasukanPiutang;

        // Pengeluaran Harian
        $totalPengeluaran = PengeluaranHarian::where('is_deleted', 0)->sum('nominal');

        $labaRugi = $totalPemasukan - $totalPengeluaran;

        $totalPiutang = Piutang::whereIn('status_piutang', ['Belum Lunas', 'Sebagian'])
                        ->where('is_deleted', 0)
                        ->sum(DB::raw('total_tagihan - jumlah_terbayar'));

        // Daftar Pemesanan Hari Ini
        $orderanHariIni = Pesanan::with(['pelanggan', 'detail.produk'])
                            ->where('is_deleted', 0)
                            ->whereDate('tgl_order', Carbon::today())
                            ->orderBy('id_pesanan', 'desc')
                            ->get();

        // Daftar Pemasokan Daging Hari Ini
        $pemasokanHariIni = PemasokanDaging::with(['produk', 'jagal'])
                            ->where('is_deleted', 0)
                            ->whereDate('tgl_pemasokan', Carbon::today())
                            ->orderBy('id_pemasokan', 'desc')
                            ->get();

        return view('dashboard', compact(
            'labaRugi',
            'totalPemasukan',
            'totalPengeluaran',
            'totalPiutang',
            'orderanHariIni',
            'pemasokanHariIni'
        ));
    }
}