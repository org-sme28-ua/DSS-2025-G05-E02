<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BilleteraSeeder::class,
            JuegoSeeder::class,
            ApuestaSeeder::class,
            ChatSeeder::class,
            MensajeSeeder::class,
            NotificacionSeeder::class,
            RankingSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
