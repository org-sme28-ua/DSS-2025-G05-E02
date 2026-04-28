<?php

namespace App\Http\Controllers;

use App\Models\Juego;
use Illuminate\Http\Request;

class JuegoController extends Controller
{
    public function getData(Request $request)
    {
        $query = Juego::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('categoria', 'like', '%' . $search . '%');
        }

        if ($request->has('estado') && $request->estado !== '') {
            $query->where('estado', $request->estado);
        }

        $sort = $request->get('sort', 'id');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort, $dir);

        $perPage = $request->get('per', 6);
        return response()->json($query->paginate($perPage));
    }

    public function show($id)
    {
        return response()->json(Juego::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
        ]);

        $juego = Juego::create($data);

        return response()->json([
            'success' => true,
            'data' => $juego,
            'message' => 'Juego creado correctamente'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $juego = Juego::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'categoria' => 'sometimes|string|max:255',
            'estado' => 'sometimes|string|max:255',
        ]);

        $juego->update($data);

        return response()->json([
            'success' => true,
            'data' => $juego,
            'message' => 'Juego actualizado correctamente'
        ]);
    }

    public function destroy($id)
    {
        $juego = Juego::findOrFail($id);
        $juego->delete();

        return response()->json([
            'success' => true,
            'message' => 'Juego eliminado correctamente'
        ]);
    }
}
