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
                'chat_id' => $chats['Chat con Lucía Herrera'],
                'emisor_id' => $users['lucia@bookie20.test'],
                'receptor_id' => $users['admin@bookie20.test'],
                'contenido' => 'Hola, ¿puedes revisar mi predicción pendiente?',
                'editado' => false,
                'read_at' => null,
                'created_at' => $now->copy()->subHours(1),
                'updated_at' => $now->copy()->subHours(1),
            ],
            [
                'chat_id' => $chats['Chat con Lucía Herrera'],
                'emisor_id' => $users['admin@bookie20.test'],
                'receptor_id' => $users['lucia@bookie20.test'],
                'contenido' => 'Claro, la reviso ahora desde el panel de administrador.',
                'editado' => false,
                'read_at' => $now->copy()->subMinutes(45),
                'created_at' => $now->copy()->subMinutes(45),
                'updated_at' => $now->copy()->subMinutes(45),
            ],
            [
                'chat_id' => $chats['Chat con Lucía Herrera'],
                'emisor_id' => $users['lucia@bookie20.test'],
                'receptor_id' => $users['admin@bookie20.test'],
                'contenido' => 'Perfecto, gracias. Era sobre el resultado del torneo.',
                'editado' => false,
                'read_at' => null,
                'created_at' => $now->copy()->subMinutes(35),
                'updated_at' => $now->copy()->subMinutes(35),
            ],
            [
                'chat_id' => $chats['Chat con Carlos Mendoza'],
                'emisor_id' => $users['admin@bookie20.test'],
                'receptor_id' => $users['carlos@bookie20.test'],
                'contenido' => 'He visto que tienes varias apuestas de ruleta. ¿Todo correcto?',
                'editado' => false,
                'read_at' => $now->copy()->subHours(3),
                'created_at' => $now->copy()->subHours(5),
                'updated_at' => $now->copy()->subHours(5),
            ],
            [
                'chat_id' => $chats['Chat con Carlos Mendoza'],
                'emisor_id' => $users['carlos@bookie20.test'],
                'receptor_id' => $users['admin@bookie20.test'],
                'contenido' => 'Sí, solo estaba probando la animación y el historial.',
                'editado' => false,
                'read_at' => $now->copy()->subHour(),
                'created_at' => $now->copy()->subHours(2),
                'updated_at' => $now->copy()->subHours(2),
            ],
            [
                'chat_id' => $chats['Chat con Operador Central'],
                'emisor_id' => $users['daniel@bookie20.test'],
                'receptor_id' => $users['operador@bookie20.test'],
                'contenido' => '¿Cuándo se resolverá mi predicción de esta semana?',
                'editado' => false,
                'read_at' => null,
                'created_at' => $now->copy()->subHours(8),
                'updated_at' => $now->copy()->subHours(8),
            ],
            [
                'chat_id' => $chats['Chat con Sofía Navarro'],
                'emisor_id' => $users['sofia@bookie20.test'],
                'receptor_id' => $users['valentina@bookie20.test'],
                'contenido' => 'Yo apostaría poco en la siguiente predicción, está difícil.',
                'editado' => false,
                'read_at' => $now->copy()->subDay(),
                'created_at' => $now->copy()->subDay()->subMinutes(20),
                'updated_at' => $now->copy()->subDay()->subMinutes(20),
            ],
            [
                'chat_id' => $chats['Chat con Sofía Navarro'],
                'emisor_id' => $users['valentina@bookie20.test'],
                'receptor_id' => $users['sofia@bookie20.test'],
                'contenido' => 'Sí, voy a esperar a que el admin resuelva las pendientes.',
                'editado' => false,
                'read_at' => null,
                'created_at' => $now->copy()->subDay(),
                'updated_at' => $now->copy()->subDay(),
            ],
        ]);
    }
}
