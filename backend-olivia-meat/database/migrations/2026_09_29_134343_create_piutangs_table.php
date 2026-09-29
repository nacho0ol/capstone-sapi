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
        Schema::create('piutangs', function (Blueprint $table) {
            $table->id('id_piutang');
            $table->string('id_pesanan', 20)->unique();
            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanans');
            $table->date('tgl_jatuh_tempo');
            $table->integer('total_tagihan');
            $table->integer('jumlah_terbayar')->default(0);
            $table->enum('status_piutang', ['Belum Lunas', 'Sebagian', 'Lunas']);
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('piutangs');
    }
};
