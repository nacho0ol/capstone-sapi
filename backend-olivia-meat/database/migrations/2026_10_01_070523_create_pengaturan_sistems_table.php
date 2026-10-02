<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_sistems', function (Blueprint $table) {
            $table->id('id_pengaturan');
            $table->integer('h_minus_notifikasi')->default(1);
            $table->text('template_tagihan');
            $table->boolean('is_notif_aktif')->default(1);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE pengaturan_sistems ADD CONSTRAINT chk_h_minus CHECK (h_minus_notifikasi >= 0 AND h_minus_notifikasi <= 30)");
        DB::statement("ALTER TABLE pengaturan_sistems ADD CONSTRAINT chk_template CHECK (LENGTH(template_tagihan) > 10)");
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_sistems');
    }
};