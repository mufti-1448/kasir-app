<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = ['kode', 'user_id', 'total', 'diskon', 'metode_pembayaran'];
    public function details()
    {
        return $this->hasMany(TransaksiDetail::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
