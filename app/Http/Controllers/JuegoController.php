<?php

namespace App\Http\Controllers;

use App\Models\Juego;
use Illuminate\Http\Request;

class JuegoController extends Controller
{
    public function getData(Request $request)
    {
        $query = Juego::query();

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', $search)
                    ->orWhere('categoria', 'like', $search)
                    ->orWhere('estado', 'like', $search);
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        $allowedSorts = ['id', 'nombre', 'categoria', 'estado', 'created_at'];
        $sort = in_array($request->get('sort'), $allowedSorts, true) ? $request->get('sort') : 'id';
        $dir = $request->get('dir') === 'desc' ? 'desc' : 'asc';

        return response()->json($query->orderBy($sort, $dir)->paginate((int) $request->get('per', 10)));
    }

    public function show($id)
    {
        return response()->json(Juego::with('apuestas')->findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria' => 'required|string|max:255',
            'estado' => 'required|string|max:255',
        ]);

        $juego = Juego::create($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $juego, 'message' => 'Juego creado correctamente'], 201);
        }

        return back()->with('success', 'Juego creado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $juego = Juego::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'categoria' => 'sometimes|required|string|max:255',
            'estado' => 'sometimes|required|string|max:255',
        ]);

        $juego->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $juego->fresh(), 'message' => 'Juego actualizado correctamente']);
        }

        return back()->with('success', 'Juego actualizado correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $juego = Juego::findOrFail($id);

        if ($juego->apuestas()->exists()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No se puede eliminar un juego con apuestas asociadas.'], 422);
            }
            return back()->with('error', 'No se puede eliminar un juego con apuestas asociadas.');
        }

        $juego->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Juego eliminado correctamente']);
        }

        return back()->with('success', 'Juego eliminado correctamente.');
    }
}
