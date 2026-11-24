<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jagal extends Model
{
    protected $table = 'jagals';
    protected $primaryKey = 'id_jagal';

    protected $fillable = [
        'nama_jagal',
        'keterangan',
        'is_deleted',
    ];

    public function produks()
    {
        return $this->hasMany(Produk::class, 'id_jagal', 'id_jagal');
    }

    public function pemasokanDagings()
    {
        return $this->hasMany(PemasokanDaging::class, 'id_jagal', 'id_jagal');
    }
}