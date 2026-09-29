<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produks';
    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'nama_produk',
        'kategori',
        'harga_awal',
        'harga_jual',
        'nama_jagal',
        'is_deleted'
    ];
}