<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParametroGananciaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('parametros_ganancia')->insert([
            [
                'multiplicacion_por_juego' => 1.50,
                'bonus_por_racha' => 10.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'multiplicacion_por_juego' => 2.00,
                'bonus_por_racha' => 25.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'multiplicacion_por_juego' => 3.25,
                'bonus_por_racha' => 50.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}