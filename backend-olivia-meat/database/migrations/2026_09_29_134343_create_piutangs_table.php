<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piutangs', function (Blueprint $table) {
            // Di SQL, PK-nya adalah id_pesanan (bukan id_piutang baru)
            $table->string('id_pesanan', 20)->primary();
            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanans');
            $table->date('tgl_jatuh_tempo');
            $table->integer('total_tagihan');
            $table->integer('jumlah_terbayar')->default(0);
            $table->enum('status_piutang', ['Belum Lunas', 'Sebagian', 'Lunas']);
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE piutangs ADD CONSTRAINT chk_total_tagihan CHECK (total_tagihan > 0 AND total_tagihan <= 100000000)");
        DB::statement("ALTER TABLE piutangs ADD CONSTRAINT chk_jumlah_terbayar CHECK (jumlah_terbayar >= 0 AND jumlah_terbayar <= total_tagihan)");
    }

    public function down(): void
    {
        Schema::dropIfExists('piutangs');
    }
};