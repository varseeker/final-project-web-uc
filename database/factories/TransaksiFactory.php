<?php

use App\Models\Rekening;
use App\Models\Transaksi;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransaksiFactory extends Factory {
    protected $model = Transaksi::class;

    public function definition() {
        return [
            'nomor_rekening' => Rekening::factory(),
            'jenis_transaksi' => $this->faker->randomElement(['debit', 'kredit']),
            'tanggal_transaksi' => $this->faker->date(),
            'jumlah_transaksi' => $this->faker->randomFloat(2, 1000, 500000),
        ];
    }
}
