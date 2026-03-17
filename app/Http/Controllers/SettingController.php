<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Setting;

class SettingController extends Controller
{
    public function listar()
    {
        return Setting::where('activo', true)->get();
    }

    public function ver(Setting $setting)
    {
        return $setting;
    }

    public function crear(Request $request)
    {
        $data = $request->validate([
            'clave' => ['required', 'string', 'max:100', 'unique:settings,clave'],
            'valor' => ['required', 'string'],
            'descripcion' => ['sometimes', 'string'],
            'activo' => ['sometimes', 'boolean'],
        ]);

        return Setting::create($data);
    }

    public function actualizar(Request $request, Setting $setting)
    {
        $data = $request->validate([
            'clave' => ['sometimes', 'string', 'max:100', 'unique:settings,clave,'.$setting->id],
            'valor' => ['sometimes', 'string'],
            'descripcion' => ['sometimes', 'string'],
            'activo' => ['sometimes', 'boolean'],
        ]);

        $setting->update($data);

        return $setting;
    }

    public function eliminar(Setting $setting)
    {
        $setting->delete();

        return response()->json(null, 204);
    }
}
