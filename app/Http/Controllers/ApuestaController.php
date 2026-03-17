<?php

namespace App\Http\Controllers;

use App\Models\Apuesta;
use Illuminate\Http\Request;

class ApuestaController extends Controller
{
    public function listar()
    {
        return Apuesta::with(['user', 'juego'])->get();
    }

    public function ver(Apuesta $apuesta)
    {
        return $apuesta->load(['user', 'juego']);
    }

    public function crear(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'juego_id' => ['required', 'integer', 'exists:juegos,id'],
            'monto' => ['required', 'numeric', 'min:0'],
            'cuota' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'string', 'max:50'],
            'fecha' => ['required', 'date'],
        ]);

        return Apuesta::create($data);
    }

    public function actualizar(Request $request, Apuesta $apuesta)
    {
        $data = $request->validate([
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'juego_id' => ['sometimes', 'integer', 'exists:juegos,id'],
            'monto' => ['sometimes', 'numeric', 'min:0'],
            'cuota' => ['sometimes', 'numeric', 'min:0'],
            'estado' => ['sometimes', 'string', 'max:50'],
            'fecha' => ['sometimes', 'date'],
        ]);

        $apuesta->update($data);

        return $apuesta;
    }

    public function eliminar(Apuesta $apuesta)
    {
        $apuesta->delete();

        return response()->json(null, 204);
    }
}
