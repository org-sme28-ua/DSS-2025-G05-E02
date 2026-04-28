<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('users')->insert([
            [
                'name' => 'Administrador Principal',
                'email' => 'admin@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 5000,
                'nivel_vip' => 3,
                'role' => 'admin',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Operador Central',
                'email' => 'operador@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 1200,
                'nivel_vip' => 1,
                'role' => 'operator',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Carlos Mendoza',
                'email' => 'carlos@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 850,
                'nivel_vip' => 1,
                'role' => 'player',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Lucía Herrera',
                'email' => 'lucia@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 2100,
                'nivel_vip' => 2,
                'role' => 'player',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Mateo Ruiz',
                'email' => 'mateo@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 150,
                'nivel_vip' => 0,
                'role' => 'player',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sofía Navarro',
                'email' => 'sofia@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 3100,
                'nivel_vip' => 3,
                'role' => 'player',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Daniel Ortega',
                'email' => 'daniel@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 980,
                'nivel_vip' => 1,
                'role' => 'player',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Valentina Cruz',
                'email' => 'valentina@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 420,
                'nivel_vip' => 0,
                'role' => 'player',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}