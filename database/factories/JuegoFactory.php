<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Juego;

class JuegoFactory extends Factory
{
    protected $model = Juego::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->words(2, true),
            'categoria' => $this->faker->randomElement(['Mesa', 'Cartas', 'Slots']),
            'estado' => $this->faker->randomElement(['abierta', 'en_juego', 'cerrada']),
        ];
    }
}
