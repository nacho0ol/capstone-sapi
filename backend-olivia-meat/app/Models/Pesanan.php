<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanans';
    protected $primaryKey = 'id_pesanan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_pesanan',
        'id_pelanggan',
        'id_user',
        'tgl_order',
        'metode_bayar',
        'status_bayar',
        'tgl_antar',
        'status_pemesanan',
        'catatan',
        'is_deleted'
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function detail()
    {
        return $this->hasMany(PesananDetail::class, 'id_pesanan', 'id_pesanan');
    }

    public function piutang()
    {
        return $this->hasOne(Piutang::class, 'id_pesanan', 'id_pesanan');
    }
}