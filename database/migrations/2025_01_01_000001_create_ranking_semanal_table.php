<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ranking_semanal', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('semana');          // número ISO de semana: 1-53
            $table->unsignedInteger('anio');            // año ISO
            $table->date('fecha_inicio');               // lunes de esa semana
            $table->date('fecha_fin');                  // domingo de esa semana
            $table->unsignedTinyInteger('posicion');    // 1-5
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('puntos');
            $table->decimal('total_ganado', 12, 2)->default(0);
            $table->timestamps();

            // Clave única: un usuario no puede aparecer dos veces en la misma semana
            $table->unique(['semana', 'anio', 'user_id'], 'unique_semana_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ranking_semanal');
    }
};
