<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParametroGanancia extends Model
{
    protected $table = 'parametros_ganancia';

    protected $fillable = [
        'juego_id',
        'multiplicacion_por_juego',
        'bonus_por_racha',
    ];

    public function juego()
    {
        return $this->belongsTo(Juego::class, 'juego_id');
    }
}
