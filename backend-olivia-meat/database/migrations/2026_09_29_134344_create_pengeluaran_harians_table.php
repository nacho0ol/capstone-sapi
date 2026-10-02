<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; 

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengeluaran_harians', function (Blueprint $table) {
            $table->id('id_pengeluaran');
            $table->date('tgl_pengeluaran');
            $table->enum('kategori_pengeluaran', ['Makan', 'Pembelian Daging', 'Operasional', 'Transportasi', 'Lain-lain']);
            $table->foreignId('id_user')->references('id_user')->on('users');
            $table->integer('nominal');
            $table->text('keterangan');
            $table->boolean('is_deleted')->default(0); 
            $table->timestamps();
        });

        DB::statement("ALTER TABLE pengeluaran_harians ADD CONSTRAINT chk_nominal CHECK (nominal > 1000 AND nominal <= 100000000)");
        DB::statement("ALTER TABLE pengeluaran_harians ADD CONSTRAINT chk_keterangan CHECK (LENGTH(keterangan) >= 5)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengeluaran_harians');
    }
};