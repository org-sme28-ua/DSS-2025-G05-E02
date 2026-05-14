<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $filter = $request->query('estado', 'todas');

        $baseQuery = $user->notificaciones();

        $notificaciones = (clone $baseQuery)
            ->when($filter === 'sin-leer', fn ($query) => $query->where('leido', false))
            ->when($filter === 'leidas', fn ($query) => $query->where('leido', true))
            ->latest('fecha')
            ->get();

        return view('notificaciones', [
            'notificaciones' => $notificaciones,
            'totalNotificaciones' => (clone $baseQuery)->count(),
            'notificacionesSinLeer' => (clone $baseQuery)->where('leido', false)->count(),
            'notificacionesLeidas' => (clone $baseQuery)->where('leido', true)->count(),
            'filter' => $filter,
        ]);
    }

    public function markAsRead(Request $request, Notificacion $notificacion)
    {
        $this->authorizeNotification($request, $notificacion);

        $notificacion->update(['leido' => true]);

        return redirect()
            ->route('private.notificaciones')
            ->with('success', 'Notificacion marcada como leida.');
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->notificaciones()
            ->where('leido', false)
            ->update(['leido' => true]);

        return redirect()
            ->route('private.notificaciones')
            ->with('success', 'Todas las notificaciones se han marcado como leidas.');
    }

    public function destroyOwn(Request $request, Notificacion $notificacion)
    {
        $this->authorizeNotification($request, $notificacion);

        $notificacion->delete();

        return redirect()
            ->route('private.notificaciones')
            ->with('success', 'Notificacion eliminada.');
    }

    public function getData(Request $request)
    {
        $query = Notificacion::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titulo', 'like', '%' . $search . '%')
                    ->orWhere('mensaje', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->has('tipo') && $request->tipo !== '') {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('leido') && $request->leido !== '') {
            $query->where('leido', $request->leido === 'true');
        }

        $sort = $request->get('sort', 'id');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort, $dir);

        $perPage = $request->get('per', 6);
        $notificaciones = $query->paginate($perPage);

        return response()->json($notificaciones);
    }

    public function show($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        return response()->json($notificacion);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tipo' => 'required|string|in:apuesta,promo,alerta,chat,info,mensaje,sistema',
            'titulo' => 'required|string|max:255',
            'mensaje' => 'nullable|string',
            'leido' => 'nullable|boolean',
            'fecha' => 'required|date',
        ]);

        $data['leido'] = (bool) ($data['leido'] ?? false);
        $notificacion = Notificacion::create($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $notificacion, 'message' => 'Notificación creada correctamente'], 201);
        }

        return back()->with('success', 'Notificación creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $notificacion = Notificacion::findOrFail($id);

        $data = $request->validate([
            'user_id' => 'sometimes|exists:users,id',
            'tipo' => 'sometimes|string|in:apuesta,promo,alerta,chat,info,mensaje,sistema',
            'titulo' => 'sometimes|string|max:255',
            'mensaje' => 'sometimes|nullable|string',
            'leido' => 'sometimes|boolean',
            'fecha' => 'sometimes|date',
        ]);

        $notificacion->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $notificacion->fresh(), 'message' => 'Notificación actualizada']);
        }

        return back()->with('success', 'Notificación actualizada correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        try {
            $notificacion = Notificacion::findOrFail($id);
            $notificacion->delete();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Notificación eliminada']);
            }

            return back()->with('success', 'Notificación eliminada correctamente.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    private function authorizeNotification(Request $request, Notificacion $notificacion): void
    {
        if ((int) $notificacion->user_id !== (int) $request->user()->id) {
            abort(403);
        }
    }
}
