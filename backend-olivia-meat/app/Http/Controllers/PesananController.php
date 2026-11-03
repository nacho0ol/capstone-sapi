<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Piutang;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::with(['pelanggan', 'user', 'detail.produk', 'piutang'])->where('is_deleted', 0);

        if ($request->filled('tanggal')) {
            $query->whereDate('tgl_order', $request->tanggal);
        }
        if ($request->filled('pelanggan_id')) {
            $query->where('id_pelanggan', $request->pelanggan_id);
        }
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $pesanan = $query->orderBy('tgl_order', 'desc')->orderBy('id_pesanan', 'desc')->get();

        if ($request->wantsJson()) {
            return response()->json($pesanan);
        }

        $pelanggans = Pelanggan::where('is_deleted', 0)->orderBy('nama_pelanggan')->get();
        $produks = Produk::where('is_deleted', 0)->orderBy('nama_produk')->get();

        return view('transaksi.index', compact('pesanan', 'pelanggans', 'produks'));
    }

    public function show($id)
    {
        $pesanan = Pesanan::with(['pelanggan', 'user', 'detail.produk', 'piutang'])
                    ->where('is_deleted', 0)
                    ->findOrFail($id);
        return response()->json($pesanan);
    }

    public function store(Request $request)
    {
        $this->validasiPesanan($request, true);

        DB::beginTransaction();
        try {
            $datePrefix = date('Ymd', strtotime($request->tgl_order));
            $count = Pesanan::where('id_pesanan', 'like', 'ORD-' . $datePrefix . '-%')->count() + 1;
            do {
                $id_pesanan = 'ORD-' . $datePrefix . '-' . str_pad($count, 2, '0', STR_PAD_LEFT);
                $count++;
            } while (Pesanan::where('id_pesanan', $id_pesanan)->exists());

            // Hitung total tagihan terlebih dahulu untuk validasi DP
            $total_tagihan = 0;
            $preparedItems = [];
            foreach ($request->items as $item) {
                $produk = Produk::where('is_deleted', 0)->findOrFail($item['id_produk']);
                $subtotal = (int) round($item['qty'] * $produk->harga_jual);
                $total_tagihan += $subtotal;
                $preparedItems[] = [
                    'id_produk' => $produk->id_produk,
                    'qty' => $item['qty'],
                    'harga_jual_saat_ini' => $produk->harga_jual,
                    'subtotal' => $subtotal,
                ];
            }

            $dp = (int) ($request->jumlah_terbayar ?? 0);
            if ($request->metode_bayar === 'Tempo' && $dp > $total_tagihan) {
                DB::rollBack();
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['jumlah_terbayar' => 'Nominal DP / pembayaran awal tidak boleh melebihi total tagihan pesanan (Rp ' . number_format($total_tagihan, 0, ',', '.') . ').']);
            }

            $status_bayar = 'Lunas';
            if ($request->metode_bayar === 'Tempo') {
                if ($dp >= $total_tagihan && $total_tagihan > 0) {
                    $status_bayar = 'Lunas';
                } elseif ($dp > 0) {
                    $status_bayar = 'DP';
                } else {
                    $status_bayar = 'Unpaid';
                }
            }

            $idUser = Auth::id() ?? $request->id_user ?? User::value('id_user') ?? 1;

            $pesananData = [
                'id_pesanan' => $id_pesanan,
                'id_pelanggan' => $request->id_pelanggan,
                'id_user' => $idUser,
                'tgl_order' => $request->tgl_order,
                'metode_bayar' => $request->metode_bayar,
                'status_bayar' => $status_bayar,
                'tgl_antar' => $request->tgl_antar,
                'status_pemesanan' => 'Diterima',
                'is_deleted' => 0,
            ];

            if (Schema::hasColumn('pesanans', 'catatan')) {
                $pesananData['catatan'] = $request->catatan;
            }

            $pesanan = Pesanan::create($pesananData);

            $subtotalGenerated = $this->isSubtotalGenerated();

            foreach ($preparedItems as $itemData) {
                $detailPayload = [
                    'id_pesanan' => $id_pesanan,
                    'id_produk' => $itemData['id_produk'],
                    'qty' => $itemData['qty'],
                    'harga_jual_saat_ini' => $itemData['harga_jual_saat_ini'],
                    'is_deleted' => 0,
                ];

                if (!$subtotalGenerated) {
                    $detailPayload['subtotal'] = $itemData['subtotal'];
                }

                PesananDetail::create($detailPayload);
            }

            // Simpan Piutang Jika Tempo
            if ($request->metode_bayar === 'Tempo') {
                $status_piutang = 'Belum Lunas';
                if ($dp > 0 && $dp < $total_tagihan) {
                    $status_piutang = 'Sebagian';
                } elseif ($dp >= $total_tagihan) {
                    $status_piutang = 'Lunas';
                }

                Piutang::create([
                    'id_pesanan' => $id_pesanan,
                    'tgl_jatuh_tempo' => $request->tgl_jatuh_tempo,
                    'total_tagihan' => $total_tagihan,
                    'jumlah_terbayar' => $dp,
                    'status_piutang' => $status_piutang,
                    'is_deleted' => 0,
                ]);
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Pesanan berhasil dibuat', 'data' => $pesanan], 201);
            }

            return redirect()->route('transaksi.index')->with('success', 'Pesanan berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            $friendlyError = $this->formatDatabaseError($e);
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Gagal membuat pesanan', 'error' => $friendlyError], 500);
            }
            return redirect()->back()->withInput()->with('error', $friendlyError);
        }
    }

    public function edit($id)
    {
        $pesanan = Pesanan::with(['pelanggan', 'detail.produk', 'piutang'])->where('is_deleted', 0)->findOrFail($id);

        if (in_array(strtolower($pesanan->status_pemesanan), ['ditolak', 'dibatalkan'])) {
            return redirect()->route('transaksi.index')->with('error', 'Pesanan yang sudah dibatalkan tidak dapat diubah.');
        }

        $pelanggans = Pelanggan::where('is_deleted', 0)
            ->orWhere('id_pelanggan', $pesanan->id_pelanggan)
            ->orderBy('nama_pelanggan')
            ->get();

        $produks = Produk::where('is_deleted', 0)->orderBy('nama_produk')->get();

        return view('transaksi.edit', compact('pesanan', 'pelanggans', 'produks'));
    }

    public function update(Request $request, $id)
    {
        $pesanan = Pesanan::where('is_deleted', 0)->findOrFail($id);

        if (in_array(strtolower($pesanan->status_pemesanan), ['ditolak', 'dibatalkan'])) {
            return redirect()->route('transaksi.index')->with('error', 'Pesanan yang sudah dibatalkan tidak dapat diubah.');
        }

        $this->validasiPesanan($request, false, $pesanan->id_pelanggan);

        DB::beginTransaction();
        try {
            $total_tagihan = 0;
            $preparedItems = [];
            foreach ($request->items as $item) {
                $produk = Produk::findOrFail($item['id_produk']);
                $subtotal = (int) round($item['qty'] * $produk->harga_jual);
                $total_tagihan += $subtotal;
                $preparedItems[] = [
                    'id_produk' => $produk->id_produk,
                    'qty' => $item['qty'],
                    'harga_jual_saat_ini' => $produk->harga_jual,
                    'subtotal' => $subtotal,
                ];
            }

            $piutang = Piutang::where('id_pesanan', $pesanan->id_pesanan)->first();
            $dp = (int) ($request->jumlah_terbayar ?? ($piutang ? $piutang->jumlah_terbayar : 0));

            if ($request->metode_bayar === 'Tempo' && $dp > $total_tagihan) {
                DB::rollBack();
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['jumlah_terbayar' => 'Nominal DP / pembayaran tidak boleh melebihi total tagihan pesanan (Rp ' . number_format($total_tagihan, 0, ',', '.') . ').']);
            }

            $status_bayar = 'Lunas';
            if ($request->metode_bayar === 'Tempo') {
                if ($dp >= $total_tagihan && $total_tagihan > 0) {
                    $status_bayar = 'Lunas';
                } elseif ($dp > 0) {
                    $status_bayar = 'DP';
                } else {
                    $status_bayar = 'Unpaid';
                }
            }

            $updateData = [
                'id_pelanggan' => $request->id_pelanggan,
                'tgl_order' => $request->tgl_order,
                'metode_bayar' => $request->metode_bayar,
                'status_bayar' => $status_bayar,
                'tgl_antar' => $request->tgl_antar,
            ];

            if (Schema::hasColumn('pesanans', 'catatan')) {
                $updateData['catatan'] = $request->catatan;
            }

            $pesanan->update($updateData);

            PesananDetail::where('id_pesanan', $pesanan->id_pesanan)->delete();

            $subtotalGenerated = $this->isSubtotalGenerated();

            foreach ($preparedItems as $itemData) {
                $detailPayload = [
                    'id_pesanan' => $pesanan->id_pesanan,
                    'id_produk' => $itemData['id_produk'],
                    'qty' => $itemData['qty'],
                    'harga_jual_saat_ini' => $itemData['harga_jual_saat_ini'],
                    'is_deleted' => 0,
                ];

                if (!$subtotalGenerated) {
                    $detailPayload['subtotal'] = $itemData['subtotal'];
                }

                PesananDetail::create($detailPayload);
            }

            if ($request->metode_bayar === 'Tempo') {
                $status_piutang = 'Belum Lunas';
                if ($dp > 0 && $dp < $total_tagihan) {
                    $status_piutang = 'Sebagian';
                } elseif ($dp >= $total_tagihan) {
                    $status_piutang = 'Lunas';
                }

                $tglJatuhTempo = $request->tgl_jatuh_tempo ?? ($piutang ? $piutang->tgl_jatuh_tempo : date('Y-m-d', strtotime($request->tgl_order . ' +7 days')));

                if ($piutang) {
                    $piutang->update([
                        'tgl_jatuh_tempo' => $tglJatuhTempo,
                        'total_tagihan' => $total_tagihan,
                        'jumlah_terbayar' => $dp,
                        'status_piutang' => $status_piutang,
                        'is_deleted' => 0,
                    ]);
                } else {
                    Piutang::create([
                        'id_pesanan' => $pesanan->id_pesanan,
                        'tgl_jatuh_tempo' => $tglJatuhTempo,
                        'total_tagihan' => $total_tagihan,
                        'jumlah_terbayar' => $dp,
                        'status_piutang' => $status_piutang,
                        'is_deleted' => 0,
                    ]);
                }
            } else {
                if ($piutang) {
                    $piutang->update(['is_deleted' => 1]);
                }
            }

            DB::commit();
            return redirect()->route('transaksi.index')->with('success', 'Pesanan berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $this->formatDatabaseError($e));
        }
    }

    public function cancel($id, Request $request)
    {
        $pesanan = Pesanan::where('is_deleted', 0)->findOrFail($id);

        if (in_array(strtolower($pesanan->status_pemesanan), ['ditolak', 'dibatalkan'])) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Pesanan ini sudah dibatalkan sebelumnya.'], 422);
            }
            return redirect()->back()->with('error', 'Pesanan ini sudah dibatalkan sebelumnya.');
        }

        if (strtolower($pesanan->status_pemesanan) === 'selesai') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Pesanan yang sudah selesai tidak dapat dibatalkan.'], 422);
            }
            return redirect()->back()->with('error', 'Pesanan yang sudah selesai tidak dapat dibatalkan.');
        }

        DB::beginTransaction();
        try {
            $pesanan->status_pemesanan = $this->supportsDibatalkanStatus() ? 'Dibatalkan' : 'Ditolak';
            $pesanan->save();

            if ($pesanan->metode_bayar === 'Tempo') {
                $piutang = Piutang::where('id_pesanan', $pesanan->id_pesanan)->first();
                if ($piutang) {
                    $piutang->is_deleted = 1;
                    $piutang->save();
                }
            }
            DB::commit();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Pesanan berhasil dibatalkan']);
            }
            return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            $friendlyError = $this->formatDatabaseError($e);
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Gagal membatalkan pesanan', 'error' => $friendlyError], 500);
            }
            return redirect()->back()->with('error', $friendlyError);
        }
    }

    private function validasiPesanan(Request $request, bool $isStore = true, $currentPelangganId = null): void
    {
        $pelangganRule = Rule::exists('pelanggans', 'id_pelanggan')->where(function ($query) use ($currentPelangganId) {
            if ($currentPelangganId) {
                $query->where('is_deleted', 0)->orWhere('id_pelanggan', $currentPelangganId);
            } else {
                $query->where('is_deleted', 0);
            }
        });

        $rules = [
            'id_pelanggan'      => ['required', $pelangganRule],
            'tgl_order'         => 'required|date|after_or_equal:2015-01-01|before_or_equal:2100-12-31',
            'metode_bayar'      => 'required|in:Tunai,Transfer,Tempo',
            'tgl_antar'         => 'required|date|after_or_equal:2015-01-01|after_or_equal:tgl_order|before_or_equal:2100-12-31',
            'items'             => 'required|array|min:1',
            'items.*.id_produk' => [
                'required',
                Rule::exists('produks', 'id_produk')->where('is_deleted', 0),
            ],
            'items.*.qty'       => 'required|numeric|min:0.01|max:99999',
            'tgl_jatuh_tempo'   => 'nullable|required_if:metode_bayar,Tempo|date|after_or_equal:tgl_order|before_or_equal:2100-12-31',
            'jumlah_terbayar'   => 'nullable|numeric|min:0',
            'catatan'           => 'nullable|string|max:500',
        ];

        if ($isStore) {
            $rules['id_user'] = 'nullable|exists:users,id_user';
        }

        $messages = [
            'id_pelanggan.required'          => 'Pelanggan wajib dipilih.',
            'id_pelanggan.exists'            => 'Pelanggan yang dipilih tidak valid atau sudah dihapus.',
            'id_user.exists'                 => 'Data pengguna tidak valid.',
            'tgl_order.required'             => 'Tanggal order wajib diisi.',
            'tgl_order.date'                 => 'Format tanggal order tidak valid.',
            'tgl_order.after_or_equal'       => 'Tanggal order tidak valid (minimal tahun 2015).',
            'tgl_order.before_or_equal'      => 'Tanggal order tidak valid.',
            'metode_bayar.required'          => 'Metode bayar wajib dipilih.',
            'metode_bayar.in'                => 'Metode bayar harus Tunai, Transfer, atau Tempo.',
            'tgl_antar.required'             => 'Tanggal antar wajib diisi.',
            'tgl_antar.date'                 => 'Format tanggal antar tidak valid.',
            'tgl_antar.after_or_equal'       => 'Tanggal antar tidak boleh kurang dari tanggal order (dan minimal tahun 2015).',
            'tgl_antar.before_or_equal'      => 'Tanggal antar tidak valid.',
            'items.required'                 => 'Minimal harus ada 1 item produk dalam pesanan.',
            'items.min'                      => 'Minimal harus ada 1 item produk dalam pesanan.',
            'items.*.id_produk.required'     => 'Produk pada setiap baris item wajib dipilih.',
            'items.*.id_produk.exists'       => 'Produk yang dipilih tidak tersedia atau sudah dihapus.',
            'items.*.qty.required'           => 'Jumlah (Qty) produk wajib diisi.',
            'items.*.qty.numeric'            => 'Jumlah (Qty) produk harus berupa angka.',
            'items.*.qty.min'                => 'Jumlah (Qty) produk minimal 0.01 kg.',
            'items.*.qty.max'                => 'Jumlah (Qty) produk melebihi batas maksimal.',
            'tgl_jatuh_tempo.required_if'    => 'Tanggal jatuh tempo wajib diisi untuk metode bayar Tempo.',
            'tgl_jatuh_tempo.date'           => 'Format tanggal jatuh tempo tidak valid.',
            'tgl_jatuh_tempo.after_or_equal' => 'Tanggal jatuh tempo tidak boleh kurang dari tanggal order.',
            'tgl_jatuh_tempo.before_or_equal'=> 'Tanggal jatuh tempo tidak valid.',
            'jumlah_terbayar.numeric'        => 'Nominal DP / pembayaran awal harus berupa angka.',
            'jumlah_terbayar.min'            => 'Nominal DP / pembayaran awal tidak boleh kurang dari 0.',
            'catatan.max'                    => 'Catatan pesanan maksimal 500 karakter.',
        ];

        $request->validate($rules, $messages);
    }

    private function isSubtotalGenerated(): bool
    {
        try {
            $col = DB::selectOne("
                SELECT EXTRA 
                FROM information_schema.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'pesanan_details' 
                  AND COLUMN_NAME = 'subtotal'
            ");
            return $col && stripos($col->EXTRA ?? '', 'GENERATED') !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function supportsDibatalkanStatus(): bool
    {
        try {
            $col = DB::selectOne("
                SELECT COLUMN_TYPE 
                FROM information_schema.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'pesanans' 
                  AND COLUMN_NAME = 'status_pemesanan'
            ");
            return $col && stripos($col->COLUMN_TYPE ?? '', 'dibatalkan') !== false;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function formatDatabaseError(\Exception $e): string
    {
        $msg = $e->getMessage();

        if (stripos($msg, 'chk_tgl_antar') !== false || stripos($msg, 'tgl_antar') !== false) {
            return 'Tanggal antar tidak valid. Pastikan tanggal antar minimal tahun 2015 dan tidak kurang dari tanggal order.';
        }
        if (stripos($msg, 'chk_tgl_order') !== false || stripos($msg, 'tgl_order') !== false) {
            return 'Tanggal order tidak valid. Pastikan tanggal order minimal tahun 2015.';
        }
        if (stripos($msg, 'chk_qty') !== false || stripos($msg, "column 'qty'") !== false) {
            return 'Jumlah (Qty) produk tidak valid atau melebihi kapasitas maksimal.';
        }
        if (stripos($msg, 'chk_jumlah_terbayar') !== false) {
            return 'Nominal DP / pembayaran tidak boleh melebihi total tagihan atau kurang dari 0.';
        }
        if (stripos($msg, 'chk_total_tagihan') !== false) {
            return 'Total tagihan pesanan tidak valid (maksimal Rp 100.000.000).';
        }
        if (stripos($msg, 'SQLSTATE') !== false) {
            return 'Gagal memproses data pesanan. Pastikan seluruh tanggal dan data produk sudah diisi dengan benar.';
        }

        return 'Terjadi kesalahan: ' . $msg;
    }
}
