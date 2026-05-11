<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    protected $model = Chat::class;

    public function definition(): array
    {
        return [
            'nombre' => 'Chat privado ' . $this->faker->word(),
            'activo' => true,
            'user_id' => User::factory(),
            'user_one_id' => null,
            'user_two_id' => null,
            'last_message_at' => null,
        ];
    }
}
