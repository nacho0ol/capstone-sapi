<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengeluaranHarian;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $pengeluaran = PengeluaranHarian::where('is_deleted', 0)
                            ->orderBy('tgl_pengeluaran', 'desc')
                            ->get();
                            
        return view('pengeluaran.index', compact('pengeluaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_pengeluaran' => 'required|date',
            'kategori_pengeluaran' => 'required|in:Makan,Pembelian Daging,Operasional,Transportasi,Lain-lain',
            'nominal' => 'required|integer|min:1000',
            'keterangan' => 'required|string|min:5'
        ]);

        PengeluaranHarian::create([
            'tgl_pengeluaran' => $request->tgl_pengeluaran,
            'kategori_pengeluaran' => $request->kategori_pengeluaran,
            'id_user' => 1, 
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan,
            'is_deleted' => 0       
        ]);

        return redirect()->back()->with('success', 'Catatan pengeluaran harian berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_pengeluaran' => 'required|date',
            'kategori_pengeluaran' => 'required|in:Makan,Pembelian Daging,Operasional,Transportasi,Lain-lain',
            'nominal' => 'required|integer|min:1000',
            'keterangan' => 'required|string|min:5'
        ]);

        $pengeluaran = PengeluaranHarian::where('id_pengeluaran', $id)->where('is_deleted', 0)->firstOrFail();
        $pengeluaran->update([
            'tgl_pengeluaran' => $request->tgl_pengeluaran,
            'kategori_pengeluaran' => $request->kategori_pengeluaran,
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->back()->with('success', 'Pengeluaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pengeluaran = PengeluaranHarian::where('id_pengeluaran', $id)->where('is_deleted', 0)->firstOrFail();
        $pengeluaran->update(['is_deleted' => 1]);

        return redirect()->back()->with('success', 'Pengeluaran berhasil dihapus!');
    }
}