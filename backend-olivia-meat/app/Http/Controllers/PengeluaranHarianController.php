<?php

namespace App\Http\Controllers;

use App\Models\PengeluaranHarian;
use Illuminate\Http\Request;

class PengeluaranHarianController extends Controller
{
    public function index(Request $request)
    {
        $query = PengeluaranHarian::with('user');

        if ($request->has('tanggal')) {
            $query->whereDate('tgl_pengeluaran', $request->tanggal);
        }
        
        if ($request->has('kategori')) {
            $query->where('kategori_pengeluaran', $request->kategori);
        }

        $pengeluaran = $query->orderBy('tgl_pengeluaran', 'desc')->get();
        return response()->json($pengeluaran);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl_pengeluaran' => 'required|date',
            'kategori_pengeluaran' => 'required|in:Makan,Pembelian Daging,Operasional,Transportasi,Lain-lain',
            'nominal' => 'required|integer|min:1000',
            'keterangan' => 'required|string|min:5',
            'id_user' => 'required|exists:users,id_user'
        ]);

        $pengeluaran = PengeluaranHarian::create($validated);

        return redirect()->back()->with('success', 'Pengeluaran berhasil dicatat');
    }

    public function show($id)
    {
        $pengeluaran = PengeluaranHarian::with('user')->findOrFail($id);
        return response()->json($pengeluaran);
    }

    public function update(Request $request, $id)
    {
        $pengeluaran = PengeluaranHarian::findOrFail($id);
        
        $validated = $request->validate([
            'tgl_pengeluaran' => 'sometimes|date',
            'kategori_pengeluaran' => 'sometimes|in:Makan,Pembelian Daging,Operasional,Transportasi,Lain-lain',
            'nominal' => 'sometimes|integer|min:1000',
            'keterangan' => 'sometimes|string|min:5',
        ]);

        $pengeluaran->update($validated);

        return redirect()->back()->with('success', 'Data pengeluaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        $pengeluaran = PengeluaranHarian::findOrFail($id);
        $pengeluaran->delete();

        return redirect()->back()->with('success', 'Data pengeluaran berhasil dihapus');
    }
}
