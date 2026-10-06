<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengeluaranHarian extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran_harians';
    protected $primaryKey = 'id_pengeluaran';

    protected $fillable = [
        'tgl_pengeluaran',
        'kategori_pengeluaran',
        'id_user',
        'nominal',
        'keterangan',
        'is_deleted'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}