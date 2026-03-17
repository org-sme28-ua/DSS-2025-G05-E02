<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function listar()
    {
        return User::with(['billetera', 'apuestas', 'chats', 'mensajesEnviados', 'mensajesRecibidos'])->get();
    }

    public function ver(User $user)
    {
        return $user->load(['billetera', 'apuestas', 'chats', 'mensajesEnviados', 'mensajesRecibidos']);
    }

    public function crear(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'puntos_fidelidad' => ['sometimes', 'integer', 'min:0'],
            'nivel_vip' => ['sometimes', 'integer', 'min:0'],
        ]);

        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    public function actualizar(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['sometimes', 'string', 'min:6'],
            'puntos_fidelidad' => ['sometimes', 'integer', 'min:0'],
            'nivel_vip' => ['sometimes', 'integer', 'min:0'],
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $user;
    }

    public function eliminar(User $user)
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
