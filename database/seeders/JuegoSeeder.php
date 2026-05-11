<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JuegoSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('juegos')->insert([
            [
                'id' => 1,
                'nombre' => 'Ruleta',
                'categoria' => 'Casino',
                'estado' => 'abierta',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'nombre' => 'Slot Machine',
                'categoria' => 'Slots',
                'estado' => 'abierta',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'nombre' => 'Bingo',
                'categoria' => 'Casino',
                'estado' => 'abierta',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'nombre' => 'Predicción',
                'categoria' => 'Predicciones',
                'estado' => 'abierta',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
