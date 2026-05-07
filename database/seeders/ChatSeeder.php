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
            [
                'nombre' => 'Chat con Lucía Herrera',
                'activo' => true,
                'user_id' => $users['admin@bookie20.test'],
                'user_one_id' => $users['admin@bookie20.test'],
                'user_two_id' => $users['lucia@bookie20.test'],
                'last_message_at' => $now->copy()->subMinutes(35),
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subMinutes(35),
            ],
            [
                'nombre' => 'Chat con Carlos Mendoza',
                'activo' => true,
                'user_id' => $users['admin@bookie20.test'],
                'user_one_id' => $users['admin@bookie20.test'],
                'user_two_id' => $users['carlos@bookie20.test'],
                'last_message_at' => $now->copy()->subHours(2),
                'created_at' => $now->copy()->subDays(3),
                'updated_at' => $now->copy()->subHours(2),
            ],
            [
                'nombre' => 'Chat con Operador Central',
                'activo' => true,
                'user_id' => $users['daniel@bookie20.test'],
                'user_one_id' => $users['daniel@bookie20.test'],
                'user_two_id' => $users['operador@bookie20.test'],
                'last_message_at' => $now->copy()->subHours(8),
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subHours(8),
            ],
            [
                'nombre' => 'Chat con Sofía Navarro',
                'activo' => true,
                'user_id' => $users['valentina@bookie20.test'],
                'user_one_id' => $users['valentina@bookie20.test'],
                'user_two_id' => $users['sofia@bookie20.test'],
                'last_message_at' => $now->copy()->subDay(),
                'created_at' => $now->copy()->subDays(6),
                'updated_at' => $now->copy()->subDay(),
            ],
        ]);
    }
}
