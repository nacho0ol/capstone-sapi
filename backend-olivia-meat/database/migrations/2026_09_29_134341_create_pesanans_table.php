<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->string('id_pesanan', 20)->primary();
            $table->foreignId('id_pelanggan')->references('id_pelanggan')->on('pelanggans');
            
            $table->foreignId('id_user')->references('id_user')->on('users'); 
            
            $table->date('tgl_order');
            $table->enum('metode_bayar', ['Tunai', 'Transfer', 'Tempo']);
            $table->enum('status_bayar', ['Unpaid', 'DP', 'Lunas']);
            $table->date('tgl_antar');
            $table->enum('status_pemesanan', ['Diterima', 'Diproses', 'Dikirim', 'Ditolak', 'Selesai', 'Dibatalkan']);
            $table->text('catatan')->nullable();
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE pesanans ADD CONSTRAINT chk_id_pesanan CHECK (id_pesanan LIKE 'ORD-%')");
        DB::statement("ALTER TABLE pesanans ADD CONSTRAINT chk_tgl_order CHECK (YEAR(tgl_order) >= 2015)");
        DB::statement("ALTER TABLE pesanans ADD CONSTRAINT chk_tgl_antar CHECK (YEAR(tgl_antar) >= 2015)");
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};