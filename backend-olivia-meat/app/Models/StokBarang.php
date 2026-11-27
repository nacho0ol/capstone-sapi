<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokBarang extends Model
{
    use HasFactory;

    protected $table = 'stok_barangs';
    protected $primaryKey = 'id_stok';

    protected $fillable = [
        'jenis_daging',
        'berat',
        'tgl_stok',
        'tgl_keluar_stok',
        'status_stok',
        'catatan',
        'is_deleted',
    ];
}