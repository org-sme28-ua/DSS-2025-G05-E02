<?php

namespace App\Http\Controllers;

use App\Models\Apuesta;
use App\Models\Billetera;
use App\Models\Juego;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function store(Request $request)
    {
        $data = $request->validate([
            'descripcion' => ['required', 'string', 'min:8', 'max:255'],
            'seleccion' => ['required', 'string', 'min:2', 'max:1000'],
            'amount' => ['required', 'numeric', 'min:1', 'max:999999.99'],
        ], [
            'descripcion.required' => 'Describe la predicción que quieres apostar.',
            'descripcion.min' => 'La predicción debe tener al menos 8 caracteres.',
            'seleccion.required' => 'Indica cuál es tu resultado esperado.',
            'amount.required' => 'Indica cuánto quieres apostar.',
            'amount.min' => 'La apuesta mínima es 1 EUR.',
        ]);

        $user = Auth::user();
        $amountCents = (int) round(((float) $data['amount']) * 100);

        $result = DB::transaction(function () use ($user, $data, $amountCents) {
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
                return ['error' => 'Saldo insuficiente para crear esta predicción.'];
            }

            $balanceAfterCents = $balanceBeforeCents - $amountCents;
            $wallet->saldoDisponible = $balanceAfterCents / 100;
            $wallet->save();

            $game = Juego::firstOrCreate(
                ['nombre' => 'Predicción'],
                ['categoria' => 'Predicciones', 'estado' => 'abierta']
            );

            $bet = Apuesta::create([
                'user_id' => $user->id,
                'juego_id' => $game->id,
                'tipo' => 'prediccion',
                'descripcion' => $data['descripcion'],
                'seleccion' => $data['seleccion'],
                'monto' => $amountCents / 100,
                'cuota' => 2.00,
                'estado' => 'pendiente',
                'fecha' => now(),
                'balance_antes' => $balanceBeforeCents / 100,
                'balance_despues' => $balanceAfterCents / 100,
            ]);

            Notificacion::crearNotificacion(
                $user->id,
                'Predicción enviada',
                'Tu predicción se ha enviado al panel de administración y queda pendiente de revisión.',
                'apuesta'
            );

            return ['bet' => $bet];
        });

        if (isset($result['error'])) {
            return back()->withErrors(['amount' => $result['error']])->withInput();
        }

        return redirect()->route('prediction.index')->with('success', 'Predicción enviada. Queda pendiente de revisión por administración.');
    }
}
