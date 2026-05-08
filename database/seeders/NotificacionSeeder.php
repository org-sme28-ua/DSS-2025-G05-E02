<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificacionSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $users = DB::table('users')->pluck('id', 'email');

        DB::table('notificaciones')->insert([
            ['user_id' => $users['carlos@bookie20.test'], 'tipo' => 'apuesta', 'titulo' => 'Ruleta resuelta', 'mensaje' => 'Tu apuesta a rojo perdió porque salió negro.', 'leido' => true, 'fecha' => $now->copy()->subDays(13), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['lucia@bookie20.test'], 'tipo' => 'apuesta', 'titulo' => 'Ruleta ganada', 'mensaje' => 'Tu apuesta a negro fue ganadora.', 'leido' => false, 'fecha' => $now->copy()->subDays(11), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['daniel@bookie20.test'], 'tipo' => 'apuesta', 'titulo' => 'Predicción pendiente', 'mensaje' => 'Tu predicción queda pendiente de revisión por administración.', 'leido' => false, 'fecha' => $now->copy()->subDays(4), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['valentina@bookie20.test'], 'tipo' => 'apuesta', 'titulo' => 'Predicción aceptada', 'mensaje' => 'Tu predicción ha sido aceptada y queda pendiente de resolución.', 'leido' => false, 'fecha' => $now->copy()->subDays(3), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['carlos@bookie20.test'], 'tipo' => 'apuesta', 'titulo' => 'Predicción perdida', 'mensaje' => 'Tu predicción fue marcada como perdida.', 'leido' => false, 'fecha' => $now->copy()->subDay(), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['lucia@bookie20.test'], 'tipo' => 'apuesta', 'titulo' => 'Predicción ganada', 'mensaje' => 'Tu predicción fue marcada como ganada y el premio fue abonado.', 'leido' => false, 'fecha' => $now->copy()->subHours(8), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['admin@bookie20.test'], 'tipo' => 'mensaje', 'titulo' => 'Nuevo mensaje de Lucía Herrera', 'mensaje' => 'Lucía Herrera te ha escrito en el chat.', 'leido' => false, 'fecha' => $now->copy()->subMinutes(35), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['operador@bookie20.test'], 'tipo' => 'mensaje', 'titulo' => 'Nuevo mensaje de Daniel Ortega', 'mensaje' => 'Daniel Ortega te ha enviado un mensaje privado.', 'leido' => false, 'fecha' => $now->copy()->subHours(8), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['sofia@bookie20.test'], 'tipo' => 'mensaje', 'titulo' => 'Nuevo mensaje de Valentina Cruz', 'mensaje' => 'Valentina Cruz ha respondido a tu conversación.', 'leido' => false, 'fecha' => $now->copy()->subDay(), 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
