<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function getData(Request $request)
    {
        $query = Ranking::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('user_id', 'like', "%{$search}%");
        }

        $sort = $request->get('sort', 'id');
        $dir = $request->get('dir', 'asc');
        $per = (int) $request->get('per', 6);

        return response()->json(
            $query->orderBy($sort, $dir)->paginate($per)
        );
    }

    public function show($id)
    {
        return response()->json(Ranking::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'posicion' => 'required|integer|min:1',
            'puntos' => 'required|numeric|min:0',
            'total_ganado' => 'required|numeric|min:0',
        ]);

        $ranking = Ranking::create($data);

        return response()->json([
            'success' => true,
            'data' => $ranking,
            'message' => 'Ranking creado correctamente'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $ranking = Ranking::findOrFail($id);

        $data = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'posicion' => 'sometimes|integer|min:1',
            'puntos' => 'sometimes|numeric|min:0',
            'total_ganado' => 'sometimes|numeric|min:0',
        ]);

        $ranking->update($data);

        return response()->json([
            'success' => true,
            'data' => $ranking,
            'message' => 'Ranking actualizado correctamente'
        ]);
    }

    public function destroy($id)
    {
        $ranking = Ranking::findOrFail($id);
        $ranking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ranking eliminado correctamente'
        ]);
    }
}
