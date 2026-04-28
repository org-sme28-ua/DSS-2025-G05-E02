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
            ['user_id' => $users['carlos@bookie20.test'], 'tipo' => 'apuesta', 'titulo' => 'Apuesta registrada', 'mensaje' => 'Tu ticket se ha creado correctamente.', 'leido' => true, 'fecha' => $now->copy()->subHours(12), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['lucia@bookie20.test'], 'tipo' => 'promo', 'titulo' => 'Bonus VIP acreditado', 'mensaje' => 'Se ha aplicado tu bonus semanal VIP.', 'leido' => false, 'fecha' => $now->copy()->subDay(), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['mateo@bookie20.test'], 'tipo' => 'alerta', 'titulo' => 'Documento pendiente', 'mensaje' => 'Debes completar la verificación KYC.', 'leido' => false, 'fecha' => $now->copy()->subDays(2), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['sofia@bookie20.test'], 'tipo' => 'chat', 'titulo' => 'Nuevo mensaje de soporte', 'mensaje' => 'Tienes una respuesta en tu conversación abierta.', 'leido' => true, 'fecha' => $now->copy()->subDays(3), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['daniel@bookie20.test'], 'tipo' => 'apuesta', 'titulo' => 'Ticket en revisión', 'mensaje' => 'Tu apuesta pendiente sigue en validación.', 'leido' => false, 'fecha' => $now->copy()->subHours(4), 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['valentina@bookie20.test'], 'tipo' => 'promo', 'titulo' => 'Gira gratis activada', 'mensaje' => 'Ya puedes usar tus tiradas gratis en slots.', 'leido' => false, 'fecha' => $now->copy()->subDays(5), 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}