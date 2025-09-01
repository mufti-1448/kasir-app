// app/Models/Barang.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Barang extends Model
{
use HasFactory, SoftDeletes;

protected $table = 'barangs'; // <- Pastikan ini 'barangs'

    protected $fillable=[ 'nama' , 'kategori' , 'harga_beli' , 'harga_jual' , 'stok' , 'satuan'
    ];
    }
