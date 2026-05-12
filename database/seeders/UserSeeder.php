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
            // ── ADMINISTRACIÓN Y OPERADORES ──────────────────────────────────
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

            // ── JUGADORES (Coincidentes con RankingSeeder) ───────────────────
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
                'name' => 'Carlos Mendoza',
                'email' => 'carlos@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 1850,
                'nivel_vip' => 2,
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
            [
                'name' => 'Marcos Vidal',
                'email' => 'marcos@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 380,
                'nivel_vip' => 0,
                'role' => 'player',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Elena Gómez',
                'email' => 'elena@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 250,
                'nivel_vip' => 0,
                'role' => 'player',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Pablo Torres',
                'email' => 'pablo@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 190,
                'nivel_vip' => 0,
                'role' => 'player',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Nuria Castro',
                'email' => 'nuria@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 120,
                'nivel_vip' => 0,
                'role' => 'player',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Adrián Molina',
                'email' => 'adrian@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 80,
                'nivel_vip' => 0,
                'role' => 'player',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Marta Sánchez',
                'email' => 'marta@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 45,
                'nivel_vip' => 0,
                'role' => 'player',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Jorge Ibáñez',
                'email' => 'jorge@bookie20.test',
                'email_verified_at' => $now,
                'password' => Hash::make('password123'),
                'puntos_fidelidad' => 0,
                'nivel_vip' => 0,
                'role' => 'player',
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            
            // ── OTROS JUGADORES (No están en el ranking actual) ───────────────
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
        ]);
        
        $this->command->info('Usuarios insertados: Administradores y todos los jugadores del ranking en ' . now()->toTimeString());
    }
}
