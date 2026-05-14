<?php

namespace App\Services;

use App\Http\Controllers\ApuestaController;
use App\Http\Controllers\BilleteraController;
use App\Models\Apuesta;
use App\Models\Juego;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ApuestaService
{
    public function __construct(
        private readonly BilleteraController $billeteraController,
        private readonly ApuestaController $apuestaController,
    ) {
    }

    public function procesarApuesta(User $user, array $datos): array
    {
        return DB::transaction(function () use ($user, $datos) {
            $montoCentimos = (int) round(((float) ($datos['monto'] ?? 0)) * 100);

            if ($montoCentimos <= 0) {
                throw new RuntimeException('La apuesta debe ser mayor que 0.');
            }

            if (empty($datos['juego_nombre'])) {
                throw new RuntimeException('Debe indicarse el juego de la apuesta.');
            }

            $estado = (string) ($datos['estado'] ?? 'pendiente');
            $cuota = (float) ($datos['cuota'] ?? 2.00);

            if (!in_array($estado, ['pendiente', 'aceptada', 'ganada', 'perdida'], true)) {
                throw new RuntimeException('Estado de apuesta no válido.');
            }

            $billetera = $this->billeteraController->obtenerBilleteraBloqueada($user);
            $this->billeteraController->comprobarSaldoDisponible($billetera, $montoCentimos);

            $balanceAntesCentimos = (int) round(((float) $billetera->saldoDisponible) * 100);
            $balanceDespuesCentimos = match ($estado) {
                'ganada' => $balanceAntesCentimos + (int) round($montoCentimos * max($cuota - 1, 0)),
                'perdida', 'pendiente', 'aceptada' => $balanceAntesCentimos - $montoCentimos,
            };

            $billetera = $this->billeteraController->actualizarSaldoDesdeCentimos($billetera, $balanceDespuesCentimos);

            $juego = Juego::firstOrCreate(
                ['nombre' => (string) $datos['juego_nombre']],
                [
                    'categoria' => (string) ($datos['juego_categoria'] ?? 'General'),
                    'estado' => 'abierta',
                ]
            );

            $apuesta = $this->apuestaController->crearApuestaDesdeServicio([
                'user_id' => $user->id,
                'juego_id' => $juego->id,
                'tipo' => (string) ($datos['tipo'] ?? 'general'),
                'descripcion' => $datos['descripcion'] ?? null,
                'seleccion' => $datos['seleccion'] ?? null,
                'resultado' => $datos['resultado'] ?? null,
                'monto' => $montoCentimos / 100,
                'cuota' => $cuota,
                'estado' => $estado,
                'fecha' => $datos['fecha'] ?? now(),
                'balance_antes' => $balanceAntesCentimos / 100,
                'balance_despues' => $balanceDespuesCentimos / 100,
                'resuelta_at' => in_array($estado, ['ganada', 'perdida'], true) ? now() : null,
                'admin_id' => $datos['admin_id'] ?? null,
            ]);

            if (!empty($datos['notificacion_titulo']) && !empty($datos['notificacion_mensaje'])) {
                Notificacion::crearNotificacion(
                    $user->id,
                    (string) $datos['notificacion_titulo'],
                    (string) $datos['notificacion_mensaje'],
                    (string) ($datos['notificacion_tipo'] ?? 'apuesta')
                );
            }

            return [
                'apuesta' => $apuesta,
                'apuesta_id' => $apuesta->id,
                'amount' => $montoCentimos / 100,
                'balance_before' => $balanceAntesCentimos / 100,
                'balance_after' => (float) $billetera->saldoDisponible,
                'estado' => $estado,
            ];
        });
    }
}
