<?php

namespace App\Http\Controllers;

use App\Models\Apuesta;
use Illuminate\Http\Request;

class ApuestaController extends Controller
{
    public function getData(Request $request)
    {
        // Traemos las relaciones para obtener los nombres en el frontend
        $query = Apuesta::with(['user', 'juego']);
        
        // Búsqueda en tablas relacionadas
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })->orWhereHas('juego', function($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->has('estado') && $request->estado !== '') {
            $query->where('estado', $request->estado);
        }
        
        $sort = $request->get('sort', 'id');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort, $dir);
        
        $perPage = $request->get('per', 6);
        $apuestas = $query->paginate($perPage);
        
        return response()->json($apuestas);
    }
    
    public function show($id)
    {
        $apuesta = Apuesta::findOrFail($id);
        return response()->json($apuesta);
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'juego_id' => 'required|exists:juegos,id',
            'monto' => 'required|numeric|min:0.01',
            'cuota' => 'required|numeric|min:1',
            'estado' => 'required|string|in:pendiente,ganada,perdida',
            'fecha' => 'required|date',
        ]);
        
        $apuesta = Apuesta::create($data);
        
        return response()->json(['success' => true, 'data' => $apuesta, 'message' => 'Apuesta creada correctamente'], 201);
    }
    
    public function update(Request $request, $id)
    {
        $apuesta = Apuesta::findOrFail($id);
        
        $data = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'juego_id' => 'sometimes|exists:juegos,id',
            'monto' => 'sometimes|numeric|min:0.01',
            'cuota' => 'sometimes|numeric|min:1',
            'estado' => 'sometimes|string|in:pendiente,ganada,perdida',
            'fecha' => 'sometimes|date',
        ]);
        
        $apuesta->update($data);
        
        return response()->json(['success' => true, 'data' => $apuesta, 'message' => 'Apuesta actualizada correctamente']);
    }
    
    public function destroy($id)
    {
        $apuesta = Apuesta::findOrFail($id);
        $apuesta->delete();
        
        return response()->json(['success' => true, 'message' => 'Apuesta eliminada correctamente']);
    }
}