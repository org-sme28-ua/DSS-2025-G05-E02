<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Obsoleto: la ruleta ya no usa tabla especifica.
        // Las tiradas se guardan en la tabla general `apuestas`.
    }

    public function down(): void
    {
        // Sin cambios. Si ya existe `roulette_bets` de una version anterior,
        // se puede eliminar manualmente tras confirmar que no se necesita.
    }
};
