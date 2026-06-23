<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    use HasFactory;

    protected $table = 'pengirimans';

    protected $fillable = [
        'penjualan_id',
        'no_pengiriman',
        'no_invoice',
        'tanggal_kirim',
        'plat_kendaraan',
        'catatan',
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function details()
    {
        return $this->hasMany(PengirimanDetail::class, 'pengiriman_id');
    }
}
