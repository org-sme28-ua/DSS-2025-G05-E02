<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function listar()
    {
        return Mensaje::with(['chat', 'emisor', 'receptor'])->get();
    }

    public function ver(Mensaje $mensaje)
    {
        return $mensaje->load(['chat', 'emisor', 'receptor']);
    }

    public function crear(Request $request)
    {
        $data = $request->validate([
            'chat_id' => ['required', 'integer', 'exists:chats,id'],
            'emisor_id' => ['required', 'integer', 'exists:users,id'],
            'receptor_id' => ['required', 'integer', 'exists:users,id'],
            'contenido' => ['required', 'string'],
            'fechaHora' => ['required', 'date'],
            'editado' => ['required', 'boolean'],
        ]);

        return Mensaje::create($data);
    }

    public function actualizar(Request $request, Mensaje $mensaje)
    {
        $data = $request->validate([
            'chat_id' => ['sometimes', 'integer', 'exists:chats,id'],
            'emisor_id' => ['sometimes', 'integer', 'exists:users,id'],
            'receptor_id' => ['sometimes', 'integer', 'exists:users,id'],
            'contenido' => ['sometimes', 'string'],
            'fechaHora' => ['sometimes', 'date'],
            'editado' => ['sometimes', 'boolean'],
        ]);

        $mensaje->update($data);

        return $mensaje;
    }

    public function eliminar(Mensaje $mensaje)
    {
        $mensaje->delete();

        return response()->json(null, 204);
    }
}
