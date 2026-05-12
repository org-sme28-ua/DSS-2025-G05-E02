<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notificaciones')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE notificaciones MODIFY tipo ENUM('apuesta','promo','alerta','chat','info','mensaje','sistema') NOT NULL DEFAULT 'info'");
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('notificaciones')) {
            return;
        }

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE notificaciones MODIFY tipo ENUM('apuesta','promo','alerta','chat') NOT NULL DEFAULT 'apuesta'");
        }
    }
};
