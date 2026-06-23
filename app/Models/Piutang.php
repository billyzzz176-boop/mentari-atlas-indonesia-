<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Penjualan; // <-- Pemanggilan eksplisit agar tidak error
use App\Models\User;

class Piutang extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_invoice',
        'penjualan_id',
        'total_tagihan',
        'total_dibayar',
        'status_bayar',
        'jatuh_tempo',
        'diinput_by'
    ];

    // Relasi balik ke data Penjualan SO
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    // Relasi ke tabel Users (Admin/Direktur)
    public function PJ_Bayar()
    {
        return $this->belongsTo(User::class, 'diinput_by')->withTrashed();
    }

    // Relasi untuk menarik semua data riwayat cicilan
    public function pembayarans()
    {
        return $this->hasMany(PembayaranPiutang::class, 'piutang_id')->orderBy('tanggal_bayar', 'desc');
    }
}