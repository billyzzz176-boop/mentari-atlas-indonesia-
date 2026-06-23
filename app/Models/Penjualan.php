<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;
use App\Models\User;
use App\Models\PenjualanDetail;

class Penjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_so',
        'tanggal_order',
        'customer_id',
        'user_id',
        'total_semua',
        'status_approval',
        'catatan',
        'approved_by',
        'approved_at',
        'sales_created_at',
        'sales_created_by',
        'skor_spk'
    ];

    protected $casts = [
        'sales_created_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function getSkorPersentaseAttribute()
    {
        if ($this->status_approval === 'disetujui') {
            return 100;
        }
        
        if ($this->status_approval === 'ditolak') {
            return 0;
        }

        return 95;
    }

    public function getPeluangAttribute()
    {
        return $this->getSkorPersentaseAttribute(); 
    }

    public function customer() 
    { 
        return $this->belongsTo(Customer::class)->withTrashed(); 
    }

    public function user() 
    { 
        return $this->belongsTo(User::class, 'user_id')->withTrashed(); 
    }

    public function approver() 
    { 
        return $this->belongsTo(User::class, 'approved_by')->withTrashed(); 
    }

    public function details() 
    { 
        return $this->hasMany(PenjualanDetail::class); 
    }

    public function pengirimans()
    {
        return $this->hasMany(Pengiriman::class, 'penjualan_id');
    }
}