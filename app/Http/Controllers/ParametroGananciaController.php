<?php

namespace App\Http\Controllers;

use App\Models\ParametroGanancia;
use Illuminate\Http\Request;

class ParametroGananciaController extends Controller
{
    public function getData(Request $request)
    {
        $query = ParametroGanancia::with('juego');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where('juego_id', 'like', "%{$search}%")
                ->orWhereHas('juego', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                });
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
        return response()->json(ParametroGanancia::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'juego_id' => 'required|exists:juegos,id',
            'multiplicacion_por_juego' => 'required|numeric|min:0',
            'bonus_por_racha' => 'required|numeric|min:0',
        ]);

        $parametro = ParametroGanancia::create($data);

        return response()->json([
            'success' => true,
            'data' => $parametro,
            'message' => 'Parámetro creado correctamente'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $parametro = ParametroGanancia::findOrFail($id);

        $data = $request->validate([
            'juego_id' => 'sometimes|exists:juegos,id',
            'multiplicacion_por_juego' => 'sometimes|numeric|min:0',
            'bonus_por_racha' => 'sometimes|numeric|min:0',
        ]);

        $parametro->update($data);

        return response()->json([
            'success' => true,
            'data' => $parametro,
            'message' => 'Parámetro actualizado correctamente'
        ]);
    }

    public function destroy($id)
    {
        $parametro = ParametroGanancia::findOrFail($id);
        $parametro->delete();

        return response()->json([
            'success' => true,
            'message' => 'Parámetro eliminado correctamente'
        ]);
    }
}
