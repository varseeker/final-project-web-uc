<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Nasabah;

class NasabahSeeder extends Seeder
{
    public function run()
    {
        // Membuat 15 data dummy nasabah
        Nasabah::factory()->count(15)->create();
    }
}
