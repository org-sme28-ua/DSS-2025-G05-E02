<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Ranking;

class RankingController extends Controller
{
    public function listar()
    {
        return Ranking::with('user')->orderBy('posicion', 'asc')->get();
    }

    public function ver(Ranking $ranking)
    {
        return $ranking->load('user');
    }

    public function crear(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'posicion' => ['required', 'integer', 'min:1'],
            'puntos' => ['required', 'numeric', 'min:0'],
            'total_ganado' => ['required', 'numeric', 'min:0'],
        ]);

        return Ranking::create($data);
    }

    public function actualizar(Request $request, Ranking $ranking)
    {
        $data = $request->validate([
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'posicion' => ['sometimes', 'integer', 'min:1'],
            'puntos' => ['sometimes', 'numeric', 'min:0'],
            'total_ganado' => ['sometimes', 'numeric', 'min:0'],
        ]);

        $ranking->update($data);

        return $ranking;
    }

    public function eliminar(Ranking $ranking)
    {
        $ranking->delete();

        return response()->json(null, 204);
    }
}
