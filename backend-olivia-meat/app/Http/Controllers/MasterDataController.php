<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Pelanggan;

class MasterDataController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'produk');

        $produkQuery = Produk::where('is_deleted', 0);
        if ($request->filled('search_produk')) {
            $search = $request->search_produk;
            $produkQuery->where(function ($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '%' . $search . '%')
                ->orWhere('kategori', 'LIKE', '%' . $search . '%')
                ->orWhere('nama_jagal', 'LIKE', '%' . $search . '%');
            });
        }
        $produk = $produkQuery->get();
        
        // 1. Buat Query Builder (JANGAN panggil ->get() di sini)
        $pelangganQuery = Pelanggan::where('is_deleted', 0);

        // 2. Filter Pencarian jika ada input search_pelanggan
        if ($request->filled('search_pelanggan')) {
            $search = $request->search_pelanggan;
            $pelangganQuery->where(function ($q) use ($search) {
                $q->where('nama_pelanggan', 'LIKE', '%' . $search . '%')
                  ->orWhere('no_telp', 'LIKE', '%' . $search . '%')
                  ->orWhere('alamat', 'LIKE', '%' . $search . '%');
            });
        }

        // 3. Eksekusi query di paling akhir
        $pelanggan = $pelangganQuery->get();

        return view('master.index', compact('produk', 'pelanggan', 'tab'));
    }

    // ==========================================
    // BAGIAN PRODUK
    // ==========================================

    public function storeProduk(Request $request)
    {
        $this->validasiProduk($request);
        Produk::create(array_merge($request->all(), ['is_deleted' => 0]));
        return redirect()->route('master.index', ['tab' => 'produk'])->with('success', 'Produk baru berhasil disimpan!');
    }

    public function updateProduk(Request $request, $id)
    {
        $this->validasiProduk($request);
        Produk::where('id_produk', $id)->update($request->only(['nama_produk', 'kategori', 'harga_awal', 'harga_jual', 'nama_jagal']));
        return redirect()->route('master.index', ['tab' => 'produk'])->with('success', 'Data produk berhasil diperbarui!');
    }

    public function deleteProduk($id)
    {
        Produk::where('id_produk', $id)->update(['is_deleted' => 1]);
        return redirect()->route('master.index', ['tab' => 'produk'])->with('success', 'Produk berhasil dihapus.');
    }

    // ==========================================
    // BAGIAN PELANGGAN
    // ==========================================

    public function storePelanggan(Request $request)
    {
        $this->validasiPelanggan($request);
        $pelanggan = Pelanggan::create(array_merge($request->all(), ['is_deleted' => 0]));
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Pelanggan berhasil disimpan', 'data' => $pelanggan], 201);
        }
        return redirect()->route('master.index', ['tab' => 'pelanggan'])->with('success', 'Pelanggan baru berhasil disimpan!');
    }

    public function updatePelanggan(Request $request, $id)
    {
        $this->validasiPelanggan($request);
        Pelanggan::where('id_pelanggan', $id)->update($request->only(['nama_pelanggan', 'no_telp', 'alamat', 'batas_toleransi_khusus']));
        return redirect()->route('master.index', ['tab' => 'pelanggan'])->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    public function deletePelanggan($id)
    {
        Pelanggan::where('id_pelanggan', $id)->update(['is_deleted' => 1]);
        return redirect()->route('master.index', ['tab' => 'pelanggan'])->with('success', 'Pelanggan berhasil dihapus.');
    }

    // ==========================================
    // FUNGSI VALIDASI KUSTOM (Biar Nggak Redundan)
    // ==========================================

    private function validasiProduk(Request $request)
    {
        $request->validate([
            // Regex: hanya huruf, angka, dan spasi
            'nama_produk' => ['required', 'string', 'min:3', 'regex:/^[a-zA-Z]{3}[a-zA-Z0-9\s]*$/'],
            'kategori'    => 'required|in:Daging,Tulang,Buntut,Jeroan,Kaki',
            // Harga disamakan dengan constraint DB: > 1000 dan <= 1000000
            'harga_awal'  => 'required|integer|min:1001|max:1000000',
            'harga_jual'  => 'required|integer|gte:harga_awal|max:1000000',
            // Regex: hanya huruf dan spasi
            'nama_jagal'  => ['required', 'string', 'min:3', 'regex:/^[a-zA-Z\s]+$/']
        ], [
            // Pesan error ramah pengguna (ditampilkan di frontend)
            'nama_produk.regex' => 'Nama produk hanya boleh berisi huruf dan angka di akhir (tanpa simbol).',
            'nama_jagal.min'  => 'Nama jagal setidaknya terdiri dari 3 karakter.',
            'harga_awal.min'    => 'Harga modal/awal harus lebih dari Rp 1.000.',
            'harga_awal.max'    => 'Harga modal/awal maksimal Rp 1.000.000.',
            'harga_jual.gte'    => 'Harga jual tidak boleh lebih kecil dari harga modal.',
            'harga_jual.max'    => 'Harga jual maksimal Rp 1.000.000.'
        ]);
    }

    private function validasiPelanggan(Request $request)
    {
        $request->validate([
            // Regex: harus diawali huruf, dan selanjutnya hanya huruf/spasi
            'nama_pelanggan' => ['required', 'string', 'min:3', 'regex:/^[a-zA-Z][a-zA-Z\s]*$/'],
            // Regex: harus diawali 08, total digit 10-15
            'no_telp'        => ['required', 'regex:/^08[0-9]{8,13}$/'],
            'alamat'         => 'required|string|min:5',
            'batas_toleransi_khusus' => 'nullable|integer|min:0'
        ], [
            'nama_pelanggan.regex' => 'Nama pelanggan harus diawali huruf dan tidak boleh mengandung angka/simbol.',
            'no_telp.regex'        => 'Nomor telepon tidak valid. Harus diawali "08" dan terdiri dari 10-15 digit angka.',
            'batas_toleransi_khusus.min' => 'Batas toleransi tidak boleh kurang dari 0 hari.'
        ]);
    }
}