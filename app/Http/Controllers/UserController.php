<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getData(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('role', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('nivel_vip')) {
            $query->where('nivel_vip', $request->nivel_vip);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $allowedSorts = ['id', 'name', 'email', 'role', 'puntos_fidelidad', 'nivel_vip', 'created_at'];
        $sort = in_array($request->get('sort'), $allowedSorts, true) ? $request->get('sort') : 'id';
        $dir = $request->get('dir') === 'desc' ? 'desc' : 'asc';

        return response()->json($query->orderBy($sort, $dir)->paginate((int) $request->get('per', 10)));
    }

    public function amigos(User $user)
    {
        return $user->amigos()->get();
    }

    public function agregarAmigo(Request $request, User $user)
    {
        $otroId = $request->validate(['friend_id' => 'required|exists:users,id'])['friend_id'];
        if ($user->amigos()->where('friend_id', $otroId)->exists()) {
            return response()->json(['message' => 'Ya son amigos.'], 409);
        }
        $user->amigos()->attach($otroId);
        return response()->json(['message' => 'Amigo agregado exitosamente.']);
    }

    public function quitarAmigo(Request $request, User $user)
    {
        $otroId = $request->validate(['friend_id' => 'required|exists:users,id'])['friend_id'];
        $user->amigos()->detach($otroId);
        return response()->json(['message' => 'Amigo eliminado exitosamente.']);
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
            'password' => ['required', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
            'puntos_fidelidad' => ['nullable', 'integer', 'min:0'],
            'nivel_vip' => ['nullable', 'integer', 'min:0'],
            'role' => ['nullable', 'in:admin,operator,player'],
        ], [
            'password.regex' => 'La contraseña debe incluir mayúscula, minúscula y número.',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['puntos_fidelidad'] = $data['puntos_fidelidad'] ?? 0;
        $data['nivel_vip'] = $data['nivel_vip'] ?? 0;
        $data['role'] = $data['role'] ?? 'player';

        $user = DB::transaction(function () use ($data) {
            $user = User::create($data);
            $user->billetera()->create(['saldoDisponible' => 0, 'moneda' => 'EUR']);
            return $user;
        });

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $user, 'message' => 'Usuario creado correctamente'], 201);
        }

        return back()->with('success', 'Usuario creado correctamente.');
    }

    public function actualizar(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/'],
            'puntos_fidelidad' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'nivel_vip' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'role' => ['sometimes', 'required', 'in:admin,operator,player'],
        ], [
            'password.regex' => 'La contraseña debe incluir mayúscula, minúscula y número.',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $user->fresh(), 'message' => 'Usuario actualizado correctamente']);
        }

        return back()->with('success', 'Usuario actualizado correctamente.');
    }

    public function eliminar(Request $request, User $user)
    {
        if ((int) auth()->id() === (int) $user->id) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'No puedes eliminar tu propio usuario desde el panel.'], 422);
            }
            return back()->with('error', 'No puedes eliminar tu propio usuario desde el panel.');
        }

        $user->delete();

        if ($request->expectsJson()) {
            return response()->json(null, 204);
        }

        return back()->with('success', 'Usuario eliminado correctamente.');
    }

    public function quitarAmistadAdmin($ids)
    {
        [$userId, $friendId] = explode('-', $ids);

        DB::table('user_user')
            ->where('user_id', $userId)
            ->where('friend_id', $friendId)
            ->delete();

        return back()->with('success', 'Vínculo de amistad eliminado correctamente.');
    }

    public function addFriendFront(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ], [
            'email.required' => 'Debes introducir un email.',
            'email.email' => 'El formato del email no es válido.'
        ]);

        $friend = User::where('email', $request->email)->first();
        $user = auth()->user();

        if (!$friend) {
            return back()->with('error', 'No se encontró ningún usuario con ese email.');
        }

        if ($friend->id === $user->id) {
            return back()->with('error', 'No puedes añadirte a ti mismo como amigo.');
        }

        if ($user->amigos()->where('friend_id', $friend->id)->exists()) {
            return back()->with('error', 'Este usuario ya está en tu lista de amigos.');
        }

        $user->amigos()->attach($friend->id);

        return back()->with('success', '¡Genial! ' . $friend->name . ' ha sido añadido a tus amigos.');
    }
}
