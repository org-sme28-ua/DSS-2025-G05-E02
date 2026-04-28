<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function getData(Request $request)
    {
        $query = Chat::query();

        if ($request->has('search') && $request->search) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        if ($request->has('activo') && $request->activo !== '') {
            $query->where('activo', $request->activo === 'true');
        }

        $sort = $request->get('sort', 'id');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort, $dir);

        $perPage = $request->get('per', 6);
        return response()->json($query->paginate($perPage));
    }

    public function show($id)
    {
        return response()->json(Chat::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'fechaCreacion' => 'required|date',
            'activo' => 'boolean',
        ]);

        $chat = Chat::create($data);

        return response()->json([
            'success' => true,
            'data' => $chat,
            'message' => 'Chat creado correctamente'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $chat = Chat::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'fechaCreacion' => 'sometimes|date',
            'activo' => 'boolean',
        ]);

        $chat->update($data);

        return response()->json([
            'success' => true,
            'data' => $chat,
            'message' => 'Chat actualizado correctamente'
        ]);
    }

    public function destroy($id)
    {
        $chat = Chat::findOrFail($id);
        $chat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Chat eliminado correctamente'
        ]);
    }
}
