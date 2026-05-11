<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use App\Models\User;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    /**
     * Listado con búsqueda, ordenación y paginación.
     */
    public function index(Request $request)
    {
        $query = Ranking::with('user');

        // ── Búsqueda por nombre de usuario ──────────────────────────
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('posicion', 'like', "%{$search}%");
        }

        // ── Ordenación ───────────────────────────────────────────────
        $allowedSorts = ['id', 'posicion', 'puntos', 'total_ganado', 'user_id'];
        $sort = in_array($request->get('sort'), $allowedSorts)
            ? $request->get('sort')
            : 'posicion';
        $dir = $request->get('dir', 'asc') === 'desc' ? 'desc' : 'asc';

        $query->orderBy($sort, $dir);

        // ── Paginación ───────────────────────────────────────────────
        $per      = in_array((int) $request->get('per'), [10, 25, 50]) ? (int) $request->get('per') : 10;
        $rankings = $query->paginate($per)->withQueryString();

        // Top 3 para el podio (siempre ordenado por posicion, sin paginar)
        $allRankings = Ranking::with('user')->orderBy('posicion')->take(3)->get();

        // Usuarios disponibles para el formulario de creación
        $usuarios = User::orderBy('name')->get();

        return view('rankings.index', compact('rankings', 'allRankings', 'usuarios', 'sort', 'dir'));
    }

    /**
     * Guardar nuevo ranking.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'      => 'required|exists:users,id|unique:rankings,user_id',
            'posicion'     => 'required|integer|min:1',
            'puntos'       => 'required|numeric|min:0',
            'total_ganado' => 'required|numeric|min:0',
        ], [
            'user_id.required'  => 'Debes seleccionar un usuario.',
            'user_id.unique'    => 'Este usuario ya tiene una entrada en el ranking.',
            'posicion.required' => 'La posición es obligatoria.',
            'posicion.min'      => 'La posición debe ser al menos 1.',
            'puntos.required'   => 'Los puntos son obligatorios.',
            'puntos.min'        => 'Los puntos no pueden ser negativos.',
            'total_ganado.min'  => 'El total ganado no puede ser negativo.',
        ]);

        Ranking::create($data);

        return redirect()->route('rankings.index')
            ->with('success', 'Entrada de ranking creada correctamente.');
    }

    /**
     * Actualizar ranking existente.
     */
    public function update(Request $request, $id)
    {
        $ranking = Ranking::findOrFail($id);

        $data = $request->validate([
            'user_id'      => 'required|exists:users,id',
            'posicion'     => 'required|integer|min:1',
            'puntos'       => 'required|numeric|min:0',
            'total_ganado' => 'required|numeric|min:0',
        ], [
            'user_id.required'  => 'Debes seleccionar un usuario.',
            'posicion.required' => 'La posición es obligatoria.',
            'posicion.min'      => 'La posición debe ser al menos 1.',
            'puntos.min'        => 'Los puntos no pueden ser negativos.',
            'total_ganado.min'  => 'El total ganado no puede ser negativo.',
        ]);

        $ranking->update($data);

        return redirect()->route('rankings.index')
            ->with('success', 'Ranking actualizado correctamente.');
    }

    /**
     * Eliminar ranking.
     */
    public function destroy($id)
    {
        $ranking = Ranking::findOrFail($id);
        $ranking->delete();

        return redirect()->route('rankings.index')
            ->with('success', 'Entrada de ranking eliminada correctamente.');
    }
}
