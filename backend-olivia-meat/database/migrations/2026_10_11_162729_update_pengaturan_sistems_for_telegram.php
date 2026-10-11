<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pengaturan_sistems', function (Blueprint $table) {
            $table->string('h_minus_notifikasi')->nullable()->change();
            
            $table->string('telegram_bot_token')->nullable()->after('is_notif_aktif');
            $table->string('telegram_chat_id')->nullable()->after('telegram_bot_token');
        });
    }

    public function down()
    {
        Schema::table('pengaturan_sistems', function (Blueprint $table) {
            $table->integer('h_minus_notifikasi')->nullable()->change();
            $table->dropColumn(['telegram_bot_token', 'telegram_chat_id']);
        });
    }
};