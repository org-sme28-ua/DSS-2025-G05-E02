<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // <--- IMPORTANTE: Añade esta línea

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Desactivar revisión de llaves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 2. Limpiar la tabla
        User::truncate();

        // 3. Crear los usuarios
        User::create([
            'name' => 'Sofía Jugadora',
            'email' => 'sofia@casino.com',
            'password' => Hash::make('password123'),
            'puntos_fidelidad' => 150,
            'nivel_vip' => 2
        ]);

        User::create([
            'name' => 'Carlos Operador',
            'email' => 'carlos@casino.com',
            'password' => Hash::make('password123'),
            'puntos_fidelidad' => 0,
            'nivel_vip' => 0
        ]);

        // 4. Reactivar revisión de llaves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
