<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\Mensaje;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'read_at' => null,
        ];
    }
}
