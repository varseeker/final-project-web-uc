<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rekening;

class RekeningSeeder extends Seeder
{
    public function run()
    {
        // Membuat 15 data dummy Rekening
        Rekening::factory()->count(15)->create();
    }
}
