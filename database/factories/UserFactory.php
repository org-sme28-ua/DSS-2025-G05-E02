<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),

            // Si tu tabla users tiene estos campos (en tu User.php aparecen):
            'puntos_fidelidad' => $this->faker->numberBetween(0, 500),
            'nivel_vip' => $this->faker->numberBetween(0, 5),
        ];
    }
}
