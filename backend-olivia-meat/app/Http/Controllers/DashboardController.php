<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\PengeluaranHarian;
use App\Models\Piutang;
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
        $totalPengeluaran = PengeluaranHarian::sum('nominal');

        $labaRugi = $totalPemasukan - $totalPengeluaran;

        $totalPiutang = Piutang::whereIn('status_piutang', ['Belum Lunas', 'Sebagian'])
                        ->where('is_deleted', 0)
                        ->sum(DB::raw('total_tagihan - jumlah_terbayar'));

        return view('dashboard', compact('labaRugi', 'totalPemasukan', 'totalPengeluaran', 'totalPiutang'));
    }
}