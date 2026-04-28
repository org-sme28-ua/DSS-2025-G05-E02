<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create(['clave' => 'apuestas_activas', 'valor' => 'true', 'descripcion' => 'Activa la sección de apuestas en el home', 'activo' => true]);
        Setting::create(['clave' => 'apuesta_minima', 'valor' => '5', 'descripcion' => 'Monto mínimo de apuesta', 'activo' => true]);
    }
}
