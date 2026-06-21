<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'id',
        'name',
        'description',
        'category',
        'price',
        'most_ordered',
        'is_bundle',
        'img_url',
        'options',
        'inventory_menu_id',
        'inventory_menu_code',
        'is_active',
        'inventory_synced_at',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'most_ordered' => 'boolean',
            'is_bundle' => 'boolean',
            'is_active' => 'boolean',
            'options' => 'array',
        ];
    }
}
