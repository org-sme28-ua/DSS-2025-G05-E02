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
            $search = '%' . $request->search . '%';
            $query->where('juego_id', 'like', $search)
                ->orWhereHas('juego', fn ($q) => $q->where('nombre', 'like', $search));
        }

        if ($request->filled('juego_id')) {
            $query->where('juego_id', $request->juego_id);
        }

        $allowedSorts = ['id', 'juego_id', 'multiplicacion_por_juego', 'bonus_por_racha', 'created_at'];
        $sort = in_array($request->get('sort'), $allowedSorts, true) ? $request->get('sort') : 'id';
        $dir = $request->get('dir') === 'desc' ? 'desc' : 'asc';

        return response()->json($query->orderBy($sort, $dir)->paginate((int) $request->get('per', 10)));
    }

    public function show($id)
    {
        return response()->json(ParametroGanancia::with('juego')->findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'juego_id' => 'required|exists:juegos,id',
            'multiplicacion_por_juego' => 'required|numeric|min:0',
            'bonus_por_racha' => 'required|numeric|min:0',
        ]);

        $parametro = ParametroGanancia::create($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $parametro, 'message' => 'Parámetro creado correctamente'], 201);
        }

        return back()->with('success', 'Parámetro creado correctamente.');
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

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $parametro->fresh(), 'message' => 'Parámetro actualizado correctamente']);
        }

        return back()->with('success', 'Parámetro actualizado correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $parametro = ParametroGanancia::findOrFail($id);
        $parametro->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Parámetro eliminado correctamente']);
        }

        return back()->with('success', 'Parámetro eliminado correctamente.');
    }
}
