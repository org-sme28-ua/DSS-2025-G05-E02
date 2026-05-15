<?php

namespace App\Http\Controllers;

use App\Models\Apuesta;
use App\Models\Billetera;
use App\Models\Juego;
use App\Models\Notificacion;
use App\Models\Ranking;
use App\Services\ApuestaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PredictionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $wallet = Billetera::firstOrCreate(
            ['user_id' => $user->id],
            ['saldoDisponible' => 0, 'moneda' => 'EUR']
        );

        $bets = Apuesta::query()
            ->with('juego')
            ->where('user_id', $user->id)
            ->where('tipo', 'prediccion')
            ->latest('fecha')
            ->get();

        return view('prediction.index', compact('wallet', 'bets'));
    }

    public function store(Request $request, ApuestaService $apuestaService)
    {
        $data = $request->validate([
            'descripcion' => ['required', 'string', 'min:8', 'max:255'],
            'seleccion'   => ['required', 'string', 'min:2', 'max:1000'],
            'amount'      => ['required', 'numeric', 'min:1', 'max:999999.99'],
        ], [
            'descripcion.required' => 'Describe la predicción que quieres apostar.',
            'descripcion.min'      => 'La predicción debe tener al menos 8 caracteres.',
            'seleccion.required'   => 'Indica cuál es tu resultado esperado.',
            'amount.required'      => 'Indica cuánto quieres apostar.',
            'amount.min'           => 'La apuesta mínima es 1 EUR.',
        ]);

        $user = Auth::user();
        $amount = round((float) $data['amount'], 2);

        try {
            // Utilizamos el servicio para procesar la apuesta de forma centralizada
            $apuestaService->procesarApuesta($user, [
                'juego_nombre' => 'Predicción',
                'juego_categoria' => 'Predicciones',
                'tipo' => 'prediccion',
                'descripcion' => $data['descripcion'],
                'seleccion' => $data['seleccion'],
                'monto' => $amount,
                'cuota' => 2.00,
                'estado' => 'pendiente',
                'notificacion_titulo' => 'Predicción enviada',
                'notificacion_mensaje' => 'Tu predicción se ha enviado al panel de administración y queda pendiente de revisión.',
            ]);

            // Puntos de participación (Fidelidad)
            // Se otorgan puntos por el hecho de participar, independientemente de que el admin la resuelva luego.
            $nuevosPuntos = (int) floor($amount * 2);
            Ranking::actualizarRankingUsuario($user, 0, $nuevosPuntos);

        } catch (\Throwable $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        return redirect()->route('prediction.index')
            ->with('success', 'Predicción enviada. Queda pendiente de revisión por administración.');
    }
}
