<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Apuesta;
use App\Models\User;
use App\Models\Juego;

class ApuestaFactory extends Factory
{
    protected $model = Apuesta::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'juego_id' => Juego::factory(),
            'monto' => $this->faker->randomFloat(2, 1, 500),
            'cuota' => $this->faker->randomFloat(2, 1.1, 10),
            'estado' => $this->faker->randomElement(['pendiente', 'ganada', 'perdida']),
            'fecha' => now(),
        ];
    }
}
