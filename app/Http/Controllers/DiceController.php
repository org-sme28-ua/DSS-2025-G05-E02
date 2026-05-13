<?php

namespace App\Http\Controllers;

use App\Models\Apuesta;
use App\Models\Billetera;
use App\Models\Juego;
use App\Models\Notificacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DiceController extends Controller
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
            ->where('tipo', 'dados')
            ->latest('fecha')
            ->take(8)
            ->get();

        return view('games.dice', compact('wallet', 'lastBets'));
    }

    public function play(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'seleccion' => ['required', 'in:bajo,alto'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999.99'],
        ], [
            'seleccion.required' => 'Elige bajo o alto antes de tirar el dado.',
            'seleccion.in' => 'Solo puedes elegir bajo o alto.',
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

                $roll = random_int(1, 6);
                $resultBand = $roll <= 3 ? 'bajo' : 'alto';
                $won = $selected === $resultBand;
                $balanceAfterCents = $won
                    ? $balanceBeforeCents + $amountCents
                    : $balanceBeforeCents - $amountCents;

                $wallet->saldoDisponible = $balanceAfterCents / 100;
                $wallet->save();

                $juego = Juego::firstOrCreate(
                    ['nombre' => 'Dados'],
                    ['categoria' => 'Azar simple', 'estado' => 'abierta']
                );

                Apuesta::create([
                    'user_id' => $user->id,
                    'juego_id' => $juego->id,
                    'tipo' => 'dados',
                    'descripcion' => 'Apuesta a dado bajo (1-3) o alto (4-6)',
                    'seleccion' => $selected === 'bajo' ? 'Bajo (1-3)' : 'Alto (4-6)',
                    'resultado' => 'Dado ' . $roll . ' - ' . ucfirst($resultBand),
                    'monto' => $amountCents / 100,
                    'cuota' => 2.00,
                    'estado' => $won ? 'ganada' : 'perdida',
                    'fecha' => now(),
                    'balance_antes' => $balanceBeforeCents / 100,
                    'balance_despues' => $balanceAfterCents / 100,
                    'resuelta_at' => now(),
                ]);

                Notificacion::crearNotificacion(
                    $user->id,
                    $won ? 'Dados ganados' : 'Dados perdidos',
                    $won
                        ? 'Salió ' . $roll . ' y acertaste. Ganaste ' . number_format($amountCents / 100, 2, ',', '.') . ' EUR netos.'
                        : 'Salió ' . $roll . ' y perdiste ' . number_format($amountCents / 100, 2, ',', '.') . ' EUR.',
                    'apuesta'
                );

                return [
                    'seleccion' => $selected,
                    'roll' => $roll,
                    'resultado' => $resultBand,
                    'won' => $won,
                    'amount' => $amountCents / 100,
                    'balance_after' => $balanceAfterCents / 100,
                ];
            });
        } catch (\Throwable $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('dice.index')
            ->with('dice_result', $result);
    }
}
