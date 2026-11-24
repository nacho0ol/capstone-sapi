<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom id_jagal di produks
        Schema::table('produks', function (Blueprint $table) {
            $table->unsignedBigInteger('id_jagal')->nullable()->after('harga_jual');
            $table->foreign('id_jagal')->references('id_jagal')->on('jagals');
        });

        // Migrasi otomatis nama_jagal lama ke tabel jagals (jika sudah ada data)
        $existingJagals = DB::table('produks')->whereNotNull('nama_jagal')->distinct()->pluck('nama_jagal');
        foreach ($existingJagals as $nama) {
            $trimmed = substr(trim($nama), 0, 30);
            if ($trimmed !== '') {
                $idJagal = DB::table('jagals')->insertGetId([
                    'nama_jagal' => $trimmed,
                    'keterangan' => 'Migrasi otomatis dari data produk',
                    'is_deleted' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::table('produks')->where('nama_jagal', $nama)->update(['id_jagal' => $idJagal]);
            }
        }

        // Jadikan nama_jagal lama nullable agar tidak wajib diisi saat pakai id_jagal
        DB::statement("ALTER TABLE produks DROP CHECK chk_nama_jagal");
        Schema::table('produks', function (Blueprint $table) {
            $table->string('nama_jagal', 50)->nullable()->change();
        });

        // 2. Update kategori pengeluaran lama sebelum ubah ENUM
        DB::table('pengeluaran_harians')->where('kategori_pengeluaran', 'Transportasi')->update(['kategori_pengeluaran' => 'Makan']);
        DB::table('pengeluaran_harians')->whereIn('kategori_pengeluaran', ['Pembelian Daging', 'Operasional', 'Lain-lain'])->update(['kategori_pengeluaran' => 'Makan']);

        DB::statement("ALTER TABLE pengeluaran_harians MODIFY COLUMN kategori_pengeluaran ENUM('Listrik', 'Makan', 'Plastik', 'Bensin', 'Gaji') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pengeluaran_harians MODIFY COLUMN kategori_pengeluaran ENUM('Makan', 'Pembelian Daging', 'Operasional', 'Transportasi', 'Lain-lain') NOT NULL");

        Schema::table('produks', function (Blueprint $table) {
            $table->dropForeign(['id_jagal']);
            $table->dropColumn('id_jagal');
        });
    }
};