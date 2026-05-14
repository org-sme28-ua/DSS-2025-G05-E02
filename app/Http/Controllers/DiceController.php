<?php

namespace App\Http\Controllers;

use App\Models\Billetera;
use App\Services\ApuestaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function play(Request $request, ApuestaService $apuestaService): RedirectResponse
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
        $amount = round((float) $validated['amount'], 2);
        $roll = random_int(1, 6);
        $resultBand = $roll <= 3 ? 'bajo' : 'alto';
        $won = $selected === $resultBand;

        try {
            $result = $apuestaService->procesarApuesta($user, [
                'juego_nombre' => 'Dados',
                'juego_categoria' => 'Azar simple',
                'tipo' => 'dados',
                'descripcion' => 'Apuesta a dado bajo (1-3) o alto (4-6)',
                'seleccion' => $selected === 'bajo' ? 'Bajo (1-3)' : 'Alto (4-6)',
                'resultado' => 'Dado ' . $roll . ' - ' . ucfirst($resultBand),
                'monto' => $amount,
                'cuota' => 2.00,
                'estado' => $won ? 'ganada' : 'perdida',
                'notificacion_titulo' => $won ? 'Dados ganados' : 'Dados perdidos',
                'notificacion_mensaje' => $won
                    ? 'Salió ' . $roll . ' y acertaste. Ganaste ' . number_format($amount, 2, ',', '.') . ' EUR netos.'
                    : 'Salió ' . $roll . ' y perdiste ' . number_format($amount, 2, ',', '.') . ' EUR.',
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('dice.index')
            ->with('dice_result', array_merge($result, [
                'seleccion' => $selected,
                'roll' => $roll,
                'resultado' => $resultBand,
                'won' => $won,
            ]));
    }
}
