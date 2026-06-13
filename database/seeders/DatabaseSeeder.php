<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Data dasar (admin, menu) sudah di-insert lewat migration.
     * Seeder ini menambah user dummy dari UserFactory.
     */
    public function run(): void
    {
        User::factory(2)->crew()->create();
        User::factory(2)->cashier()->create();

        User::factory()->cashier()->create([
            'name' => 'Test Cashier',
            'email' => 'cashier@test.com',
            'phone' => '081111111111',
            'address' => 'Jl. Test Cashier 1',
        ]);
    }
}
