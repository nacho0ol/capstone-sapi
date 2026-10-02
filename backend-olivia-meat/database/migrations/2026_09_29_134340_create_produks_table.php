<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id('id_produk');
            $table->string('nama_produk', 50);
            $table->enum('kategori', ['Daging', 'Tulang', 'Buntut', 'Jeroan', 'Kaki']);
            $table->integer('harga_awal');
            $table->integer('harga_jual');
            $table->string('nama_jagal', 50);
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });

        // Constraint dari SQL
        DB::statement("ALTER TABLE produks ADD CONSTRAINT chk_nama_produk CHECK (LENGTH(nama_produk) >= 3 AND nama_produk REGEXP '^[a-zA-Z0-9 ]+$')");
        DB::statement("ALTER TABLE produks ADD CONSTRAINT chk_harga_awal CHECK (harga_awal > 1000 AND harga_awal <= 1000000)");
        DB::statement("ALTER TABLE produks ADD CONSTRAINT chk_harga_jual CHECK (harga_jual >= harga_awal AND harga_jual <= 1000000)");
        DB::statement("ALTER TABLE produks ADD CONSTRAINT chk_nama_jagal CHECK (LENGTH(nama_jagal) >= 3 AND nama_jagal REGEXP '^[a-zA-Z ]+$')");
    }

    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};