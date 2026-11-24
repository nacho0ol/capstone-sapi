<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasokan_dagings', function (Blueprint $table) {
            $table->id('id_pemasokan');
            $table->foreignId('id_produk')->references('id_produk')->on('produks');
            $table->foreignId('id_jagal')->references('id_jagal')->on('jagals');
            $table->decimal('jumlah', 10, 2);
            $table->integer('total_harga');
            $table->date('tgl_pemasokan');
            $table->text('catatan')->nullable();
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE pemasokan_dagings ADD CONSTRAINT chk_pemasokan_jumlah CHECK (jumlah > 0 AND jumlah <= 1000)");
        DB::statement("ALTER TABLE pemasokan_dagings ADD CONSTRAINT chk_pemasokan_total CHECK (total_harga >= 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasokan_dagings');
    }
};