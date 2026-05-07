<?php

namespace App\Http\Controllers;

use App\Models\Apuesta;
use Illuminate\Http\Request;

class ApuestaController extends Controller
{
    public function getData(Request $request)
    {
        $query = Apuesta::with(['user', 'juego', 'admin']);

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'like', $search)
                    ->orWhere('seleccion', 'like', $search)
                    ->orWhere('resultado', 'like', $search)
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', $search)->orWhere('email', 'like', $search);
                    })
                    ->orWhereHas('juego', function ($jq) use ($search) {
                        $jq->where('nombre', 'like', $search);
                    });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('juego_id')) {
            $query->where('juego_id', $request->juego_id);
        }

        $allowedSorts = ['id', 'user_id', 'juego_id', 'tipo', 'monto', 'cuota', 'estado', 'fecha', 'created_at'];
        $sort = in_array($request->get('sort'), $allowedSorts, true) ? $request->get('sort') : 'id';
        $dir = $request->get('dir') === 'desc' ? 'desc' : 'asc';

        $query->orderBy($sort, $dir);

        return response()->json($query->paginate((int) $request->get('per', 10)));
    }

    public function show($id)
    {
        return response()->json(Apuesta::with(['user', 'juego', 'admin'])->findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'juego_id' => 'required|exists:juegos,id',
            'tipo' => 'nullable|string|max:40',
            'descripcion' => 'nullable|string|max:1000',
            'seleccion' => 'nullable|string|max:1000',
            'resultado' => 'nullable|string|max:1000',
            'monto' => 'required|numeric|min:0.01',
            'cuota' => 'required|numeric|min:1',
            'estado' => 'required|string|in:pendiente,aceptada,rechazada,ganada,perdida',
            'fecha' => 'required|date',
            'balance_antes' => 'nullable|numeric',
            'balance_despues' => 'nullable|numeric',
            'resuelta_at' => 'nullable|date',
            'admin_id' => 'nullable|exists:users,id',
        ]);

        $data['tipo'] = $data['tipo'] ?? 'general';

        $apuesta = Apuesta::create($data);

        return response()->json(['success' => true, 'data' => $apuesta, 'message' => 'Apuesta creada correctamente'], 201);
    }

    public function update(Request $request, $id)
    {
        $apuesta = Apuesta::findOrFail($id);

        $data = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'juego_id' => 'sometimes|exists:juegos,id',
            'tipo' => 'sometimes|nullable|string|max:40',
            'descripcion' => 'sometimes|nullable|string|max:1000',
            'seleccion' => 'sometimes|nullable|string|max:1000',
            'resultado' => 'sometimes|nullable|string|max:1000',
            'monto' => 'sometimes|numeric|min:0.01',
            'cuota' => 'sometimes|numeric|min:1',
            'estado' => 'sometimes|string|in:pendiente,aceptada,rechazada,ganada,perdida',
            'fecha' => 'sometimes|date',
            'balance_antes' => 'sometimes|nullable|numeric',
            'balance_despues' => 'sometimes|nullable|numeric',
            'resuelta_at' => 'sometimes|nullable|date',
            'admin_id' => 'sometimes|nullable|exists:users,id',
        ]);

        $apuesta->update($data);

        return response()->json(['success' => true, 'data' => $apuesta, 'message' => 'Apuesta actualizada correctamente']);
    }

    public function destroy($id)
    {
        Apuesta::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Apuesta eliminada correctamente']);
    }
}
