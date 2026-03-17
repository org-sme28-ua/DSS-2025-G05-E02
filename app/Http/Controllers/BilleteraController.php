<?php

namespace App\Http\Controllers;

use App\Models\Billetera;
use Illuminate\Http\Request;

class BilleteraController extends Controller
{
    public function listar()
    {
        return Billetera::with('user')->get();
    }

    public function ver(Billetera $billetera)
    {
        return $billetera->load('user');
    }

    public function crear(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'saldoDisponible' => ['required', 'numeric', 'min:0'],
            'moneda' => ['required', 'string', 'max:10'],
        ]);

        return Billetera::create($data);
    }

    public function actualizar(Request $request, Billetera $billetera)
    {
        $data = $request->validate([
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'saldoDisponible' => ['sometimes', 'numeric', 'min:0'],
            'moneda' => ['sometimes', 'string', 'max:10'],
        ]);

        $billetera->update($data);

        return $billetera;
    }

    public function eliminar(Billetera $billetera)
    {
        $billetera->delete();

        return response()->json(null, 204);
    }
}
