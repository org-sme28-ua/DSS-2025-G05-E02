<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function getData(Request $request)
    {
        $query = Notificacion::query();
        
        if ($request->has('search') && $request->search) {
            $query->where('titulo', 'like', '%' . $request->search . '%')
                  ->orWhere('usuario', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('tipo') && $request->tipo !== '') {
            $query->where('tipo', $request->tipo);
        }
        
        if ($request->has('leido') && $request->leido !== '') {
            $query->where('leido', $request->leido === 'true');
        }
        
        $sort = $request->get('sort', 'id');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort, $dir);
        
        $perPage = $request->get('per', 6);
        $notificaciones = $query->paginate($perPage);
        
        return response()->json($notificaciones);
    }
    
    public function show($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        return response()->json($notificacion);
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'usuario' => 'required|string|max:255',
            'tipo' => 'required|string|in:apuesta,promo,alerta',
            'titulo' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'leido' => 'boolean',
            'fechaHora' => 'required|date',
        ]);
        
        $notificacion = Notificacion::create($data);
        
        return response()->json(['success' => true, 'data' => $notificacion, 'message' => 'Notificación creada correctamente'], 201);
    }
    
    public function update(Request $request, $id)
    {
        $notificacion = Notificacion::findOrFail($id);
        
        $data = $request->validate([
            'usuario' => 'sometimes|string|max:255',
            'tipo' => 'sometimes|string|in:apuesta,promo,alerta',
            'titulo' => 'sometimes|string|max:255',
            'desc' => 'nullable|string',
            'leido' => 'boolean',
            'fechaHora' => 'sometimes|date',
        ]);
        
        $notificacion->update($data);
        
        return response()->json(['success' => true, 'data' => $notificacion, 'message' => 'Notificación actualizada correctamente']);
    }
    
    public function destroy($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->delete();
        
        return response()->json(['success' => true, 'message' => 'Notificación eliminada correctamente']);
    }
}