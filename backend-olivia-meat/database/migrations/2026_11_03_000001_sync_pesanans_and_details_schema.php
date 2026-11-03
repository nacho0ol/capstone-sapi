<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pesanans')) {
            if (!Schema::hasColumn('pesanans', 'catatan')) {
                Schema::table('pesanans', function (Blueprint $table) {
                    $table->text('catatan')->nullable()->after('status_pemesanan');
                });
            }

            DB::statement("ALTER TABLE pesanans MODIFY COLUMN status_pemesanan ENUM('Diterima', 'Diproses', 'Dikirim', 'Ditolak', 'Selesai', 'Dibatalkan') NOT NULL");
        }

        if (Schema::hasTable('pesanan_details')) {
            DB::statement("ALTER TABLE pesanan_details MODIFY COLUMN qty DECIMAL(10, 2) NOT NULL");
        }
    }

    public function down(): void
    {
        // Tidak menghapus kolom catatan agar data tetap aman
    }
};

