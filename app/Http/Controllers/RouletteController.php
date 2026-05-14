<?php

namespace App\Http\Controllers;

use App\Models\Apuesta;
use App\Models\Billetera;
use App\Services\ApuestaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function play(Request $request, ApuestaService $apuestaService)
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
        $amount = round((float) $validated['amount'], 2);
        $resultColor = $this->spinRoulette();
        $won = $selectedColor === $resultColor;
        $colorLabels = ['red' => 'Rojo', 'black' => 'Negro', 'green' => 'Verde'];

        try {
            $result = $apuestaService->procesarApuesta($user, [
                'juego_nombre' => 'Ruleta',
                'juego_categoria' => 'Casino',
                'tipo' => 'ruleta',
                'descripcion' => 'Apuesta simple a color en ruleta',
                'seleccion' => $selectedColor,
                'resultado' => $resultColor,
                'monto' => $amount,
                'cuota' => 2.00,
                'estado' => $won ? 'ganada' : 'perdida',
                'notificacion_titulo' => $won ? 'Ruleta ganada' : 'Ruleta perdida',
                'notificacion_mensaje' => 'Apostaste a ' . ($colorLabels[$selectedColor] ?? $selectedColor) . ' y salió ' . ($colorLabels[$resultColor] ?? $resultColor) . '.',
            ]);
        } catch (\Throwable $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return redirect()->route('roulette.index')->with('roulette_result', array_merge($result, [
            'selected_color' => $selectedColor,
            'result_color' => $resultColor,
            'won' => $won,
        ]));
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
