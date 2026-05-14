<?php

namespace App\Http\Controllers;

use App\Models\Billetera;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BilleteraController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $billetera = Billetera::firstOrCreate(
            ['user_id' => $user->id],
            ['saldoDisponible' => 0, 'moneda' => 'EUR']
        );

        $apuestas = $user->apuestas()
            ->with('juego')
            ->latest('fecha')
            ->take(8)
            ->get();

        return view('billetera', [
            'billetera' => $billetera,
            'apuestas' => $apuestas,
            'totalApuestas' => $user->apuestas()->count(),
            'apuestasPendientes' => $user->apuestas()->whereIn('estado', ['pendiente', 'aceptada'])->count(),
            'apuestasGanadas' => $user->apuestas()->where('estado', 'ganada')->count(),
        ]);
    }

    public function deposit(Request $request): RedirectResponse
    {
        $card = $this->validateCard($request);

        if ($card instanceof RedirectResponse) {
            return $card;
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:5', 'max:10000'],
        ], [
            'amount.required' => 'Indica la cantidad a ingresar.',
            'amount.numeric' => 'La cantidad debe ser un número.',
            'amount.min' => 'El ingreso mínimo es de 5 EUR.',
            'amount.max' => 'El ingreso máximo por operación es de 10.000 EUR.',
        ]);

        $amount = round((float) $data['amount'], 2);
        $newBalance = 0.0;

        DB::transaction(function () use ($amount, &$newBalance) {
            $user = auth()->user();
            $wallet = Billetera::firstOrCreate(
                ['user_id' => $user->id],
                ['saldoDisponible' => 0, 'moneda' => 'EUR']
            );

            $wallet = Billetera::whereKey($wallet->id)->lockForUpdate()->firstOrFail();
            $wallet->saldoDisponible = round((float) $wallet->saldoDisponible + $amount, 2);
            $wallet->save();

            $newBalance = (float) $wallet->saldoDisponible;

            Notificacion::crearNotificacion(
                $user->id,
                'Ingreso realizado',
                'Se han añadido ' . number_format($amount, 2, ',', '.') . ' EUR a tu billetera.',
                'sistema'
            );
        });

        return redirect()
            ->route('billetera')
            ->with('success', 'Ingreso realizado. Nuevo saldo: ' . number_format($newBalance, 2, ',', '.') . ' EUR.');
    }

    public function withdraw(Request $request): RedirectResponse
    {
        $card = $this->validateCard($request);

        if ($card instanceof RedirectResponse) {
            return $card;
        }

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:10000'],
        ], [
            'amount.required' => 'Indica la cantidad a retirar.',
            'amount.numeric' => 'La cantidad debe ser un número.',
            'amount.min' => 'La retirada mínima es de 1 EUR.',
            'amount.max' => 'La retirada máxima por operación es de 10.000 EUR.',
        ]);

        $amount = round((float) $data['amount'], 2);
        $newBalance = 0.0;

        try {
            DB::transaction(function () use ($amount, &$newBalance) {
                $user = auth()->user();
                $wallet = Billetera::firstOrCreate(
                    ['user_id' => $user->id],
                    ['saldoDisponible' => 0, 'moneda' => 'EUR']
                );

                $wallet = Billetera::whereKey($wallet->id)->lockForUpdate()->firstOrFail();
                $currentBalance = (float) $wallet->saldoDisponible;

                if ($currentBalance < $amount) {
                    throw new \RuntimeException('Saldo insuficiente para retirar esa cantidad.');
                }

                $wallet->saldoDisponible = round($currentBalance - $amount, 2);
                $wallet->save();

                $newBalance = (float) $wallet->saldoDisponible;

                Notificacion::crearNotificacion(
                    $user->id,
                    'Retirada realizada',
                    'Se han retirado ' . number_format($amount, 2, ',', '.') . ' EUR de tu billetera.',
                    'sistema'
                );
            });
        } catch (\Throwable $e) {
            return back()
                ->withErrors(['amount' => $e->getMessage()])
                ->withInput();
        }

        return redirect()
            ->route('billetera')
            ->with('success', 'Retirada realizada. Nuevo saldo: ' . number_format($newBalance, 2, ',', '.') . ' EUR.');
    }

    private function validateCard(Request $request): array|RedirectResponse
    {
        $data = $request->validate([
            'card_holder' => ['required', 'string', 'min:3', 'max:80', 'regex:/^[\pL\s\.\'\-]+$/u'],
            'card_number' => ['required', 'string', 'max:25'],
            'card_expiry' => ['required', 'string', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'card_cvc' => ['required', 'string', 'regex:/^\d{3,4}$/'],
        ], [
            'card_holder.required' => 'Indica el titular de la tarjeta.',
            'card_holder.min' => 'El titular debe tener al menos 3 caracteres.',
            'card_holder.regex' => 'El titular solo puede contener letras, espacios, puntos, apóstrofes o guiones.',
            'card_number.required' => 'Indica el número de tarjeta.',
            'card_number.max' => 'El número de tarjeta es demasiado largo.',
            'card_expiry.required' => 'Indica la caducidad en formato MM/AA.',
            'card_expiry.regex' => 'La caducidad debe tener formato MM/AA.',
            'card_cvc.required' => 'Indica el CVC.',
            'card_cvc.regex' => 'El CVC debe tener 3 o 4 números.',
        ]);

        $digits = preg_replace('/\D+/', '', $data['card_number']);

        if (strlen($digits) !== 16) {
            return back()
                ->withErrors(['card_number' => 'El número de tarjeta debe tener exactamente 16 dígitos.'])
                ->withInput();
        }

        [$month, $year] = explode('/', $data['card_expiry']);
        $expiryYear = 2000 + (int) $year;
        $expiryMonth = (int) $month;
        $expiryDate = now()->setDate($expiryYear, $expiryMonth, 1)->endOfMonth()->endOfDay();

        if ($expiryDate->isPast()) {
            return back()
                ->withErrors(['card_expiry' => 'La tarjeta no puede estar caducada.'])
                ->withInput();
        }

        return array_merge($data, ['card_number_digits' => $digits]);
    }


    public function obtenerBilleteraBloqueada(User $user): Billetera
    {
        $billetera = Billetera::firstOrCreate(
            ['user_id' => $user->id],
            ['saldoDisponible' => 0, 'moneda' => 'EUR']
        );

        return Billetera::whereKey($billetera->id)->lockForUpdate()->firstOrFail();
    }

    public function comprobarSaldoDisponible(Billetera $billetera, int $montoCentimos): void
    {
        $saldoCentimos = (int) round(((float) $billetera->saldoDisponible) * 100);

        if ($saldoCentimos < $montoCentimos) {
            throw new \RuntimeException('Saldo insuficiente para hacer esa apuesta.');
        }
    }

    public function actualizarSaldoDesdeCentimos(Billetera $billetera, int $nuevoSaldoCentimos): Billetera
    {
        $billetera->saldoDisponible = $nuevoSaldoCentimos / 100;
        $billetera->save();

        return $billetera->refresh();
    }

    public function getData(Request $request)
    {
        $query = Billetera::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('id', 'like', '%' . $search . '%')
                  ->orWhere('moneda', 'like', '%' . $search . '%');
        }

        if ($request->has('moneda') && $request->moneda !== '') {
            $query->where('moneda', $request->moneda);
        }

        $sort = $request->get('sort', 'id');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort, $dir);

        $perPage = $request->get('per', 6);
        return response()->json($query->paginate($perPage));
    }

    public function show($id)
    {
        return response()->json(Billetera::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id|unique:billeteras,user_id',
            'saldoDisponible' => 'required|numeric|min:0',
            'moneda' => 'required|string|max:10',
        ]);

        $billetera = Billetera::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $billetera,
                'message' => 'Billetera creada correctamente'
            ], 201);
        }

        return back()->with('success', 'Billetera creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $billetera = Billetera::findOrFail($id);

        $data = $request->validate([
            'user_id' => 'sometimes|exists:users,id|unique:billeteras,user_id,' . $billetera->id,
            'saldoDisponible' => 'sometimes|numeric|min:0',
            'moneda' => 'sometimes|string|max:10',
        ]);

        $billetera->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $billetera->fresh(),
                'message' => 'Billetera actualizada correctamente'
            ]);
        }

        return back()->with('success', 'Billetera actualizada correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $billetera = Billetera::findOrFail($id);
        $billetera->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Billetera eliminada correctamente'
            ]);
        }

        return back()->with('success', 'Billetera eliminada correctamente.');
    }
}
