<?php

namespace App\Http\Controllers;

use App\Models\StokBarang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StokBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = StokBarang::where('is_deleted', 0);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('jenis_daging', 'LIKE', '%' . $search . '%')
                  ->orWhere('catatan', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->filled('filter_status')) {
            $query->where('status_stok', $request->filter_status);
        }

        if ($request->filled('filter_tgl_stok')) {
            $query->whereDate('tgl_stok', $request->filter_tgl_stok);
        }

        $stokBarangs = $query->orderByRaw("FIELD(status_stok, 'Tersedia', 'Sudah Keluar')")
                             ->orderBy('tgl_stok', 'desc')
                             ->orderBy('id_stok', 'desc')
                             ->get();

        $totalBeratTersedia = StokBarang::where('is_deleted', 0)
                                ->where('status_stok', 'Tersedia')
                                ->sum('berat');

        $produks = Produk::where('is_deleted', 0)->orderBy('nama_produk', 'asc')->get();

        return view('stok.index', compact('stokBarangs', 'totalBeratTersedia', 'produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_daging'    => 'required|string|max:100',
            'berat'           => 'required|numeric|min:0.01|max:10000',
            'tgl_stok'        => 'required|date',
            'tgl_keluar_stok' => 'nullable|date|after_or_equal:tgl_stok',
            'status_stok'     => 'required|in:Tersedia,Sudah Keluar',
            'catatan'         => 'nullable|string',
        ], [
            'jenis_daging.required'           => 'Jenis daging wajib diisi.',
            'berat.required'                  => 'Berat stok wajib diisi.',
            'berat.min'                       => 'Berat stok minimal 0,01 kg.',
            'tgl_stok.required'               => 'Tanggal stok masuk wajib diisi.',
            'tgl_keluar_stok.after_or_equal'  => 'Tanggal keluar stok tidak boleh lebih awal dari tanggal masuk.',
        ]);

        $tglKeluar = $request->tgl_keluar_stok;
        if ($request->status_stok === 'Sudah Keluar' && empty($tglKeluar)) {
            $tglKeluar = Carbon::today()->toDateString();
        } elseif ($request->status_stok === 'Tersedia') {
            $tglKeluar = null;
        }

        StokBarang::create([
            'jenis_daging'    => $request->jenis_daging,
            'berat'           => $request->berat,
            'tgl_stok'        => $request->tgl_stok,
            'tgl_keluar_stok' => $tglKeluar,
            'status_stok'     => $request->status_stok,
            'catatan'         => $request->catatan,
            'is_deleted'      => 0,
        ]);

        return redirect()->route('stok.index')->with('success', 'Data stok barang berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_daging'    => 'required|string|max:100',
            'berat'           => 'required|numeric|min:0.01|max:10000',
            'tgl_stok'        => 'required|date',
            'tgl_keluar_stok' => 'nullable|date|after_or_equal:tgl_stok',
            'status_stok'     => 'required|in:Tersedia,Sudah Keluar',
            'catatan'         => 'nullable|string',
        ]);

        $stok = StokBarang::where('id_stok', $id)->where('is_deleted', 0)->firstOrFail();

        $tglKeluar = $request->tgl_keluar_stok;
        if ($request->status_stok === 'Sudah Keluar' && empty($tglKeluar)) {
            $tglKeluar = Carbon::today()->toDateString();
        } elseif ($request->status_stok === 'Tersedia') {
            $tglKeluar = null;
        }

        $stok->update([
            'jenis_daging'    => $request->jenis_daging,
            'berat'           => $request->berat,
            'tgl_stok'        => $request->tgl_stok,
            'tgl_keluar_stok' => $tglKeluar,
            'status_stok'     => $request->status_stok,
            'catatan'         => $request->catatan,
        ]);

        return redirect()->route('stok.index')->with('success', 'Data stok barang berhasil diperbarui!');
    }

    public function tandaiKeluar($id)
    {
        $stok = StokBarang::where('id_stok', $id)->where('is_deleted', 0)->firstOrFail();
        $stok->update([
            'status_stok'     => 'Sudah Keluar',
            'tgl_keluar_stok' => Carbon::today()->toDateString(),
        ]);

        return redirect()->route('stok.index')->with('success', 'Status stok berhasil ditandai Sudah Keluar!');
    }

    public function destroy($id)
    {
        $stok = StokBarang::where('id_stok', $id)->where('is_deleted', 0)->firstOrFail();
        $stok->update(['is_deleted' => 1]);

        return redirect()->route('stok.index')->with('success', 'Data stok barang berhasil dihapus.');
    }
}