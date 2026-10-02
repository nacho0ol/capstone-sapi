<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan_details', function (Blueprint $table) {
            $table->id('id_detail');
            $table->string('id_pesanan', 20);
            $table->foreign('id_pesanan')->references('id_pesanan')->on('pesanans');
            $table->foreignId('id_produk')->references('id_produk')->on('produks');
            
            // QTY di SQL aslinya (3,2), bukan (10,2)
            $table->decimal('qty', 3, 2); 
            $table->integer('harga_jual_saat_ini');
            
            // Dibuat otomatis Generated Always Store seperti MySQL Asli!
            $table->integer('subtotal')->storedAs('qty * harga_jual_saat_ini');
            
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE pesanan_details ADD CONSTRAINT chk_qty CHECK (qty > 0)");
        DB::statement("ALTER TABLE pesanan_details ADD CONSTRAINT chk_harga_jual_saat_ini CHECK (harga_jual_saat_ini > 1000 AND harga_jual_saat_ini <= 1000000)");
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_details');
    }
};