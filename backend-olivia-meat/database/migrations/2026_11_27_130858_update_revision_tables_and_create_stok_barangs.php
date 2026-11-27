<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom foto pada tabel produks
        Schema::table('produks', function (Blueprint $table) {
            if (!Schema::hasColumn('produks', 'foto')) {
                $table->string('foto', 255)->nullable()->after('nama_produk');
            }
        });

        // 2. Perluas ENUM metode_bayar pada tabel pesanans agar mendukung Pending / Cash Tunai / Cash Transfer
        // Tetap mempertahankan nilai lama ('Tunai', 'Transfer', 'Tempo') agar data existing tidak error
        DB::statement("ALTER TABLE pesanans MODIFY COLUMN metode_bayar ENUM('Pending', 'Cash Tunai', 'Cash Transfer', 'Tunai', 'Transfer', 'Tempo') NOT NULL DEFAULT 'Pending'");

        // 3. Buat tabel stok_barangs
        Schema::create('stok_barangs', function (Blueprint $table) {
            $table->id('id_stok');
            $table->string('jenis_daging', 100);
            $table->decimal('berat', 8, 2); // dalam kg, misal 25.50
            $table->date('tgl_stok');
            $table->date('tgl_keluar_stok')->nullable();
            $table->enum('status_stok', ['Tersedia', 'Sudah Keluar'])->default('Tersedia');
            $table->text('catatan')->nullable();
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_barangs');

        Schema::table('produks', function (Blueprint $table) {
            if (Schema::hasColumn('produks', 'foto')) {
                $table->dropColumn('foto');
            }
        });

        DB::statement("ALTER TABLE pesanans MODIFY COLUMN metode_bayar ENUM('Tunai', 'Transfer', 'Tempo') NOT NULL");
    }
};