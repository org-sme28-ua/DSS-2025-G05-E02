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
        $juegos = DB::table('juegos')->pluck('id', 'nombre');

        $parametros = [
            'Ruleta' => [
                'multiplicacion_por_juego' => 2.00,
                'bonus_por_racha' => 10.00,
            ],
            'Slot Machine' => [
                'multiplicacion_por_juego' => 3.00,
                'bonus_por_racha' => 25.00,
            ],
            'Bingo' => [
                'multiplicacion_por_juego' => 2.50,
                'bonus_por_racha' => 15.00,
            ],
            'Predicción' => [
                'multiplicacion_por_juego' => 2.00,
                'bonus_por_racha' => 5.00,
            ],
        ];

        $rows = [];

        foreach ($parametros as $nombreJuego => $valores) {
            if (!isset($juegos[$nombreJuego])) {
                continue;
            }

            $rows[] = [
                'juego_id' => $juegos[$nombreJuego],
                'multiplicacion_por_juego' => $valores['multiplicacion_por_juego'],
                'bonus_por_racha' => $valores['bonus_por_racha'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows)) {
            DB::table('parametros_ganancia')->insert($rows);
        }
    }
}
