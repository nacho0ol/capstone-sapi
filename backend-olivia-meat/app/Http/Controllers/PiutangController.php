<?php

namespace App\Http\Controllers;

use App\Models\Piutang;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator; 

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
        $piutang = Piutang::where('is_deleted', 0)->where('id_pesanan', $id)->firstOrFail();
        $sisaTagihan = $piutang->total_tagihan - $piutang->jumlah_terbayar;

        // Minimal Rp 1000. Tapi kalau sisa tagihan nyangkut di bawah 1000, minimalnya ngikutin sisa tagihan biar bisa dilunasi.
        $minBayar = $sisaTagihan < 1000 ? $sisaTagihan : 1000;

        $validator = Validator::make($request->all(), [
            'nominal_bayar' => 'required|numeric|min:' . $minBayar . '|max:' . $sisaTagihan
        ], [
            'nominal_bayar.required' => 'Nominal pembayaran wajib diisi.',
            'nominal_bayar.numeric' => 'Nominal pembayaran harus berupa angka.',
            'nominal_bayar.min' => 'Pembayaran cicilan minimal adalah Rp ' . number_format($minBayar, 0, ',', '.') . '.',
            'nominal_bayar.max' => 'Nominal bayar tidak boleh melebihi sisa tagihan (Rp ' . number_format($sisaTagihan, 0, ',', '.') . ').'
        ]);
        if ($validator->fails()) {
            // Jika gagal, kembali ke halaman sebelumnya dengan membawa pesan error DAN identitas popup yang gagal
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_modal_id', $id)
                ->with('error_modal_sisa', $sisaTagihan);
        }
        
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