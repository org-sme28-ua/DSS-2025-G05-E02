<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Ranking;

class RankingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ranking::create(['user_id' => 1, 'posicion' => 1, 'puntos' => 150, 'total_ganado' => 1200]);
        Ranking::create(['user_id' => 2, 'posicion' => 2, 'puntos' => 100, 'total_ganado' => 680]);
    }
}
