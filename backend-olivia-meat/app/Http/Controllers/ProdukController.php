<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\Produk;
use App\Models\Jagal;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $tab =$request->get('tab', 'produk');

        // Query Produk
        $produkQuery = Produk::with('jagal')->where('is_deleted', 0);
        if ($request->filled('search_produk')) {
            $search =$request->search_produk;
            $produkQuery->where(function ($q) use ($search) {
                $q->where('nama_produk', 'LIKE', '\%' .$search . '%')
                  ->orWhere('kategori', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('jagal', function ($qj) use ($search) {
                      $qj->where('nama_jagal', 'LIKE', '\%' .$search . '%');
                  });
            });
        }
        $produk =$produkQuery->orderBy('nama_produk', 'asc')->get();

        // Query Jagal
        $jagalQuery = Jagal::where('is_deleted', 0);
        if ($request->filled('search_jagal')) {
            $searchJagal =$request->search_jagal;
            $jagalQuery->where(function ($q) use ($searchJagal) {
                $q->where('nama_jagal', 'LIKE', '\%' .$searchJagal . '%')
                  ->orWhere('keterangan', 'LIKE', '%' . $searchJagal . '%');
            });
        }
        $jagals =$jagalQuery->orderBy('nama_jagal', 'asc')->get();

        // Daftar semua jagal aktif untuk dropdown form produk
        $listJagal = Jagal::where('is_deleted', 0)->orderBy('nama_jagal', 'asc')->get();

        return view('produk.index', compact('produk', 'jagals', 'listJagal', 'tab'));
    }

    // ==========================================
    // CRUD PRODUK
    // ==========================================

    public function store(Request $request)
    {
        $this->validasiProduk($request);
        $jagal = Jagal::findOrFail($request->id_jagal);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath =$request->file('foto')->store('produk', 'public');
        }

        Produk::create([
            'nama_produk' => $request->nama_produk,
            'foto'        => $fotoPath,
            'kategori'    => $request->kategori,
            'harga_awal'  => $request->harga_awal,
            'harga_jual'  => $request->harga_jual,
            'id_jagal'    => $jagal->id_jagal,
            'nama_jagal'  => $jagal->nama_jagal,
            'is_deleted'  => 0,
        ]);

        return redirect()->route('produk.index', ['tab' => 'produk'])->with('success', 'Produk baru berhasil disimpan!');
    }

    public function update(Request $request,$id)
    {
        $this->validasiProduk($request);
        $produk = Produk::where('id_produk',$id)->firstOrFail();
        $jagal = Jagal::findOrFail($request->id_jagal);

        $fotoPath =$produk->foto;
        if ($request->hasFile('foto')) {
            if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
                Storage::disk('public')->delete($produk->foto);
            }
            $fotoPath =$request->file('foto')->store('produk', 'public');
        }

        $produk->update([
            'nama_produk' => $request->nama_produk,
            'foto'        => $fotoPath,
            'kategori'    => $request->kategori,
            'harga_awal'  => $request->harga_awal,
            'harga_jual'  => $request->harga_jual,
            'id_jagal'    => $jagal->id_jagal,
            'nama_jagal'  => $jagal->nama_jagal,
        ]);

        return redirect()->route('produk.index', ['tab' => 'produk'])->with('success', 'Data produk berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $produk = Produk::where('id_produk',$id)->firstOrFail();
        if ($produk->foto && Storage::disk('public')->exists($produk->foto)) {
            Storage::disk('public')->delete($produk->foto);
        }
        $produk->update(['is_deleted' => 1, 'foto' => null]);

        return redirect()->route('produk.index', ['tab' => 'produk'])->with('success', 'Produk berhasil dihapus.');
    }

    // ==========================================
    // CRUD JAGAL
    // ==========================================

    public function storeJagal(Request $request)
    {
        $request->merge([
            'nama_jagal' => preg_replace('/\s+/', ' ', trim((string) $request->nama_jagal)),
        ]);

        $request->validate([
            'nama_jagal' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z\s]+$/',
                Rule::unique('jagals', 'nama_jagal')->where('is_deleted', 0),
            ],
            'keterangan' => 'nullable|string',
        ], [
            'nama_jagal.min'    => 'Nama jagal minimal 3 karakter.',
            'nama_jagal.max'    => 'Nama jagal maksimal 30 karakter.',
            'nama_jagal.regex'  => 'Nama jagal hanya boleh berisi huruf dan spasi.',
            'nama_jagal.unique' => 'Nama jagal sudah terdaftar, silakan gunakan nama lain.',
        ]);

        Jagal::create([
            'nama_jagal' => $request->nama_jagal,
            'keterangan' => $request->keterangan,
            'is_deleted' => 0,
        ]);

        return redirect()->route('produk.index', ['tab' => 'jagal'])->with('success', 'Data jagal baru berhasil ditambahkan!');
    }

    public function updateJagal(Request $request,$id)
    {
        $request->merge([
            'nama_jagal' => preg_replace('/\s+/', ' ', trim((string) $request->nama_jagal)),
        ]);

        $request->validate([
            'nama_jagal' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z\s]+$/',
                Rule::unique('jagals', 'nama_jagal')->where('is_deleted', 0)->ignore($id, 'id_jagal'),
            ],
            'keterangan' => 'nullable|string',
        ], [
            'nama_jagal.min'    => 'Nama jagal minimal 3 karakter.',
            'nama_jagal.max'    => 'Nama jagal maksimal 30 karakter.',
            'nama_jagal.regex'  => 'Nama jagal hanya boleh berisi huruf dan spasi.',
            'nama_jagal.unique' => 'Nama jagal sudah digunakan oleh data jagal lain.',
        ]);

        Jagal::where('id_jagal', $id)->update([
            'nama_jagal' => $request->nama_jagal,
            'keterangan' => $request->keterangan,
        ]);

        Produk::where('id_jagal', $id)->update(['nama_jagal' =>$request->nama_jagal]);

        return redirect()->route('produk.index', ['tab' => 'jagal'])->with('success', 'Data jagal berhasil diperbarui!');
    }

    public function destroyJagal($id)
    {
        $masihDipakai = Produk::where('id_jagal',$id)->where('is_deleted', 0)->exists();
        if ($masihDipakai) {
            return redirect()->route('produk.index', ['tab' => 'jagal'])
                ->with('error', 'Jagal tidak bisa dihapus karena masih terhubung dengan produk aktif.');
        }

        Jagal::where('id_jagal', $id)->update(['is_deleted' => 1]);
        return redirect()->route('produk.index', ['tab' => 'jagal'])->with('success', 'Data jagal berhasil dihapus.');
    }

    private function validasiProduk(Request $request)
    {
        $request->validate([
            'nama_produk' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[a-zA-Z]{3}[a-zA-Z0-9\s]*$/'],
            'foto'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kategori'    => 'required|in:Daging,Tulang,Buntut,Jeroan,Kaki',
            'harga_awal'  => 'required|integer|min:1001|max:1000000',
            'harga_jual'  => 'required|integer|gte:harga_awal|max:1000000',
            'id_jagal'    => 'required|exists:jagals,id_jagal',
        ], [
            'nama_produk.regex' => 'Nama produk minimal 3 huruf di awal dan tanpa simbol.',
            'foto.image'        => 'File foto produk harus berupa gambar.',
            'foto.mimes'        => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'foto.max'          => 'Ukuran foto maksimal 2 MB.',
            'harga_awal.min'    => 'Harga modal/awal harus lebih dari Rp 1.000.',
            'harga_awal.max'    => 'Harga modal/awal maksimal Rp 1.000.000.',
            'harga_jual.gte'    => 'Harga jual tidak boleh lebih kecil dari harga modal.',
            'harga_jual.max'    => 'Harga jual maksimal Rp 1.000.000.',
            'id_jagal.required' => 'Silakan pilih jagal terlebih dahulu.',
        ]);
    }
}