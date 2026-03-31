<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function getData(Request $request)
    {
        $query = Ranking::query();
        
        if ($request->has('search') && $request->search) {
            $query->where('usuario', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('activo') && $request->activo !== '') {
            $query->where('activo', $request->activo === 'true');
        }
        
        $sort = $request->get('sort', 'posicion');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort, $dir);
        
        $perPage = $request->get('per', 6);
        $rankings = $query->paginate($perPage);
        
        return response()->json($rankings);
    }
    
    public function show($id)
    {
        $ranking = Ranking::findOrFail($id);
        return response()->json($ranking);
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'usuario' => 'required|string|max:255|unique:rankings',
            'posicion' => 'required|integer|min:1',
            'puntos' => 'required|integer|min:0',
            'totalGanado' => 'required|numeric|min:0',
            'activo' => 'boolean',
        ]);
        
        $ranking = Ranking::create($data);
        
        return response()->json(['success' => true, 'data' => $ranking, 'message' => 'Ranking creado correctamente'], 201);
    }
    
    public function update(Request $request, $id)
    {
        $ranking = Ranking::findOrFail($id);
        
        $data = $request->validate([
            'usuario' => 'sometimes|string|max:255|unique:rankings,usuario,' . $id,
            'posicion' => 'sometimes|integer|min:1',
            'puntos' => 'sometimes|integer|min:0',
            'totalGanado' => 'sometimes|numeric|min:0',
            'activo' => 'boolean',
        ]);
        
        $ranking->update($data);
        
        return response()->json(['success' => true, 'data' => $ranking, 'message' => 'Ranking actualizado correctamente']);
    }
    
    public function destroy($id)
    {
        $ranking = Ranking::findOrFail($id);
        $ranking->delete();
        
        return response()->json(['success' => true, 'message' => 'Ranking eliminado correctamente']);
    }
}