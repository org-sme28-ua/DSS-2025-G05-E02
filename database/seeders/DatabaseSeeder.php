<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('user_user')->truncate();
        DB::table('mensajes')->truncate();
        DB::table('notificaciones')->truncate();
        DB::table('apuestas')->truncate();
        DB::table('rankings')->truncate();
        DB::table('chats')->truncate();
        DB::table('billeteras')->truncate();
        DB::table('settings')->truncate();
        DB::table('juegos')->truncate();
        DB::table('users')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
            UserSeeder::class,
            JuegoSeeder::class,
            SettingSeeder::class,
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