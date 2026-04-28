<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MensajeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $users = DB::table('users')->pluck('id', 'email');
        $chats = DB::table('chats')->pluck('id', 'nombre');

        DB::table('mensajes')->insert([
            [
                'chat_id' => $chats['Soporte VIP Lucía'],
                'emisor_id' => $users['lucia@bookie20.test'],
                'receptor_id' => $users['operador@bookie20.test'],
                'contenido' => 'Hola, necesito revisar un retiro pendiente.',
                'editado' => false,
                'created_at' => $now->copy()->subDays(7)->subHours(2),
                'updated_at' => $now->copy()->subDays(7)->subHours(2),
            ],
            [
                'chat_id' => $chats['Soporte VIP Lucía'],
                'emisor_id' => $users['operador@bookie20.test'],
                'receptor_id' => $users['lucia@bookie20.test'],
                'contenido' => 'Claro, ya lo estamos validando desde soporte.',
                'editado' => false,
                'created_at' => $now->copy()->subDays(7)->subHours(1),
                'updated_at' => $now->copy()->subDays(7)->subHours(1),
            ],
            [
                'chat_id' => $chats['Mesa Privada Sofía'],
                'emisor_id' => $users['sofia@bookie20.test'],
                'receptor_id' => $users['operador@bookie20.test'],
                'contenido' => '¿Podéis habilitar otra mesa high roller?',
                'editado' => false,
                'created_at' => $now->copy()->subDays(5)->subHours(4),
                'updated_at' => $now->copy()->subDays(5)->subHours(4),
            ],
            [
                'chat_id' => $chats['Incidencia de cobro Mateo'],
                'emisor_id' => $users['mateo@bookie20.test'],
                'receptor_id' => $users['operador@bookie20.test'],
                'contenido' => 'Se duplicó un cobro en mi último depósito.',
                'editado' => true,
                'created_at' => $now->copy()->subDays(3)->subHours(6),
                'updated_at' => $now->copy()->subDays(3)->subHours(5),
            ],
            [
                'chat_id' => $chats['Atención al jugador Carlos'],
                'emisor_id' => $users['carlos@bookie20.test'],
                'receptor_id' => $users['operador@bookie20.test'],
                'contenido' => 'Quiero saber cómo subir de nivel VIP.',
                'editado' => false,
                'created_at' => $now->copy()->subDay()->subHours(3),
                'updated_at' => $now->copy()->subDay()->subHours(3),
            ],
        ]);
    }
}