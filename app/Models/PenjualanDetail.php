<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'penjualan_id',
        'barang_id',
        'hpp', // <--- PONDASI LABA: HPP DIKUNCI DI SINI
        'harga_awal',
        'harga_satuan',
        'diskon',
        'jumlah',
        'subtotal'
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class)->withTrashed();
    }
}