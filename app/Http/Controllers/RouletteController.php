<?php

namespace App\Http\Controllers;

use App\Models\Apuesta;
use App\Models\Billetera;
use App\Models\Juego;
use App\Models\Notificacion;
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

        $lastBets = Apuesta::query()
            ->where('user_id', $user->id)
            ->where('tipo', 'ruleta')
            ->with('juego')
            ->latest('fecha')
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
            $wallet = Billetera::where('user_id', $user->id)->lockForUpdate()->first();

            if (!$wallet) {
                $wallet = Billetera::create([
                    'user_id' => $user->id,
                    'saldoDisponible' => 0,
                    'moneda' => 'EUR',
                ]);
            }

            $balanceBeforeCents = (int) round(((float) $wallet->saldoDisponible) * 100);

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

            $juego = Juego::firstOrCreate(
                ['nombre' => 'Ruleta'],
                ['categoria' => 'Casino', 'estado' => 'abierta']
            );

            $colorLabels = ['red' => 'Rojo', 'black' => 'Negro', 'green' => 'Verde'];

            $apuesta = Apuesta::create([
                'user_id' => $user->id,
                'juego_id' => $juego->id,
                'tipo' => 'ruleta',
                'descripcion' => 'Apuesta simple a color en ruleta',
                'seleccion' => $selectedColor,
                'resultado' => $resultColor,
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
                $won ? 'Ruleta ganada' : 'Ruleta perdida',
                'Apostaste a ' . ($colorLabels[$selectedColor] ?? $selectedColor) . ' y salió ' . ($colorLabels[$resultColor] ?? $resultColor) . '.',
                'apuesta'
            );

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
            return back()->withErrors(['amount' => $result['error']])->withInput();
        }

        return redirect()->route('roulette.index')->with('roulette_result', $result);
    }

    private function spinRoulette(): string
    {
        $slot = random_int(0, 36);

        if ($slot === 0) {
            return 'green';
        }

        return $slot <= 18 ? 'red' : 'black';
    }
}
