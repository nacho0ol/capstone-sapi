<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PiutangController extends Controller
{
    public function index(Request $request)
    {
        $query = Piutang::with('pesanan.pelanggan')->where('is_deleted', 0);
        
        if ($request->has('status')) {
            $query->where('status_piutang', $request->status);
        }

        $piutang = $query->orderBy('tgl_jatuh_tempo', 'asc')->get();
        
        if ($request->wantsJson()) {
            return response()->json($piutang);
        }

        return view('piutang.index', compact('piutang'));
    }

    public function show($id, Request $request)
    {
        $piutang = Piutang::with('pesanan.pelanggan')->where('is_deleted', 0)->where('id_pesanan', $id)->firstOrFail();
        return response()->json($piutang);
    }

    public function bayar(Request $request, $id) // alias for api
    {
        return $this->updatePembayaran($request, $id);
    }

    public function updatePembayaran(Request $request, $id)
    {
        $request->validate([
            'nominal_bayar' => 'required|integer|min:1'
        ]);

        DB::beginTransaction();
        try {
            $piutang = Piutang::where('is_deleted', 0)->where('id_pesanan', $id)->firstOrFail();
            $pesanan = Pesanan::find($piutang->id_pesanan); // Tarik data pesanan terkait
            
            $nominalBayar = $request->nominal_bayar;
            $piutang->jumlah_terbayar += $nominalBayar;

            if ($piutang->jumlah_terbayar >= $piutang->total_tagihan) {
                $piutang->jumlah_terbayar = $piutang->total_tagihan; 
                $piutang->status_piutang = 'Lunas';

                if ($pesanan) {
                    $pesanan->status_bayar = 'Lunas';
                    $pesanan->save();
                }
            } else {
                $piutang->status_piutang = 'Sebagian';
                
               
                if ($pesanan) {
                    $pesanan->status_bayar = 'DP'; 
                    $pesanan->save();
                }
            }

            $piutang->save();
            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Pembayaran piutang berhasil dicatat', 'data' => $piutang]);
            }
            return redirect()->back()->with('success', 'Pembayaran cicilan berhasil dicatat!');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Gagal mencatat pembayaran piutang', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal mencatat pembayaran: ' . $e->getMessage());
        }
    }
}