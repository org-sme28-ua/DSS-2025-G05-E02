<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apuestas', function (Blueprint $table) {
            if (!Schema::hasColumn('apuestas', 'tipo')) {
                $table->string('tipo', 40)->default('general')->after('juego_id')->index();
            }

            if (!Schema::hasColumn('apuestas', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('tipo');
            }

            if (!Schema::hasColumn('apuestas', 'seleccion')) {
                $table->text('seleccion')->nullable()->after('descripcion');
            }

            if (!Schema::hasColumn('apuestas', 'resultado')) {
                $table->text('resultado')->nullable()->after('seleccion');
            }

            if (!Schema::hasColumn('apuestas', 'balance_antes')) {
                $table->decimal('balance_antes', 12, 2)->nullable()->after('fecha');
            }

            if (!Schema::hasColumn('apuestas', 'balance_despues')) {
                $table->decimal('balance_despues', 12, 2)->nullable()->after('balance_antes');
            }

            if (!Schema::hasColumn('apuestas', 'resuelta_at')) {
                $table->timestamp('resuelta_at')->nullable()->after('balance_despues');
            }

            if (!Schema::hasColumn('apuestas', 'admin_id')) {
                $table->unsignedBigInteger('admin_id')->nullable()->after('resuelta_at')->index();
            }
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE apuestas MODIFY estado ENUM('pendiente','aceptada','rechazada','ganada','perdida') NOT NULL DEFAULT 'pendiente'");
        }
    }

    public function down(): void
    {
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE apuestas MODIFY estado ENUM('pendiente','ganada','perdida') NOT NULL DEFAULT 'pendiente'");
        }

        Schema::table('apuestas', function (Blueprint $table) {
            foreach (['admin_id', 'resuelta_at', 'balance_despues', 'balance_antes', 'resultado', 'seleccion', 'descripcion', 'tipo'] as $column) {
                if (Schema::hasColumn('apuestas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
