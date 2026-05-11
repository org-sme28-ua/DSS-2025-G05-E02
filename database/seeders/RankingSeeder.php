<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RankingSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $users = DB::table('users')->pluck('id', 'email');

        DB::table('rankings')->insert([
            ['user_id' => $users['sofia@bookie20.test'], 'posicion' => 1, 'puntos' => 9850.50, 'total_ganado' => 15240.00, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['lucia@bookie20.test'], 'posicion' => 2, 'puntos' => 7310.00, 'total_ganado' => 10210.25, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['carlos@bookie20.test'], 'posicion' => 3, 'puntos' => 4125.75, 'total_ganado' => 4980.00, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['daniel@bookie20.test'], 'posicion' => 4, 'puntos' => 2980.00, 'total_ganado' => 3120.40, 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['valentina@bookie20.test'], 'posicion' => 5, 'puntos' => 1875.25, 'total_ganado' => 2015.90, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}