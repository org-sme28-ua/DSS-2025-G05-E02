<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getData(Request $request)
    {
        $query = User::query();
        
        // Búsqueda por nombre o email
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        
        // Filtro por Nivel VIP
        if ($request->has('nivel_vip') && $request->nivel_vip !== '') {
            $query->where('nivel_vip', $request->nivel_vip);
        }
        
        // Ordenamiento
        $sort = $request->get('sort', 'id');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort, $dir);
        
        // Paginación
        $perPage = $request->get('per', 6);
        $usuarios = $query->paginate($perPage);
        
        return response()->json($usuarios);
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
        try {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:6'],
                'puntos_fidelidad' => ['sometimes', 'integer', 'min:0'],
                'nivel_vip' => ['sometimes', 'integer', 'min:0'],
            ]);

            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            // Creamos la billetera básica asociada
            $user->billetera()->create(['saldoDisponible' => 0, 'moneda' => 'EUR']); 

            return response()->json([
                'success' => true, 
                'data' => $user, 
                'message' => 'Usuario creado correctamente'
            ], 201);

        } catch (\Exception $e) {
            // Esto capturará cualquier error fatal y te lo enviará al frontend
            return response()->json([
                'success' => false, 
                'message' => 'Error del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function actualizar(Request $request, User $user)
    {
        try {
            $data = $request->validate([
                'name' => ['sometimes', 'string', 'max:255'],
                'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
                'password' => ['nullable', 'string', 'min:6'],
                'puntos_fidelidad' => ['sometimes', 'integer', 'min:0'],
                'nivel_vip' => ['sometimes', 'integer', 'min:0'],
            ]);

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            return response()->json([
                'success' => true, 
                'data' => $user, 
                'message' => 'Usuario actualizado correctamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function eliminar(User $user)
    {
        $user->delete();

        return response()->json(null, 204);
    }

    public function quitarAmistadAdmin($ids)
    {
        // Separa los dos IDs que vienen en la URL (ejemplo: 4-7)
        list($userId, $friendId) = explode('-', $ids);
        
        \DB::table('user_user')
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

        // Validaciones de seguridad
        if (!$friend) {
            return back()->with('error', 'No se encontró ningún usuario con ese email.');
        }

        if ($friend->id === $user->id) {
            return back()->with('error', 'No puedes añadirte a ti mismo como amigo.');
        }

        if ($user->amigos()->where('friend_id', $friend->id)->exists()) {
            return back()->with('error', 'Este usuario ya está en tu lista de amigos.');
        }

        // Crear la relación (vínculo)
        $user->amigos()->attach($friend->id);

        return back()->with('success', '¡Genial! ' . $friend->name . ' ha sido añadido a tus amigos.');
    }
}
