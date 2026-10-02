<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Wajib dipanggil

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id('id_pelanggan');
            $table->string('nama_pelanggan', 100);
            $table->string('no_telp', 15);
            $table->text('alamat');
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });

        // Constraint dari SQL
        DB::statement("ALTER TABLE pelanggans ADD CONSTRAINT chk_nama_pelanggan CHECK (LENGTH(nama_pelanggan) >= 3 AND nama_pelanggan REGEXP '^[a-zA-Z][a-zA-Z ]*$')");
        DB::statement("ALTER TABLE pelanggans ADD CONSTRAINT chk_no_telp CHECK (no_telp REGEXP '^08[0-9]{8,13}$')");
        DB::statement("ALTER TABLE pelanggans ADD CONSTRAINT chk_alamat CHECK (LENGTH(alamat) >= 5)");
    }

    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};