<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PemasokanDaging;
use App\Models\Produk;
use App\Models\Jagal;
use Carbon\Carbon;

class PemasokanDagingController extends Controller
{
    public function index(Request $request)
    {
        $query = PemasokanDaging::with(['produk', 'jagal'])->where('is_deleted', 0);

        // Search nama produk atau catatan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('catatan', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('produk', function ($qp) use ($search) {
                      $qp->where('nama_produk', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Filter Jagal
        if ($request->filled('filter_jagal')) {
            $query->where('id_jagal', $request->filter_jagal);
        }

        // Filter Tanggal Spesifik atau Rentang Waktu
        if ($request->filled('filter_tanggal')) {
            $query->whereDate('tgl_pemasokan', $request->filter_tanggal);
        } elseif ($request->filled('filter_waktu')) {
            if ($request->filter_waktu == 'hari_ini') {
                $query->whereDate('tgl_pemasokan', Carbon::today());
            } elseif ($request->filter_waktu == 'minggu_ini') {
                $query->whereBetween('tgl_pemasokan', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
            } elseif ($request->filter_waktu == 'bulan_ini') {
                $query->whereMonth('tgl_pemasokan', Carbon::now()->month)
                      ->whereYear('tgl_pemasokan', Carbon::now()->year);
            }
        }

        $pemasokan = $query->orderBy('tgl_pemasokan', 'desc')
                           ->orderBy('id_pemasokan', 'desc')
                           ->get();

        $totalBerat = $pemasokan->sum('jumlah');
        $totalNominal = $pemasokan->sum('total_harga');

        $produks = Produk::with('jagal')->where('is_deleted', 0)->orderBy('nama_produk', 'asc')->get();
        $jagals = Jagal::where('is_deleted', 0)->orderBy('nama_jagal', 'asc')->get();

        return view('pemasokan.index', compact(
            'pemasokan',
            'totalBerat',
            'totalNominal',
            'produks',
            'jagals'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produk'     => 'required|exists:produks,id_produk',
            'id_jagal'      => 'nullable|exists:jagals,id_jagal',
            'jumlah'        => 'required|numeric|gt:0|max:1000',
            'total_harga'   => 'nullable|integer|min:0',
            'tgl_pemasokan' => 'required|date',
            'catatan'       => 'nullable|string',
        ], [
            'id_produk.required'     => 'Produk daging wajib dipilih.',
            'jumlah.gt'              => 'Jumlah pemasokan harus lebih dari 0 kg.',
            'jumlah.max'             => 'Jumlah pemasokan maksimal 1.000 kg per pencatatan.',
            'tgl_pemasokan.required' => 'Tanggal pemasokan wajib diisi.',
        ]);

        $produk = Produk::findOrFail($request->id_produk);
        $idJagal = $request->id_jagal ?: $produk->id_jagal;

        if (!$idJagal) {
            return back()->withInput()->with('error', 'Produk ini belum memiliki data jagal. Silakan pilih jagal terlebih dahulu.');
        }

        // Jika total_harga kosong, hitung otomatis dari jumlah x harga_awal produk
        $totalHarga = $request->filled('total_harga')
            ? $request->total_harga
            : (int) round($request->jumlah * $produk->harga_awal);

        PemasokanDaging::create([
            'id_produk'     => $produk->id_produk,
            'id_jagal'      => $idJagal,
            'jumlah'        => $request->jumlah,
            'total_harga'   => $totalHarga,
            'tgl_pemasokan' => $request->tgl_pemasokan,
            'catatan'       => $request->catatan,
            'is_deleted'    => 0,
        ]);

        return redirect()->route('pemasokan.index')->with('success', 'Data pemasokan daging berhasil dicatat!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_produk'     => 'required|exists:produks,id_produk',
            'id_jagal'      => 'required|exists:jagals,id_jagal',
            'jumlah'        => 'required|numeric|gt:0|max:1000',
            'total_harga'   => 'required|integer|min:0',
            'tgl_pemasokan' => 'required|date',
            'catatan'       => 'nullable|string',
        ], [
            'jumlah.gt'  => 'Jumlah pemasokan harus lebih dari 0 kg.',
            'jumlah.max' => 'Jumlah pemasokan maksimal 1.000 kg per pencatatan.',
        ]);

        PemasokanDaging::where('id_pemasokan', $id)->update([
            'id_produk'     => $request->id_produk,
            'id_jagal'      => $request->id_jagal,
            'jumlah'        => $request->jumlah,
            'total_harga'   => $request->total_harga,
            'tgl_pemasokan' => $request->tgl_pemasokan,
            'catatan'       => $request->catatan,
        ]);

        return redirect()->route('pemasokan.index')->with('success', 'Data pemasokan daging berhasil diperbarui!');
    }

    public function destroy($id)
    {
        PemasokanDaging::where('id_pemasokan', $id)->update(['is_deleted' => 1]);
        return redirect()->route('pemasokan.index')->with('success', 'Data pemasokan daging berhasil dihapus.');
    }
}