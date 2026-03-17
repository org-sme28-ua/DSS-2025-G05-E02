<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Notificacion;

class NotificacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Notificacion::create([
            'user_id' => 1,
            'tipo' => 'apuesta',
            'titulo' => 'Apuesta aceptada',
            'mensaje' => 'Tu apuesta se ha registrado con éxito.',
            'leido' => false,
        ]);

        Notificacion::create([
            'user_id' => 1,
            'tipo' => 'promo',
            'titulo' => 'Bonificación activa',
            'mensaje' => 'Has obtenido +20 de saldo por jugar en ruleta.',
            'leido' => false,
        ]);
    }
}
