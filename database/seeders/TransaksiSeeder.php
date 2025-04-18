<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;

class TransaksiSeeder extends Seeder
{
    public function run()
    {
        // Membuat 15 data dummy Transaksi
        Transaksi::factory()->count(15)->create();
    }
}
