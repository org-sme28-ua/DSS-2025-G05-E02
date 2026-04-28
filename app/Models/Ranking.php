<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    protected $fillable = [
        'user_id',
        'posicion',
        'puntos',
        'total_ganado',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function actualizarRankingUsuario($user, $ganancia, $nuevosPuntos)
    {
        $ranking = Ranking::firstOrCreate(
            ['user_id' => $user->id],
            ['posicion' => 0, 'puntos' => 0, 'total_ganado' => 0]
        );

        $ranking->puntos += $nuevosPuntos;
        $ranking->total_ganado += $ganancia;
        $previaPosicion = $ranking->posicion;
        // Recalcular la posición entre todos los rankings (sencillo: ordenar por puntos descendentes)
        $ranking->save();

        // Actualizar posiciones globales: recorre todos los rankings y ordénalos
        $rankingsOrdenados = Ranking::orderByDesc('puntos')->get();
        foreach ($rankingsOrdenados as $idx => $rk) {
            $rk->posicion = $idx + 1;
            $rk->save();
        }

        // Si subió posición, notificarle (hook, requiere modelo Notificacion)
        $ranking->refresh();
        if ($ranking->posicion < $previaPosicion || $previaPosicion == 0) {
            \App\Models\Notificacion::crearNotificacion(
                $user->id,
                '¡Has subido en el ranking!',
                "¡Felicidades! Ahora estás en la posición {$ranking->posicion}."
            );
        }
    }
}
