<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    /**
     * Menentukan nama tabel yang terhubung dengan model ini.
     *
     * @var string
     */
    protected $table = 'order';


    protected $fillable = [
        'id',
        'total',
        'amountPaid',
        'amountChange',
        'customer',
        'status',
        'payment-status',
        'user_id',
        'payReference',
    ];
}