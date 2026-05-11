<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametros_ganancia', function (Blueprint $table) {
	    $table->id();
	    $table->foreignId('juego_id')->constrained('juegos')->cascadeOnDelete();
	    $table->decimal('multiplicacion_por_juego', 8, 2);
	    $table->decimal('bonus_por_racha', 8, 2);
	    $table->timestamps();
	});
    }

    public function down(): void
    {
        Schema::dropIfExists('parametros_ganancia');
    }
};
