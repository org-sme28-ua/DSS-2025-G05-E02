<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function getData(Request $request)
    {
        // Cargamos la relación del usuario
        $query = Notificacion::with('user');
        
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('titulo', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
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
            'user_id' => 'required|exists:users,id',
            'tipo' => 'required|string|in:apuesta,promo,alerta,info,mensaje',
            'titulo' => 'required|string|max:255',
            'mensaje' => 'nullable|string',
            'leido' => 'boolean',
            'fecha' => 'required|date',
        ]);
        
        $notificacion = Notificacion::create($data);
        
        return response()->json(['success' => true, 'data' => $notificacion, 'message' => 'Notificación creada correctamente'], 201);
    }
    
    public function update(Request $request, $id)
    {
        $notificacion = Notificacion::findOrFail($id);
        
        $data = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'tipo' => 'sometimes|string|in:apuesta,promo,alerta,info,mensaje',
            'titulo' => 'sometimes|string|max:255',
            'mensaje' => 'nullable|string',
            'leido' => 'boolean',
            'fecha' => 'sometimes|date',
        ]);
        
        $notificacion->update($data);
        
        return response()->json(['success' => true, 'data' => $notificacion, 'message' => 'Notificación actualizada']);
    }
    
    public function destroy($id)
    {
        try {
            $notificacion = Notificacion::findOrFail($id);
            $notificacion->delete();
            return response()->json(['success' => true, 'message' => 'Notificación eliminada']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
        }
    }
}