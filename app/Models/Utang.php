<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utang extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_utang_jurnal',
        'pembelian_id',
        'total_utang',
        'total_dibayar',
        'status_bayar',
        'tanggal_jatuh_tempo',
        'diinput_by' // <--- Sudah ditambahkan ke fillable agar bisa di-input Laravel
    ];

    // Relasi balik ke data Pembelian PO untuk mengambil info Supplier & Item Barang
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }

    // Relasi ke tabel Users untuk melihat siapa Admin/Direktur yang menginput pelunasan utang
    public function PJ_Bayar()
    {
        return $this->belongsTo(\App\Models\User::class, 'diinput_by')->withTrashed();
    }

    // Relasi untuk menarik semua data riwayat cicilan/pembayaran utang
    public function pembayarans()
    {
        return $this->hasMany(PembayaranUtang::class, 'utang_id')->orderBy('tanggal_bayar', 'desc');
    }
}