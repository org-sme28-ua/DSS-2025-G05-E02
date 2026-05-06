<?php

namespace App\Http\Controllers;

use App\Models\Apuesta;
use App\Models\Billetera;
use App\Models\Juego;
use App\Models\RouletteBet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RouletteController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $wallet = Billetera::firstOrCreate(
            ['user_id' => $user->id],
            ['saldoDisponible' => 0, 'moneda' => 'EUR']
        );

        $lastBets = RouletteBet::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        return view('roulette.index', compact('user', 'wallet', 'lastBets'));
    }

    public function play(Request $request)
    {
        $validated = $request->validate([
            'selected_color' => ['required', 'in:red,black'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999.99'],
        ], [
            'selected_color.required' => 'Elige rojo o negro antes de girar.',
            'selected_color.in' => 'Solo puedes apostar a rojo o negro.',
            'amount.required' => 'Indica cuánto quieres apostar.',
            'amount.numeric' => 'La cantidad apostada debe ser un número.',
            'amount.min' => 'La apuesta mínima es 1.',
            'amount.max' => 'La apuesta es demasiado alta.',
        ]);

        $user = Auth::user();
        $selectedColor = $validated['selected_color'];
        $amountCents = (int) round(((float) $validated['amount']) * 100);

        $result = DB::transaction(function () use ($user, $selectedColor, $amountCents) {
            $wallet = Billetera::where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if (!$wallet) {
                $wallet = Billetera::create([
                    'user_id' => $user->id,
                    'saldoDisponible' => 0,
                    'moneda' => 'EUR',
                ]);
            }

            $balanceBeforeCents = (int) round(((float) $wallet->saldoDisponible) * 100);

            if ($amountCents <= 0) {
                return ['error' => 'La apuesta debe ser mayor que 0.'];
            }

            if ($balanceBeforeCents < $amountCents) {
                return ['error' => 'Saldo insuficiente para hacer esa apuesta.'];
            }

            $resultColor = $this->spinRoulette();
            $won = $selectedColor === $resultColor;

            $balanceAfterCents = $won
                ? $balanceBeforeCents + $amountCents
                : $balanceBeforeCents - $amountCents;

            $wallet->saldoDisponible = $balanceAfterCents / 100;
            $wallet->save();

            /*
             * Creamos o reutilizamos el juego "Ruleta".
             * Esto permite que cada tirada aparezca también en la tabla apuestas.
             */
            $juego = Juego::firstOrCreate(
                ['nombre' => 'Ruleta'],
                [
                    'categoria' => 'Casino',
                    'estado' => 'abierta',
                ]
            );

            /*
             * Registro general en la tabla apuestas.
             * Aquí aparecerá la tirada en el admin o en cualquier listado general de apuestas.
             */
            $apuesta = Apuesta::create([
                'user_id' => $user->id,
                'juego_id' => $juego->id,
                'monto' => $amountCents / 100,
                'cuota' => 2.00,
                'estado' => $won ? 'ganada' : 'perdida',
                'fecha' => now(),
            ]);

            /*
             * Registro específico de ruleta.
             * Esta tabla guarda detalles que no existen en apuestas:
             * color elegido, color resultado, balance antes/después, etc.
             */
            RouletteBet::create([
                'user_id' => $user->id,
                'selected_color' => $selectedColor,
                'result_color' => $resultColor,
                'amount' => $amountCents / 100,
                'won' => $won,
                'balance_before' => $balanceBeforeCents / 100,
                'balance_after' => $balanceAfterCents / 100,
            ]);

            return [
                'apuesta_id' => $apuesta->id,
                'selected_color' => $selectedColor,
                'result_color' => $resultColor,
                'won' => $won,
                'amount' => $amountCents / 100,
                'balance_before' => $balanceBeforeCents / 100,
                'balance_after' => $balanceAfterCents / 100,
            ];
        });

        if (isset($result['error'])) {
            return back()
                ->withErrors(['amount' => $result['error']])
                ->withInput();
        }

        return redirect()
            ->route('roulette.index')
            ->with('roulette_result', $result);
    }

    private function spinRoulette(): string
    {
        /*
         * 37 casillas:
         * 0 = verde
         * 1-18 = rojo
         * 19-36 = negro
         */
        $slot = random_int(0, 36);

        if ($slot === 0) {
            return 'green';
        }

        return $slot <= 18 ? 'red' : 'black';
    }
}
