<?php

namespace Database\Factories;

use App\Models\Nasabah;
use App\Models\Produk;
use App\Models\Rekening;
use Illuminate\Database\Eloquent\Factories\Factory;

class RekeningFactory extends Factory
{
    protected $model = Rekening::class;

    public function definition()
    {
        return [
            'id_nasabah' => Nasabah::factory(),
            'id_produk' => Produk::factory(),
            'nomor_rekening' => $this->faker->unique()->bankAccountNumber(),
            'saldo' => $this->faker->randomFloat(2, 1000, 1000000),
        ];
    }
}
