<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Services\TelegramService;

Artisan::command('piutang:kirim-notifikasi', function () {
    $this->info('⏳ Memeriksa data piutang mendekati/melewati jatuh tempo...');
    $hasil = TelegramService::kirimPengingatJatuhTempo();
    if ($hasil['success']) {
        $this->info('✅ ' . $hasil['message']);
        if (!empty($hasil['detail'])) {
            foreach ($hasil['detail'] as $item) {
                $this->line("   - Terkirim untuk: {$item}");
            }
        }
    } else {
        $this->error('❌ ' . $hasil['message']);
    }
})->purpose('Kirim notifikasi pengingat piutang ke Telegram Pemilik');

// Sesuai Proposal Capstone (Activity Diagram Gambar 4 & Software Table 11)
// Menjalankan pengecekan otomatis setiap hari pada jam 08:00 pagi
Schedule::command('piutang:kirim-notifikasi')->dailyAt('08:00');

