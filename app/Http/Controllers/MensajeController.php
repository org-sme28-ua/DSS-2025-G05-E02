<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function getData(Request $request)
    {
        $query = Mensaje::query();
        
        if ($request->has('search') && $request->search) {
            $query->where('contenido', 'like', '%' . $request->search . '%')
                  ->orWhere('emisor', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('editado') && $request->editado !== '') {
            $query->where('editado', $request->editado === 'true');
        }
        
        $sort = $request->get('sort', 'id');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort, $dir);
        
        $perPage = $request->get('per', 6);
        $mensajes = $query->paginate($perPage);
        
        return response()->json($mensajes);
    }
    
    public function show($id)
    {
        $mensaje = Mensaje::findOrFail($id);
        return response()->json($mensaje);
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'chat' => 'required|string|max:255',
            'emisor' => 'required|string|max:255',
            'receptor' => 'required|string|max:255',
            'contenido' => 'required|string',
            'editado' => 'boolean',
        ]);
        
        $mensaje = Mensaje::create($data);
        
        return response()->json(['success' => true, 'data' => $mensaje, 'message' => 'Mensaje creado correctamente'], 201);
    }
    
    public function update(Request $request, $id)
    {
        $mensaje = Mensaje::findOrFail($id);
        
        $data = $request->validate([
            'chat' => 'sometimes|string|max:255',
            'emisor' => 'sometimes|string|max:255',
            'receptor' => 'sometimes|string|max:255',
            'contenido' => 'sometimes|string',
            'editado' => 'boolean',
        ]);
        
        $mensaje->update($data);
        
        return response()->json(['success' => true, 'data' => $mensaje, 'message' => 'Mensaje actualizado correctamente']);
    }
    
    public function destroy($id)
    {
        $mensaje = Mensaje::findOrFail($id);
        $mensaje->delete();
        
        return response()->json(['success' => true, 'message' => 'Mensaje eliminado correctamente']);
    }
}