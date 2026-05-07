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
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('contenido', 'like', '%' . $search . '%')
                    ->orWhereHas('emisor', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('receptor', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->has('editado') && $request->editado !== '') {
            $query->where('editado', $request->editado === 'true');
        }

        $sort = in_array($request->get('sort'), ['id', 'chat_id', 'emisor_id', 'receptor_id', 'created_at', 'updated_at'], true)
            ? $request->get('sort')
            : 'id';

        $dir = $request->get('dir') === 'desc' ? 'desc' : 'asc';
        $perPage = (int) $request->get('per', 6);

        return response()->json($query->orderBy($sort, $dir)->paginate($perPage));
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

        $data['editado'] = $data['editado'] ?? false;

        $mensaje = Mensaje::create($data);

        $mensaje->chat()->update([
            'last_message_at' => now(),
            'activo' => true,
        ]);

        return response()->json([
            'success' => true,
            'data' => $mensaje,
            'message' => 'Mensaje creado correctamente',
        ], 201);
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

        return response()->json([
            'success' => true,
            'data' => $mensaje,
            'message' => 'Mensaje actualizado correctamente',
        ]);
    }

    public function update(Request $request, $id)
    {
        return $this->actualizar($request, $id);
    }

    public function eliminar($id)
    {
        $mensaje = Mensaje::findOrFail($id);
        $mensaje->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mensaje eliminado correctamente',
        ]);
    }

    public function destroy($id)
    {
        return $this->eliminar($id);
    }
}
