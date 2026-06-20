<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'phone',
        'loyalty_points',
    ];

    protected function casts(): array
    {
        return [
            'loyalty_points' => 'integer',
        ];
    }
}
