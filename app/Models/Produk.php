<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $table = 'produk'; // Sesuaikan dengan nama tabel yang benar
    protected $primaryKey = 'id_produk'; // Jika nama primary key berbeda, sesuaikan di sini
    // Sisanya sesuaikan dengan kolom-kolom yang ada dalam tabel
    public $incrementing = true;
    protected $fillable = [
        'nama',
        'jenis',
        'deskripsi',
        'suku_bunga',
        'minimum_saldo',
        'biaya_admin'

    ];
    public function rekening()
    {
        return $this->hasMany(Rekening::class, 'id_produk');
    }
}
