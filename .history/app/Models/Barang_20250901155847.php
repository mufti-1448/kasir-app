<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
    use SoftDeletes;
    protected $fillable = ['nama', 'kategori', 'harga_beli', 'harga_jual', 'stok', 'satuan'];
    public function transaksiDetails()
    {
        return $this->hasMany(TransaksiDetail::class);
    }
}
