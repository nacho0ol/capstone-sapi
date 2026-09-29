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
        Schema::create('produks', function (Blueprint $table) {
            $table->id('id_produk');
            $table->string('nama_produk', 50);
            $table->enum('kategori', ['Daging', 'tulang', 'buntut', 'jeroan', 'kaki']);
            $table->integer('harga_awal');
            $table->integer('harga_jual');
            $table->string('nama_jagal', 50);
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
