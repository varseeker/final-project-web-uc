<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    /**
     * Menentukan nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'payment';


    protected $fillable = [
        'id',
        'order_id',
        'totalPay',
        'method',
        'status',
        'reference'
    ];
}
