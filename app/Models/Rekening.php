<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    use HasFactory;
    protected $table = 'rekening'; // Sesuaikan dengan nama tabel yang benar
    protected $primaryKey = 'nomor_rekening'; // Jika nama primary key berbeda, sesuaikan di sini
    // Sisanya sesuaikan dengan kolom-kolom yang ada dalam tabel
    public $incrementing = true;
    protected $fillable = [
        'id_nasabah',
        'id_produk',
        'nomor_rekening',
        'saldo',
    ];
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'nomor_rekening_asal');
    }
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
    public function nasabah()
    {
        return $this->belongsTo(Nasabah::class, 'id_nasabah');
    }
    
}
