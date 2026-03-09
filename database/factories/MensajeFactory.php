<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Mensaje;
use App\Models\Chat;
use App\Models\User;

class MensajeFactory extends Factory
{
    protected $model = Mensaje::class;

    public function definition(): array
    {
        return [
            'chat_id' => Chat::factory(),
            'emisor_id' => User::factory(),
            'receptor_id' => User::factory(),
            'contenido' => $this->faker->sentence(),
            'editado' => false,

            // Si tu tabla SÍ tiene fechaHora:
            // 'fechaHora' => now(),
        ];
    }
}
