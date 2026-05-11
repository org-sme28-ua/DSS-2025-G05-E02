<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ([
            'user_user',
            'mensajes',
            'notificaciones',
            'apuestas',
            'rankings',
            'chats',
            'billeteras',
            'settings',
            'parametros_ganancia',
            'juegos',
            'users',
        ] as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            UserSeeder::class,
            JuegoSeeder::class,
            SettingSeeder::class,
            ParametroGananciaSeeder::class,
            BilleteraSeeder::class,
            ChatSeeder::class,
            RankingSeeder::class,
            FriendshipSeeder::class,
            ApuestaSeeder::class,
            NotificacionSeeder::class,
            MensajeSeeder::class,
        ]);
    }
}
