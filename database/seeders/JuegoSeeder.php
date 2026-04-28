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
            ['id' => 1, 'nombre' => 'Ruleta Europea', 'categoria' => 'Casino', 'estado' => 'abierta', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'nombre' => 'Blackjack VIP', 'categoria' => 'Cartas', 'estado' => 'en_juego', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'nombre' => 'Poker Texas Hold’em', 'categoria' => 'Cartas', 'estado' => 'abierta', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'nombre' => 'Tragamonedas Pharaoh Gold', 'categoria' => 'Slots', 'estado' => 'cerrada', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'nombre' => 'Aviator Crash', 'categoria' => 'Instant', 'estado' => 'abierta', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'nombre' => 'Bacará Live', 'categoria' => 'Live Casino', 'estado' => 'en_juego', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}