<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RankingSemanal extends Model
{
    protected $table = 'ranking_semanal';

    protected $fillable = [
        'semana',
        'anio',
        'fecha_inicio',
        'fecha_fin',
        'posicion',
        'user_id',
        'puntos',
        'total_ganado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Devuelve la etiqueta legible de la semana.
     * Ej: "Semana 20 · 12/05 – 18/05/2025"
     */
    public function getLabelAttribute(): string
    {
        return "Semana {$this->semana} · "
            . $this->fecha_inicio->format('d/m')
            . ' – '
            . $this->fecha_fin->format('d/m/Y');
    }
}
