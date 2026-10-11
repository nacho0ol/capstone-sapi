<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanSistem extends Model
{
    protected $table = 'pengaturan_sistems';
    protected $primaryKey = 'id_pengaturan'; 
    
    protected $fillable = [
        'h_minus_notifikasi',
        'template_tagihan',
        'is_notif_aktif',
        'telegram_bot_token',
        'telegram_chat_id'
    ];

    protected $casts = [
        'h_minus_notifikasi' => 'array', 
        'is_notif_aktif' => 'boolean',
    ];
}