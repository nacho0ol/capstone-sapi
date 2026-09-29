<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesananDetail extends Model
{
    use HasFactory;

    protected $table = 'pesanan_details';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_pesanan',
        'id_produk',
        'qty',
        'harga_jual_saat_ini',
        'subtotal',
        'is_deleted'
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}