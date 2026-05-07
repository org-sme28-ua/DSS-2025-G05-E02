<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apuesta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'juego_id',
        'tipo',
        'descripcion',
        'seleccion',
        'resultado',
        'monto',
        'cuota',
        'estado',
        'fecha',
        'balance_antes',
        'balance_despues',
        'resuelta_at',
        'admin_id',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'cuota' => 'decimal:2',
        'balance_antes' => 'decimal:2',
        'balance_despues' => 'decimal:2',
        'fecha' => 'datetime',
        'resuelta_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function juego()
    {
        return $this->belongsTo(Juego::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', ['pendiente', 'aceptada']);
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

    public function calcularGananciaBruta(): float
    {
        if ($this->estado === 'ganada') {
            return (float) $this->monto * (float) $this->cuota;
        }

        return 0.0;
    }

    public function calcularGananciaNeta(): float
    {
        if ($this->estado === 'ganada') {
            return (float) $this->monto * ((float) $this->cuota - 1);
        }

        if ($this->estado === 'perdida') {
            return -1 * (float) $this->monto;
        }

        return 0.0;
    }

    public function estadoEtiqueta(): string
    {
        return match ($this->estado) {
            'pendiente' => 'Pendiente de revisión',
            'aceptada' => 'Aceptada',
            'rechazada' => 'Rechazada',
            'ganada' => 'Ganada',
            'perdida' => 'Perdida',
            default => ucfirst((string) $this->estado),
        };
    }

    public function liquidar($resultado)
    {
        if (!in_array($resultado, ['ganada', 'perdida'], true)) {
            throw new \InvalidArgumentException("Resultado inválido para liquidar: $resultado");
        }

        if (!in_array($this->estado, ['pendiente', 'aceptada'], true)) {
            throw new \RuntimeException('La apuesta ya está resuelta o rechazada.');
        }

        $this->estado = $resultado;
        $this->resultado = $resultado === 'ganada' ? 'Resultado ganador' : 'Resultado perdedor';
        $this->resuelta_at = now();
        $this->save();

        $user = $this->user;

        if ($resultado === 'ganada') {
            $ganancia = $this->calcularGananciaBruta();

            if (!$user->billetera) {
                $user->billetera()->create(['saldoDisponible' => 0, 'moneda' => 'EUR']);
                $user->load('billetera');
            }

            $user->billetera->saldoDisponible += $ganancia;
            $user->billetera->save();

            $puntos = intval($ganancia / 10);
            $user->sumarPuntosFidelidad($puntos);
            \App\Models\Ranking::actualizarRankingUsuario($user, $ganancia, $puntos);

            $mensaje = "Tu apuesta fue ganada. Ganaste {$ganancia} EUR y {$puntos} puntos de fidelidad.";
        } else {
            $mensaje = 'Tu apuesta fue perdida. Mejor suerte la próxima vez.';
        }

        \App\Models\Notificacion::crearNotificacion(
            $user->id,
            'Resultado de apuesta',
            $mensaje,
            'apuesta'
        );
    }
}
