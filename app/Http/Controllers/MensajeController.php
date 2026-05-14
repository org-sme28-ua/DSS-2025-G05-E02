<?php

namespace App\Http\Controllers;

use App\Models\Mensaje;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function listar(Request $request)
    {
        $query = Mensaje::query()->with(['chat', 'emisor', 'receptor']);

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($query) use ($search) {
                $query->where('contenido', 'like', $search)
                    ->orWhereHas('emisor', fn ($q) => $q->where('name', 'like', $search)->orWhere('email', 'like', $search))
                    ->orWhereHas('receptor', fn ($q) => $q->where('name', 'like', $search)->orWhere('email', 'like', $search));
            });
        }

        if ($request->filled('editado')) {
            $query->where('editado', $request->editado === 'true');
        }

        if ($request->filled('chat_id')) {
            $query->where('chat_id', $request->chat_id);
        }

        if ($request->filled('user_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('emisor_id', $request->user_id)->orWhere('receptor_id', $request->user_id);
            });
        }

        $allowedSorts = ['id', 'chat_id', 'emisor_id', 'receptor_id', 'created_at', 'updated_at'];
        $sort = in_array($request->get('sort'), $allowedSorts, true) ? $request->get('sort') : 'id';
        $dir = $request->get('dir') === 'desc' ? 'desc' : 'asc';

        $result = $query->orderBy($sort, $dir)->paginate((int) $request->get('per', 10));

        if ($request->expectsJson()) {
            return response()->json($result);
        }

        return $result;
    }

    public function getData(Request $request)
    {
        return $this->listar($request);
    }

    public function ver($id)
    {
        return response()->json(Mensaje::with(['chat', 'emisor', 'receptor'])->findOrFail($id));
    }

    public function show($id)
    {
        return $this->ver($id);
    }

    public function crear(Request $request)
    {
        $data = $request->validate([
            'chat_id' => ['required', 'exists:chats,id'],
            'emisor_id' => ['required', 'exists:users,id'],
            'receptor_id' => ['required', 'exists:users,id', 'different:emisor_id'],
            'contenido' => ['required', 'string'],
            'editado' => ['nullable', 'boolean'],
            'read_at' => ['nullable', 'date'],
        ]);

        $data['editado'] = (bool) ($data['editado'] ?? false);

        $mensaje = Mensaje::create($data);

        $mensaje->chat()->update([
            'last_message_at' => now(),
            'activo' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $mensaje,
                'message' => 'Mensaje creado correctamente',
            ], 201);
        }

        return back()->with('success', 'Mensaje creado correctamente.');
    }

    public function store(Request $request)
    {
        return $this->crear($request);
    }

    public function actualizar(Request $request, $id)
    {
        $mensaje = Mensaje::findOrFail($id);

        $data = $request->validate([
            'chat_id' => ['sometimes', 'exists:chats,id'],
            'emisor_id' => ['sometimes', 'exists:users,id'],
            'receptor_id' => ['sometimes', 'exists:users,id'],
            'contenido' => ['sometimes', 'string'],
            'editado' => ['sometimes', 'boolean'],
            'read_at' => ['sometimes', 'nullable', 'date'],
        ]);

        $mensaje->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $mensaje->fresh(),
                'message' => 'Mensaje actualizado correctamente',
            ]);
        }

        return back()->with('success', 'Mensaje actualizado correctamente.');
    }

    public function update(Request $request, $id)
    {
        return $this->actualizar($request, $id);
    }

    public function eliminar(Request $request, $id)
    {
        $mensaje = Mensaje::findOrFail($id);
        $mensaje->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mensaje eliminado correctamente',
            ]);
        }

        return back()->with('success', 'Mensaje eliminado correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        return $this->eliminar($request, $id);
    }
}
