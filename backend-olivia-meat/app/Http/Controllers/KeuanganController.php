<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\Piutang;
use App\Models\PengeluaranHarian;
use App\Models\PengaturanSistem; 

class KeuanganController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'labarugi');

        // Data Laba Rugi
        $totalPemasukan = Pesanan::where('pesanans.is_deleted', 0)
            ->where('pesanans.status_bayar', 'Lunas')
            ->join('pesanan_details', 'pesanans.id_pesanan', '=', 'pesanan_details.id_pesanan')
            ->sum(\DB::raw('pesanan_details.subtotal'));

        $totalPengeluaran = PengeluaranHarian::sum('nominal'); // No is_deleted on pengeluaran_harian
        $labaRugi = $totalPemasukan - $totalPengeluaran;

        $pesanan = Pesanan::with(['pelanggan', 'detail.produk'])->where('is_deleted', 0)->orderBy('tgl_order', 'desc')->get();
        $piutang = Piutang::where('is_deleted', 0)->get();
        $pengeluaran = PengeluaranHarian::orderBy('tgl_pengeluaran', 'desc')->get(); // No is_deleted
        
        $pengaturan = PengaturanSistem::first();

        return view('keuangan.index', compact('tab', 'totalPemasukan', 'totalPengeluaran', 'labaRugi', 'pesanan', 'piutang', 'pengeluaran', 'pengaturan'));
    }

    public function updateNotifikasi(Request $request)
    {
        $request->validate([
            'is_notif_aktif' => 'required|boolean',
            'telegram_bot_token' => 'nullable|string',
            'telegram_chat_id' => 'nullable|string',
            'h_minus_notifikasi' => 'nullable|array',
            'template_tagihan' => 'required|string'
        ]);

        $pengaturan = PengaturanSistem::first();
        if (!$pengaturan) {
            $pengaturan = new PengaturanSistem();
        }

        // Update nilainya
        $pengaturan->is_notif_aktif = $request->is_notif_aktif;
        $pengaturan->telegram_bot_token = $request->telegram_bot_token;
        $pengaturan->telegram_chat_id = $request->telegram_chat_id;
        $pengaturan->h_minus_notifikasi = $request->h_minus_notifikasi; 
        $pengaturan->template_tagihan = $request->template_tagihan;
        
        $pengaturan->save();

        return redirect()->back()->with('success', 'Pengaturan notifikasi Telegram berhasil disimpan!');
    }

    public function testNotifikasi()
    {
        $pengaturan = PengaturanSistem::first();
        if (!$pengaturan || empty($pengaturan->telegram_bot_token) || empty($pengaturan->telegram_chat_id)) {
            return redirect()->back()->with('error', 'Silakan isi dan simpan Token Bot & Chat ID terlebih dahulu sebelum tes.');
        }

        $pesan = "🎉 <b>TES KONEKSI TELEGRAM BERHASIL!</b>\n\nSistem Olivia Meat N Fresh telah berhasil terhubung dengan Telegram Anda pada " . date('d-m-Y H:i:s') . ".";
        $hasil = \App\Services\TelegramService::sendMessage($pengaturan->telegram_chat_id, $pesan, $pengaturan->telegram_bot_token);

        if ($hasil['success']) {
            return redirect()->back()->with('success', '✅ Sukses! Pesan tes berhasil dikirim ke akun Telegram Anda.');
        } else {
            return redirect()->back()->with('error', '❌ ' . $hasil['message'] . '. Pastikan Anda sudah membuka bot di Telegram dan menekan tombol START.');
        }
    }

    public function kirimPengingatManual()
    {
        $hasil = \App\Services\TelegramService::kirimPengingatJatuhTempo();
        if ($hasil['success']) {
            if (isset($hasil['terkirim']) && $hasil['terkirim'] > 0) {
                return redirect()->back()->with('success', "✅ {$hasil['message']}");
            }
            return redirect()->back()->with('info', "ℹ️ {$hasil['message']}");
        }
        return redirect()->back()->with('error', "❌ {$hasil['message']}");
    }
}