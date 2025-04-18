<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run()
    {
        // Membuat 15 data dummy Produk
        Produk::factory()->count(15)->create();
    }
}
