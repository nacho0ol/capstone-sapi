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
        Schema::create('pesanan_details', function (Blueprint $table) {
            $table->id('id_detail');
            $table->string('id_pesanan', 20);
            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanans');
            $table->foreignId('id_produk')->references('id_produk')->on('produks');
            $table->decimal('qty', 10, 2);
            $table->integer('harga_jual_saat_ini');
            $table->integer('subtotal');
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan_details');
    }
};
