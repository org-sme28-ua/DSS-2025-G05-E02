<?php

namespace App\Http\Controllers;

use App\Models\Billetera;
use App\Services\ApuestaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function play(Request $request, ApuestaService $apuestaService): RedirectResponse
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
        $amount = round((float) $validated['amount'], 2);
        $resultSide = random_int(0, 1) === 0 ? 'cara' : 'cruz';
        $won = $selected === $resultSide;

        try {
            $result = $apuestaService->procesarApuesta($user, [
                'juego_nombre' => 'Cara o Cruz',
                'juego_categoria' => 'Azar simple',
                'tipo' => 'cara_cruz',
                'descripcion' => 'Apuesta simple a cara o cruz',
                'seleccion' => ucfirst($selected),
                'resultado' => ucfirst($resultSide),
                'monto' => $amount,
                'cuota' => 2.00,
                'estado' => $won ? 'ganada' : 'perdida',
                'notificacion_titulo' => $won ? 'Cara o cruz ganada' : 'Cara o cruz perdida',
                'notificacion_mensaje' => $won
                    ? 'Acertaste ' . ucfirst($resultSide) . ' y ganaste ' . number_format($amount, 2, ',', '.') . ' EUR netos.'
                    : 'Salió ' . ucfirst($resultSide) . ' y perdiste ' . number_format($amount, 2, ',', '.') . ' EUR.',
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('coin.index')
            ->with('coin_result', array_merge($result, [
                'seleccion' => $selected,
                'resultado' => $resultSide,
                'won' => $won,
            ]));
    }
}
