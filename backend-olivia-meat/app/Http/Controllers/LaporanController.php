<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\PengeluaranHarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function labaRugi(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // 1. Hitung Total Pemasukan (Pesanan yang sudah Lunas pada rentang waktu)
        // Menurut logika akuntansi dasar, pemasukan bisa dihitung dari pesanan yang sudah lunas.
        // Untuk yang tempo, kita bisa menghitung piutang yang statusnya lunas atau menggunakan jumlah_terbayar.
        // Untuk kesederhanaan sesuai proposal: kita hitung subtotal semua pesanan berstatus "Lunas" + total terbayar pada piutang di rentang waktu tersebut.
        // Cara lebih presisi: Ambil Pesanan yang Lunas (Tunai/Transfer) dan ambil pembayaran piutang.
        
        $pemasukanLangsung = Pesanan::whereBetween('tgl_order', [$startDate, $endDate])
                            ->where('status_bayar', 'Lunas')
                            ->where('metode_bayar', '!=', 'Tempo')
                            ->where('is_deleted', 0)
                            ->with('detail')
                            ->get()
                            ->sum(function ($pesanan) {
                                return $pesanan->detail->sum('subtotal');
                            });

        // Pemasukan dari Piutang (Jika ingin akurat harusnya ada tabel log pembayaran piutang, 
        // namun berdasarkan ERD kita hanya punya `jumlah_terbayar` di tabel Piutang.
        // Kita asumsikan jumlah_terbayar dihitung berdasarkan pesanan di rentang waktu tersebut).
        $pemasukanPiutang = DB::table('piutangs')
                            ->join('pesanans', 'piutangs.id_pesanan', '=', 'pesanans.id_pesanan')
                            ->whereBetween('pesanans.tgl_order', [$startDate, $endDate])
                            ->where('piutangs.is_deleted', 0)
                            ->sum('piutangs.jumlah_terbayar');
        
        $totalPemasukan = $pemasukanLangsung + $pemasukanPiutang;

        // 2. Hitung Total Pengeluaran Harian
        $totalPengeluaran = PengeluaranHarian::whereBetween('tgl_pengeluaran', [$startDate, $endDate])
                            ->sum('nominal');

        // 3. Hitung Laba/Rugi
        $labaRugi = $totalPemasukan - $totalPengeluaran;

        return response()->json([
            'periode' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'ringkasan' => [
                'total_pemasukan' => $totalPemasukan,
                'total_pengeluaran' => $totalPengeluaran,
                'laba_rugi' => $labaRugi
            ]
        ]);
    }
}
