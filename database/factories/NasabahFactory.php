<?php

namespace Database\Factories;

use App\Models\Nasabah;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NasabahFactory extends Factory
{
    protected $model = Nasabah::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'nama' => $this->faker->name(),
            'alamat' => $this->faker->address(),
            'nomor_telepon' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'tanggal_lahir' => $this->faker->date(),
            'status_pekerjaan' => $this->faker->jobTitle(),
        ];
    }
}
