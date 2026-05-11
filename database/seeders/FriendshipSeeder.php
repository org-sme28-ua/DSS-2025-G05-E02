<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FriendshipSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $users = DB::table('users')->pluck('id', 'email');

        DB::table('user_user')->insert([
            ['user_id' => $users['carlos@bookie20.test'], 'friend_id' => $users['lucia@bookie20.test'], 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['lucia@bookie20.test'], 'friend_id' => $users['carlos@bookie20.test'], 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['carlos@bookie20.test'], 'friend_id' => $users['sofia@bookie20.test'], 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['sofia@bookie20.test'], 'friend_id' => $users['carlos@bookie20.test'], 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['mateo@bookie20.test'], 'friend_id' => $users['valentina@bookie20.test'], 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => $users['valentina@bookie20.test'], 'friend_id' => $users['mateo@bookie20.test'], 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}