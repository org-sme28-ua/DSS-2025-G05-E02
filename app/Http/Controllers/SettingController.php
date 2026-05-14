<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getData(Request $request)
    {
        $query = Setting::query();

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('clave', 'like', $search)
                    ->orWhere('valor', 'like', $search)
                    ->orWhere('descripcion', 'like', $search);
            });
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === 'true');
        }

        $allowedSorts = ['id', 'clave', 'valor', 'activo', 'created_at'];
        $sort = in_array($request->get('sort'), $allowedSorts, true) ? $request->get('sort') : 'id';
        $dir = $request->get('dir') === 'desc' ? 'desc' : 'asc';

        return response()->json($query->orderBy($sort, $dir)->paginate((int) $request->get('per', 10)));
    }

    public function show($id)
    {
        return response()->json(Setting::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'clave' => 'required|string|max:255|unique:settings,clave',
            'valor' => 'required|string|max:1000',
            'descripcion' => 'nullable|string',
            'activo' => 'nullable|boolean',
        ]);

        $data['activo'] = (bool) ($data['activo'] ?? false);
        $setting = Setting::create($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $setting, 'message' => 'Configuración creada correctamente'], 201);
        }

        return back()->with('success', 'Configuración creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);

        $data = $request->validate([
            'clave' => 'required|string|max:255|unique:settings,clave,' . $id,
            'valor' => 'required|string|max:1000',
            'descripcion' => 'nullable|string',
            'activo' => 'nullable|boolean',
        ]);

        $data['activo'] = (bool) ($data['activo'] ?? false);
        $setting->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $setting->fresh(), 'message' => 'Configuración actualizada correctamente']);
        }

        return back()->with('success', 'Configuración actualizada correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);
        $setting->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Configuración eliminada correctamente']);
        }

        return back()->with('success', 'Configuración eliminada correctamente.');
    }
}
