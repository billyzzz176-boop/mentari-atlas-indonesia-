<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengirimanDetail extends Model
{
    use HasFactory;

    protected $table = 'pengiriman_details';

    protected $fillable = [
        'pengiriman_id',
        'barang_id',
        'jumlah_kirim',
        'harga_satuan',
        'subtotal',
    ];

    public function pengiriman()
    {
        return $this->belongsTo(Pengiriman::class, 'pengiriman_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id')->withTrashed();
    }
}
