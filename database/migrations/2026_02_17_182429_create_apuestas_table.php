<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apuestas', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('juego_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('monto', 12, 2);
            $table->decimal('cuota', 5, 2);

            $table->enum('estado', [
                'pendiente',
                'ganada',
                'perdida'
            ])->default('pendiente');

            // Campos genericos opcionales para guardar detalles de juegos sencillos
            // como la ruleta sin crear una tabla especifica por juego.
            $table->string('seleccion')->nullable();
            $table->string('resultado')->nullable();

            $table->timestamp('fecha')->useCurrent();

            $table->timestamps();

            
            $table->index('estado');
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apuestas');
    }
};
