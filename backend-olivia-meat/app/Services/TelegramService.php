<?php

namespace App\Services;

use App\Models\PengaturanSistem;
use App\Models\Piutang;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Kirim pesan teks ke Telegram melalui Bot API
     */
    public static function sendMessage($chatId, $text, $botToken = null)
    {
        if (!$botToken) {
            $pengaturan = PengaturanSistem::first();
            $botToken = $pengaturan->telegram_bot_token ?? null;
        }

        if (!$botToken || !$chatId) {
            return [
                'success' => false,
                'message' => 'Telegram Bot Token atau Chat ID belum dikonfigurasi.'
            ];
        }

        try {
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
            $response = Http::timeout(10)->post($url, [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML'
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Pesan berhasil terkirim ke Telegram!'
                ];
            } else {
                $errorMsg = $response->json('description') ?? 'Gagal menghubungi Telegram API.';
                Log::error('Telegram API Error: ' . $errorMsg);
                return [
                    'success' => false,
                    'message' => 'Telegram Error: ' . $errorMsg
                ];
            }
        } catch (\Throwable $e) {
            Log::error('Telegram Service Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Koneksi error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Alias untuk konsistensi penamaan
     */
    public static function kirimPengingatJatuhTempo()
    {
        return (new self())->kirimPengingatPiutang();
    }

    /**
     * Logika utama: Kueri piutang belum lunas yang mendekati / melewati jatuh tempo
     * dan kirimkan notifikasi ke Telegram Pemilik (Sesuai Proposal Capstone)
     */
    public function kirimPengingatPiutang()
    {
        $pengaturan = PengaturanSistem::first();

        if (!$pengaturan || !$pengaturan->is_notif_aktif) {
            return [
                'success' => false,
                'terkirim' => 0,
                'message' => 'Notifikasi Telegram dinonaktifkan di sistem.'
            ];
        }

        if (empty($pengaturan->telegram_bot_token) || empty($pengaturan->telegram_chat_id)) {
            return [
                'success' => false,
                'terkirim' => 0,
                'message' => 'Bot Token atau Chat ID belum diatur di menu Keuangan.'
            ];
        }

        $hMinusConfig = $pengaturan->h_minus_notifikasi ?? [];
        if (is_string($hMinusConfig)) {
            $hMinusConfig = json_decode($hMinusConfig, true) ?? [];
        }

        // Ambil semua piutang yang belum lunas
        $daftarPiutang = Piutang::with('pesanan.pelanggan')
            ->where('is_deleted', 0)
            ->whereIn('status_piutang', ['Belum Lunas', 'Sebagian'])
            ->get();

        $today = Carbon::today();
        $jumlahTerkirim = 0;
        $detailTerkirim = [];
        $lastError = null;

        foreach ($daftarPiutang as $piutang) {
            $jatuhTempo = Carbon::parse($piutang->tgl_jatuh_tempo)->startOfDay();
            $selisihHari = (int) $today->diffInDays($jatuhTempo, false); // false agar selisih bertanda (- jika lewat)

            // Cek apakah selisih hari cocok dengan H- yang dicentang atau sudah lewat jatuh tempo
            $harusKirim = false;
            $keteranganWaktu = '';

            if ($selisihHari === 0 && in_array('0', $hMinusConfig)) {
                $harusKirim = true;
                $keteranganWaktu = '🚨 <b>[HARI INI JATUH TEMPO]</b>';
            } elseif ($selisihHari > 0 && in_array((string)$selisihHari, $hMinusConfig)) {
                $harusKirim = true;
                $keteranganWaktu = "⏳ <b>[H-{$selisihHari} JATUH TEMPO]</b>";
            } elseif ($selisihHari < 0) {
                // Lewat jatuh tempo
                $harusKirim = true;
                $hariLewat = abs($selisihHari);
                $keteranganWaktu = "⚠️ <b>[LEWAT JATUH TEMPO ({$hariLewat} hari)]</b>";
            }

            if ($harusKirim) {
                $namaPelanggan = $piutang->pesanan->pelanggan->nama_pelanggan ?? 'Pelanggan';
                $sisaTagihan = $piutang->total_tagihan - $piutang->jumlah_terbayar;
                $tglJatuhTempoFormat = Carbon::parse($piutang->tgl_jatuh_tempo)->translatedFormat('d F Y');

                // Template pengganti
                $pesan = $pengaturan->template_tagihan ?? "Halo {nama},\nTagihan piutang Anda sebesar Rp {tagihan} akan jatuh tempo pada {jatuh_tempo}.";
                $pesan = str_replace(
                    ['{nama}', '{tagihan}', '{jatuh_tempo}'],
                    [$namaPelanggan, number_format($sisaTagihan, 0, ',', '.'), $tglJatuhTempoFormat],
                    $pesan
                );

                // Tambahkan header & detail pesanan untuk pemilik
                $pesanFinal = "📢 <b>PENGINGAT PIUTANG OLIVIA MEAT</b>\n"
                    . "{$keteranganWaktu}\n"
                    . "No. Order: <b>{$piutang->id_pesanan}</b>\n\n"
                    . $pesan . "\n\n"
                    . "<i>Status: {$piutang->status_piutang} | Terbayar: Rp " . number_format($piutang->jumlah_terbayar, 0, ',', '.') . "</i>";

                $res = $this->sendMessage($pengaturan->telegram_chat_id, $pesanFinal, $pengaturan->telegram_bot_token);
                if ($res['success']) {
                    $jumlahTerkirim++;
                    $detailTerkirim[] = "{$piutang->id_pesanan} ({$namaPelanggan})";
                } else {
                    $lastError = $res['message'];
                }
            }
        }

        $msg = "Berhasil memproses pengingat piutang. Terkirim {$jumlahTerkirim} notifikasi.";
        if ($jumlahTerkirim === 0 && $lastError) {
            $msg .= " (Kendala Telegram: {$lastError}. Pastikan Anda sudah membuka bot di Telegram dan klik tombol START).";
        }

        return [
            'success' => true,
            'terkirim' => $jumlahTerkirim,
            'detail' => $detailTerkirim,
            'message' => $msg
        ];
    }
}
