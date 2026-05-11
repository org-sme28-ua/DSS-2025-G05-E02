<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Esta migración queda anulada intencionadamente.
     *
     * La ruleta ya no usa una tabla propia. Todas sus apuestas se guardan
     * en la tabla `apuestas` con tipo = `ruleta`.
     */
    public function up(): void
    {
        // No crear roulette_bets.
    }

    public function down(): void
    {
        Schema::dropIfExists('roulette_bets');
    }
};
