<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RankingSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Obtener usuarios por email para referenciarlos de forma legible
        $users = DB::table('users')->pluck('id', 'email');

        // Limpiar tabla antes de insertar para evitar duplicados al re-seedear
        DB::table('rankings')->truncate();

        DB::table('rankings')->insert([

            // ── TOP 3 (aparecen en el podio de la vista) ──────────────────────
            [
                'user_id'      => $users['sofia@bookie20.test'],
                'posicion'     => 1,
                'puntos'       => 9850,
                'total_ganado' => 15240.00,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'user_id'      => $users['lucia@bookie20.test'],
                'posicion'     => 2,
                'puntos'       => 7310,
                'total_ganado' => 10210.25,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'user_id'      => $users['carlos@bookie20.test'],
                'posicion'     => 3,
                'puntos'       => 4125,
                'total_ganado' => 4980.00,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // ── ZONA MEDIA ─────────────────────────────────────────────────────
            [
                'user_id'      => $users['daniel@bookie20.test'],
                'posicion'     => 4,
                'puntos'       => 2980,
                'total_ganado' => 3120.40,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'user_id'      => $users['valentina@bookie20.test'],
                'posicion'     => 5,
                'puntos'       => 1875,
                'total_ganado' => 2015.90,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'user_id'      => $users['marcos@bookie20.test'],
                'posicion'     => 6,
                'puntos'       => 1420,
                'total_ganado' => 1580.00,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'user_id'      => $users['elena@bookie20.test'],
                'posicion'     => 7,
                'puntos'       => 1105,
                'total_ganado' => 1230.75,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'user_id'      => $users['pablo@bookie20.test'],
                'posicion'     => 8,
                'puntos'       => 870,
                'total_ganado' => 940.50,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // ── ZONA BAJA (jugadores con poca actividad) ───────────────────────
            [
                'user_id'      => $users['nuria@bookie20.test'],
                'posicion'     => 9,
                'puntos'       => 520,
                'total_ganado' => 430.00,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'user_id'      => $users['adrian@bookie20.test'],
                'posicion'     => 10,
                'puntos'       => 310,
                'total_ganado' => 275.20,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'user_id'      => $users['marta@bookie20.test'],
                'posicion'     => 11,
                'puntos'       => 185,
                'total_ganado' => 140.00,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // ── RECIEN LLEGADOS (0 puntos, para probar el caso vacio en vista) ─
            [
                'user_id'      => $users['jorge@bookie20.test'],
                'posicion'     => 12,
                'puntos'       => 0,
                'total_ganado' => 0.00,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ]);

        $this->command->info('Rankings insertados: 12 jugadores en ' . now()->toTimeString());
    }
}
