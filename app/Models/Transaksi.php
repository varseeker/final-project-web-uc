<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi'; // Sesuaikan dengan nama tabel yang benar
    protected $primaryKey = 'id_transaksi'; // Jika nama primary key berbeda, sesuaikan di sini
    // Sisanya sesuaikan dengan kolom-kolom yang ada dalam tabel
    public $incrementing = true;
    protected $fillable = [
        'nomor_rekening_asal',
        'nomor_rekening_tujuan',
        'bank_tujuan',
        'jenis_transaksi',  
        'jumlah_transaksi'
    ];
    
    public function rekening()
    {
        return $this->belongsTo(Rekening::class, 'nomor_rekening_asal', 'nomor_rekening');
    }
}
