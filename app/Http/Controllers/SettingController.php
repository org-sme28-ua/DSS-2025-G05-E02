<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getData(Request $request)
    {
        $query = Setting::query();
        
        if ($request->has('search') && $request->search) {
            $query->where('clave', 'like', '%' . $request->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('activo') && $request->activo !== '') {
            $query->where('activo', $request->activo === 'true');
        }
        
        $sort = $request->get('sort', 'id');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort, $dir);
        
        $perPage = $request->get('per', 6);
        $settings = $query->paginate($perPage);
        
        return response()->json($settings);
    }
    
    public function show($id)
    {
        $setting = Setting::findOrFail($id);
        return response()->json($setting);
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'clave' => 'required|string|unique:settings',
            'valor' => 'required|integer',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);
        
        $setting = Setting::create($data);
        
        return response()->json(['success' => true, 'data' => $setting, 'message' => 'Configuración creada correctamente'], 201);
    }
    
    public function update(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);
        
        $data = $request->validate([
            'clave' => 'required|string|unique:settings,clave,' . $id,
            'valor' => 'required|integer',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
        ]);
        
        $setting->update($data);
        
        return response()->json(['success' => true, 'data' => $setting, 'message' => 'Configuración actualizada correctamente']);
    }
    
    public function destroy($id)
    {
        $setting = Setting::findOrFail($id);
        $setting->delete();
        
        return response()->json(['success' => true, 'message' => 'Configuración eliminada correctamente']);
    }
}