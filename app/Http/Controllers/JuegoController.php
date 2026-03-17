<?php

namespace App\Http\Controllers;

use App\Models\Juego;
use Illuminate\Http\Request;

class JuegoController extends Controller
{
    public function listar()
    {
        return Juego::with('apuestas')->get();
    }

    public function ver(Juego $juego)
    {
        return $juego->load('apuestas');
    }

    public function crear(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'categoria' => ['required', 'string', 'max:100'],
            'estado' => ['required', 'string', 'in:activo,inactivo,pausado'],
        ]);

        return Juego::create($data);
    }

    public function actualizar(Request $request, Juego $juego)
    {
        $data = $request->validate([
            'nombre' => ['sometimes', 'string', 'max:255'],
            'categoria' => ['sometimes', 'string', 'max:100'],
            'estado' => ['sometimes', 'string', 'in:activo,inactivo,pausado'],
        ]);

        $juego->update($data);

        return $juego;
    }

    public function eliminar(Juego $juego)
    {
        $juego->delete();

        return response()->json(null, 204);
    }
}
