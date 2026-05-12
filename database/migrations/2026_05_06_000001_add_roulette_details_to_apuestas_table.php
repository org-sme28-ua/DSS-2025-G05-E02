<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('apuestas', function (Blueprint $table) {
            if (!Schema::hasColumn('apuestas', 'seleccion')) {
                $table->string('seleccion')->nullable()->after('estado');
            }

            if (!Schema::hasColumn('apuestas', 'resultado')) {
                $table->string('resultado')->nullable()->after('seleccion');
            }
        });
    }

    public function down(): void
    {
        Schema::table('apuestas', function (Blueprint $table) {
            if (Schema::hasColumn('apuestas', 'resultado')) {
                $table->dropColumn('resultado');
            }

            if (Schema::hasColumn('apuestas', 'seleccion')) {
                $table->dropColumn('seleccion');
            }
        });
    }
};
