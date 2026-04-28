<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApuestaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $users = DB::table('users')->pluck('id', 'email');
        $juegos = DB::table('juegos')->pluck('id', 'nombre');

        DB::table('apuestas')->insert([
            ['user_id' => $users['carlos@bookie20.test'], 'juego_id' => $juegos['Ruleta Europea'], 'monto' => 25.00, 'cuota' => 1.80, 'estado' => 'pendiente', 'fecha' => $now->copy()->subHours(12), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['lucia@bookie20.test'], 'juego_id' => $juegos['Blackjack VIP'], 'monto' => 150.00, 'cuota' => 2.35, 'estado' => 'ganada', 'fecha' => $now->copy()->subDay(), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['mateo@bookie20.test'], 'juego_id' => $juegos['Tragamonedas Pharaoh Gold'], 'monto' => 10.00, 'cuota' => 4.20, 'estado' => 'perdida', 'fecha' => $now->copy()->subDays(2), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['sofia@bookie20.test'], 'juego_id' => $juegos['Bacará Live'], 'monto' => 500.00, 'cuota' => 1.55, 'estado' => 'ganada', 'fecha' => $now->copy()->subHours(30), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['daniel@bookie20.test'], 'juego_id' => $juegos['Poker Texas Hold’em'], 'monto' => 45.50, 'cuota' => 2.10, 'estado' => 'pendiente', 'fecha' => $now->copy()->subHours(6), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['valentina@bookie20.test'], 'juego_id' => $juegos['Aviator Crash'], 'monto' => 30.00, 'cuota' => 3.40, 'estado' => 'perdida', 'fecha' => $now->copy()->subDays(4), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['lucia@bookie20.test'], 'juego_id' => $juegos['Ruleta Europea'], 'monto' => 80.00, 'cuota' => 1.95, 'estado' => 'ganada', 'fecha' => $now->copy()->subDays(5), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['carlos@bookie20.test'], 'juego_id' => $juegos['Aviator Crash'], 'monto' => 15.00, 'cuota' => 5.00, 'estado' => 'pendiente', 'fecha' => $now->copy()->subHours(2), 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}