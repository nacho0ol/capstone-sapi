<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PemasokanDaging extends Model
{
    protected $table = 'pemasokan_dagings';
    protected $primaryKey = 'id_pemasokan';

    protected $fillable = [
        'id_produk',
        'id_jagal',
        'jumlah',
        'total_harga',
        'tgl_pemasokan',
        'catatan',
        'is_deleted',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function jagal()
    {
        return $this->belongsTo(Jagal::class, 'id_jagal', 'id_jagal');
    }
}