<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nasabah extends Model
{
    use HasFactory;
    protected $table = 'nasabah'; // Sesuaikan dengan nama tabel yang benar
    protected $primaryKey = 'id_nasabah'; // Jika nama primary key berbeda, sesuaikan di sini
    // Sisanya sesuaikan dengan kolom-kolom yang ada dalam tabel
    public $incrementing = true;
    protected $fillable = [
        'user_id',
        'noIdentitas',
        'nama',
        'alamat',
        'nomor_telepon',
        'email',
        'tanggal_lahir',
        'status_pekerjaan',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function rekening()
    {
        return $this->hasMany(Rekening::class, 'id_nasabah');
    }
}
