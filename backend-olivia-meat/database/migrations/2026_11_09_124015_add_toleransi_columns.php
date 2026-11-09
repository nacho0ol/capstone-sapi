<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah setting global (default 7 hari)
        Schema::table('pengaturan_sistems', function (Blueprint $table) {
            $table->integer('batas_toleransi_global')->default(7)->after('template_tagihan');
        });

        // Tambah setting khusus pelanggan (nullable, kalau kosong berarti ikut global)
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->integer('batas_toleransi_khusus')->nullable()->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('pengaturan_sistems', function (Blueprint $table) {
            $table->dropColumn('batas_toleransi_global');
        });

        Schema::table('pelanggans', function (Blueprint $table) {
            $table->dropColumn('batas_toleransi_khusus');
        });
    }
};