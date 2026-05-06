<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Apuesta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'juego_id',
        'monto',
        'cuota',
        'estado',
        'seleccion',
        'resultado',
        'fecha'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'cuota' => 'decimal:2',
        'fecha' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function juego()
    {
        return $this->belongsTo(Juego::class);
    }

    // SCOPES
    public function scopeActivas($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeGanadas($query)
    {
        return $query->where('estado', 'ganada');
    }

    public function scopePerdidas($query)
    {
        return $query->where('estado', 'perdida');
    }

    public function scopePorUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helpers
    public function calcularGanancia()
    {
        if ($this->estado === 'ganada') {
            return $this->monto * $this->cuota;
        }
        return 0;
    }

    // Lógica para liquidar la apuesta con resultado
    public function liquidar($resultado)
    {
        if (!in_array($resultado, ['ganada', 'perdida'])) {
            throw new \InvalidArgumentException("Resultado inválido para liquidar: $resultado");
        }

        $this->estado = $resultado;
        $this->save();

        $user = $this->user;

        if ($resultado === 'ganada') {
            $ganancia = $this->monto * $this->cuota;
            // Añadir dinero a billetera
            if (!$user->billetera) {
                $user->billetera()->create(['saldoDisponible' => 0, 'moneda' => 'EUR']);
                $user->load('billetera');
            }
            $user->billetera->saldoDisponible += $ganancia;
            $user->billetera->save();
            // Añadir puntos de fidelidad
            $puntos = intval($ganancia / 10);
            $user->sumarPuntosFidelidad($puntos);
            // Actualizar ranking
            \App\Models\Ranking::actualizarRankingUsuario($user, $ganancia, $puntos);
            $mensaje = "Tu apuesta fue ganada! Ganaste {$ganancia} y {$puntos} puntos de fidelidad.";
        } else {
            $mensaje = "Tu apuesta fue perdida. Mejor suerte la próxima vez.";
        }

        // Crear notificación con resultado
        \App\Models\Notificacion::crearNotificacion(
            $user->id,
            "Resultado de apuesta",
            $mensaje,
            'info'
        );
    }
}
