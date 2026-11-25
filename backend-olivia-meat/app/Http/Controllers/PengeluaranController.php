<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengeluaranHarian;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('keuangan.index', ['tab' => 'pengeluaran']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tgl_pengeluaran'      => 'required|date',
            'kategori_pengeluaran' => 'required|in:Listrik,Makan,Plastik,Bensin,Gaji,Lain-lain',
            'nominal'              => 'required|integer|min:1001',
            'keterangan'           => 'required|string|min:1'
        ], [
            'nominal.min'          => 'Nominal pengeluaran harus lebih dari Rp 1.000.',
            'keterangan.min'       => 'Keterangan pengeluaran minimal 1 karakter.',
            'keterangan.required'  => 'Kolom keterangan wajib diisi.'
        ]);

        PengeluaranHarian::create([
            'tgl_pengeluaran'      => $request->tgl_pengeluaran,
            'kategori_pengeluaran' => $request->kategori_pengeluaran,
            'id_user'              => auth()->id() ?? 1,
            'nominal'              => $request->nominal,
            'keterangan'           => $request->keterangan,
            'is_deleted'           => 0
        ]);

        return redirect()->route('keuangan.index', ['tab' => 'pengeluaran'])
            ->with('success', 'Catatan pengeluaran harian berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tgl_pengeluaran'      => 'required|date',
            'kategori_pengeluaran' => 'required|in:Listrik,Makan,Plastik,Bensin,Gaji,Lain-lain',
            'nominal'              => 'required|integer|min:1001',
            'keterangan'           => 'required|string|min:1'
        ], [
            'nominal.min'          => 'Nominal pengeluaran harus lebih dari Rp 1.000.',
            'keterangan.min'       => 'Keterangan pengeluaran minimal 1 karakter.',
            'keterangan.required'  => 'Kolom keterangan wajib diisi.'
        ]);

        $pengeluaran = PengeluaranHarian::where('id_pengeluaran', $id)->where('is_deleted', 0)->firstOrFail();
        $pengeluaran->update([
            'tgl_pengeluaran'      => $request->tgl_pengeluaran,
            'kategori_pengeluaran' => $request->kategori_pengeluaran,
            'nominal'              => $request->nominal,
            'keterangan'           => $request->keterangan
        ]);

        return redirect()->route('keuangan.index', ['tab' => 'pengeluaran'])
            ->with('success', 'Pengeluaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pengeluaran = PengeluaranHarian::where('id_pengeluaran', $id)->where('is_deleted', 0)->firstOrFail();
        $pengeluaran->update(['is_deleted' => 1]);

        return redirect()->route('keuangan.index', ['tab' => 'pengeluaran'])
            ->with('success', 'Pengeluaran berhasil dihapus!');
    }
}