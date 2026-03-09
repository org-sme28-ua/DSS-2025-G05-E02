<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Billetera;
use App\Models\User;

class BilleteraFactory extends Factory
{
    protected $model = Billetera::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'saldoDisponible' => $this->faker->randomFloat(2, 0, 5000),
            'moneda' => 'EUR',
        ];
    }
}
