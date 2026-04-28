<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BilleteraSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $users = DB::table('users')->pluck('id', 'email');

        DB::table('billeteras')->insert([
            ['user_id' => $users['admin@bookie20.test'], 'saldoDisponible' => 15000.00, 'moneda' => 'EUR', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['operador@bookie20.test'], 'saldoDisponible' => 8000.00, 'moneda' => 'EUR', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['carlos@bookie20.test'], 'saldoDisponible' => 420.50, 'moneda' => 'EUR', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['lucia@bookie20.test'], 'saldoDisponible' => 1290.75, 'moneda' => 'USD', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['mateo@bookie20.test'], 'saldoDisponible' => 75.00, 'moneda' => 'EUR', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['sofia@bookie20.test'], 'saldoDisponible' => 3325.40, 'moneda' => 'GBP', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['daniel@bookie20.test'], 'saldoDisponible' => 610.00, 'moneda' => 'EUR', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['valentina@bookie20.test'], 'saldoDisponible' => 250.25, 'moneda' => 'USD', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}