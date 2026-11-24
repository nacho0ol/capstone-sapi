<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jagals', function (Blueprint $table) {
            $table->id('id_jagal');
            $table->string('nama_jagal', 30);
            $table->text('keterangan')->nullable();
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
        });

        DB::statement("ALTER TABLE jagals ADD CONSTRAINT chk_jagal_nama CHECK (LENGTH(nama_jagal) >= 3 AND LENGTH(nama_jagal) <= 30)");
    }

    public function down(): void
    {
        Schema::dropIfExists('jagals');
    }
};