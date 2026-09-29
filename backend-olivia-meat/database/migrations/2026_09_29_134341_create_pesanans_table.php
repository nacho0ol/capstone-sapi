<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
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
            $table->enum('status_pemesanan', ['diterima', 'diproses', 'dikirim', 'ditolak', 'selesai']);
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
