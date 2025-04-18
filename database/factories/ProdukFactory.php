<?php

namespace Database\Factories;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdukFactory extends Factory
{
    protected $model = Produk::class;

    public function definition()
    {
        return [
            'nama' => $this->faker->word() . ' Banksie',
            'jenis' => $this->faker->randomElement(['tabungan', 'deposito']),
            'deskripsi' => $this->faker->sentence(),
            'suku_bunga' => $this->faker->randomFloat(2, 0.5, 5),
            'minimum_saldo' => $this->faker->randomFloat(2, 5000, 500000),
            'biaya_admin' => $this->faker->randomFloat(2, 0, 10000),
        ];
    }
}
