<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $users = DB::table('users')->pluck('id', 'email');

        DB::table('chats')->insert([
            ['nombre' => 'Soporte VIP Lucía', 'activo' => true, 'user_id' => $users['lucia@bookie20.test'], 'created_at' => $now->copy()->subDays(7), 'updated_at' => $now],
            ['nombre' => 'Mesa Privada Sofía', 'activo' => true, 'user_id' => $users['sofia@bookie20.test'], 'created_at' => $now->copy()->subDays(5), 'updated_at' => $now],
            ['nombre' => 'Incidencia de cobro Mateo', 'activo' => false, 'user_id' => $users['mateo@bookie20.test'], 'created_at' => $now->copy()->subDays(3), 'updated_at' => $now],
            ['nombre' => 'Atención al jugador Carlos', 'activo' => true, 'user_id' => $users['carlos@bookie20.test'], 'created_at' => $now->copy()->subDay(), 'updated_at' => $now],
        ]);
    }
}