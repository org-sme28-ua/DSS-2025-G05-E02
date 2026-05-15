<?php

namespace App\Http\Controllers;

use App\Models\Apuesta;
use App\Models\Billetera;
use App\Models\Juego;
use App\Models\Notificacion;
use App\Models\Ranking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CoinFlipController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $wallet = Billetera::firstOrCreate(
            ['user_id' => $user->id],
            ['saldoDisponible' => 0, 'moneda' => 'EUR']
        );

        $lastBets = $user->apuestas()
            ->with('juego')
            ->where('tipo', 'cara_cruz')
            ->latest('fecha')
            ->take(8)
            ->get();

        return view('games.coin', compact('wallet', 'lastBets'));
    }

    public function play(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'seleccion' => ['required', 'in:cara,cruz'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999.99'],
        ], [
            'seleccion.required' => 'Elige cara o cruz antes de jugar.',
            'seleccion.in' => 'Solo puedes elegir cara o cruz.',
            'amount.required' => 'Indica cuánto quieres apostar.',
            'amount.numeric' => 'La cantidad apostada debe ser un número.',
            'amount.min' => 'La apuesta mínima es 1 EUR.',
        ]);

        $user = auth()->user();
        $selected = $validated['seleccion'];
        $amountCents = (int) round(((float) $validated['amount']) * 100);

        try {
            $result = DB::transaction(function () use ($user, $selected, $amountCents) {
                $wallet = Billetera::firstOrCreate(
                    ['user_id' => $user->id],
                    ['saldoDisponible' => 0, 'moneda' => 'EUR']
                );

                $wallet = Billetera::whereKey($wallet->id)->lockForUpdate()->firstOrFail();
                $balanceBeforeCents = (int) round(((float) $wallet->saldoDisponible) * 100);

                if ($amountCents <= 0) {
                    throw new \RuntimeException('La apuesta debe ser mayor que 0.');
                }

                if ($balanceBeforeCents < $amountCents) {
                    throw new \RuntimeException('Saldo insuficiente para hacer esa apuesta.');
                }

                $resultSide = random_int(0, 1) === 0 ? 'cara' : 'cruz';
                $won = $selected === $resultSide;
                $monto = $amountCents / 100;
                $cuota = 2.00;

                $balanceAfterCents = $won
                    ? $balanceBeforeCents + $amountCents
                    : $balanceBeforeCents - $amountCents;

                $wallet->saldoDisponible = $balanceAfterCents / 100;
                $wallet->save();

                $juego = Juego::firstOrCreate(
                    ['nombre' => 'Cara o Cruz'],
                    ['categoria' => 'Azar simple', 'estado' => 'abierta']
                );

                Apuesta::create([
                    'user_id'         => $user->id,
                    'juego_id'        => $juego->id,
                    'tipo'            => 'cara_cruz',
                    'descripcion'     => 'Apuesta simple a cara o cruz',
                    'seleccion'       => ucfirst($selected),
                    'resultado'       => ucfirst($resultSide),
                    'monto'           => $monto,
                    'cuota'           => $cuota,
                    'estado'          => $won ? 'ganada' : 'perdida',
                    'fecha'           => now(),
                    'balance_antes'   => $balanceBeforeCents / 100,
                    'balance_despues' => $balanceAfterCents / 100,
                    'resuelta_at'     => now(),
                ]);

                // ── Ranking ──────────────────────────────────────────────────
                // Ganada: floor(monto × cuota × 10)  → premia la ganancia real
                // Perdida: floor(monto × 2)           → fidelidad por participar
                if ($won) {
                    $ganancia     = $monto;
                    $nuevosPuntos = (int) floor($monto * $cuota * 10);
                } else {
                    $ganancia     = 0;
                    $nuevosPuntos = (int) floor($monto * 2);
                }
                Ranking::actualizarRankingUsuario($user, $ganancia, $nuevosPuntos);
                // ─────────────────────────────────────────────────────────────

                Notificacion::crearNotificacion(
                    $user->id,
                    $won ? 'Cara o cruz ganada' : 'Cara o cruz perdida',
                    $won
                        ? 'Acertaste ' . ucfirst($resultSide) . ' y ganaste ' . number_format($monto, 2, ',', '.') . ' EUR netos.'
                        : 'Salió ' . ucfirst($resultSide) . ' y perdiste ' . number_format($monto, 2, ',', '.') . ' EUR.',
                    'apuesta'
                );

                return [
                    'seleccion'     => $selected,
                    'resultado'     => $resultSide,
                    'won'           => $won,
                    'amount'        => $monto,
                    'balance_after' => $balanceAfterCents / 100,
                ];
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('coin.index')
            ->with('coin_result', $result);
    }
}
