<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('juegos')) {
            return;
        }

        $now = now();

        if (DB::table('juegos')->where('nombre', 'Slot Machine')->exists()) {
            DB::table('juegos')
                ->where('nombre', 'Slot Machine')
                ->update([
                    'nombre' => 'Dados',
                    'categoria' => 'Azar simple',
                    'estado' => 'abierta',
                    'updated_at' => $now,
                ]);
        } else {
            DB::table('juegos')->updateOrInsert(
                ['nombre' => 'Dados'],
                [
                    'categoria' => 'Azar simple',
                    'estado' => 'abierta',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        if (DB::table('juegos')->where('nombre', 'Bingo')->exists()) {
            DB::table('juegos')
                ->where('nombre', 'Bingo')
                ->update([
                    'nombre' => 'Cara o Cruz',
                    'categoria' => 'Azar simple',
                    'estado' => 'abierta',
                    'updated_at' => $now,
                ]);
        } else {
            DB::table('juegos')->updateOrInsert(
                ['nombre' => 'Cara o Cruz'],
                [
                    'categoria' => 'Azar simple',
                    'estado' => 'abierta',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('juegos')) {
            return;
        }

        DB::table('juegos')
            ->where('nombre', 'Dados')
            ->update(['nombre' => 'Slot Machine', 'categoria' => 'Slots', 'updated_at' => now()]);

        DB::table('juegos')
            ->where('nombre', 'Cara o Cruz')
            ->update(['nombre' => 'Bingo', 'categoria' => 'Casino', 'updated_at' => now()]);
    }
};
