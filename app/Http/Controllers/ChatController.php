<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function listar()
    {
        return Chat::with(['mensajes', 'user'])->get();
    }

    public function ver(Chat $chat)
    {
        return $chat->load(['mensajes', 'user']);
    }

    public function crear(Request $request)
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'activo' => ['required', 'boolean'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        return Chat::create($data);
    }

    public function actualizar(Request $request, Chat $chat)
    {
        $data = $request->validate([
            'nombre' => ['sometimes', 'string', 'max:255'],
            'activo' => ['sometimes', 'boolean'],
            'user_id' => ['sometimes', 'integer', 'exists:users,id'],
        ]);

        $chat->update($data);

        return $chat;
    }

    public function eliminar(Chat $chat)
    {
        $chat->delete();

        return response()->json(null, 204);
    }
}
