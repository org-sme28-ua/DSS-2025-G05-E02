<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Notificacion;

class NotificacionController extends Controller
{
    public function listar()
    {
        return Notificacion::with('user')->get();
    }

    public function ver(Notificacion $notificacion)
    {
        return $notificacion->load('user');
    }

    public function crear(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'tipo' => ['required', 'in:apuesta,promo,alerta,chat'],
            'titulo' => ['required', 'string', 'max:255'],
            'mensaje' => ['required', 'string'],
            'leido' => ['sometimes', 'boolean'],
            'fecha' => ['sometimes', 'date'],
        ]);

        return Notificacion::create($data);
    }

    public function actualizar(Request $request, Notificacion $notificacion)
    {
        $data = $request->validate([
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
            'tipo' => ['sometimes', 'in:apuesta,promo,alerta,chat'],
            'titulo' => ['sometimes', 'string', 'max:255'],
            'mensaje' => ['sometimes', 'string'],
            'leido' => ['sometimes', 'boolean'],
            'fecha' => ['sometimes', 'date'],
        ]);

        $notificacion->update($data);

        return $notificacion;
    }

    public function eliminar(Notificacion $notificacion)
    {
        $notificacion->delete();

        return response()->json(null, 204);
    }
}
