<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Pesanan;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        $pelangganQuery = Pelanggan::where('is_deleted', 0);

        if ($request->filled('search_pelanggan')) {
            $search = $request->search_pelanggan;
            $pelangganQuery->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'LIKE', '%' . $search . '%')
                  ->orWhere('no_telp', 'LIKE', '%' . $search . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $search . '%');
            });
        }

        $pelanggan = $pelangganQuery->orderBy('nama_pelanggan', 'asc')->get();

        return view('pelanggan.index', compact('pelanggan'));
    }

    public function show($id)
    {
        $pelanggan = Pelanggan::where('is_deleted', 0)->findOrFail($id);

        $riwayatPesanan = Pesanan::with(['detail.produk', 'piutang'])
            ->where('id_pelanggan', $id)
            ->where('is_deleted', 0)
            ->orderBy('tgl_order', 'desc')
            ->orderBy('id_pesanan', 'desc')
            ->get();

        // Hitung ringkasan transaksi pelanggan (kecuali pesanan yang dibatalkan)
        $pesananAktif = $riwayatPesanan->filter(function ($p) {
            return !in_array(strtolower($p->status_pemesanan), ['ditolak', 'dibatalkan']);
        });

        $totalBelanja = $pesananAktif->sum(function ($p) {
            return $p->detail->sum('subtotal');
        });

        $sisaTagihan = $pesananAktif->sum(function ($p) {
            if ($p->piutang && $p->piutang->status_piutang !== 'Lunas') {
                return max(0, $p->piutang->total_tagihan - $p->piutang->jumlah_terbayar);
            }
            return 0;
        });

        $jumlahKomplain = $riwayatPesanan->filter(fn($p) => !empty(trim($p->catatan)))->count();

        return view('pelanggan.show', compact(
            'pelanggan',
            'riwayatPesanan',
            'totalBelanja',
            'sisaTagihan',
            'jumlahKomplain'
        ));
    }

    public function store(Request $request)
    {
        $this->validasiPelanggan($request);
        $pelanggan = Pelanggan::create(array_merge($request->all(), ['is_deleted' => 0]));

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Pelanggan berhasil disimpan', 'data' => $pelanggan], 201);
        }

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan baru berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $this->validasiPelanggan($request);
        Pelanggan::where('id_pelanggan', $id)->update(
            $request->only(['nama_pelanggan', 'no_telp', 'alamat', 'batas_toleransi_khusus'])
        );

        return redirect()->route('pelanggan.index')->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Pelanggan::where('id_pelanggan', $id)->update(['is_deleted' => 1]);
        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus.');
    }

    private function validasiPelanggan(Request $request)
    {
        $request->validate([
            'nama_pelanggan'         => ['required', 'string', 'min:3', 'regex:/^[a-zA-Z][a-zA-Z\s]*$/'],
            'no_telp'                => ['required', 'regex:/^08[0-9]{8,13}$/'],
            'alamat'                 => 'required|string|min:5',
            'batas_toleransi_khusus' => 'nullable|integer|min:0'
        ], [
            'nama_pelanggan.regex'       => 'Nama pelanggan harus diawali huruf dan tidak boleh mengandung angka/simbol.',
            'no_telp.regex'              => 'Nomor telepon tidak valid. Harus diawali "08" dan terdiri dari 10-15 digit angka.',
            'batas_toleransi_khusus.min' => 'Batas toleransi tidak boleh kurang dari 0 hari.'
        ]);
    }
}