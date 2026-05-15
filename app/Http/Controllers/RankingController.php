<?php

namespace App\Http\Controllers;

use App\Models\Ranking;
use App\Models\User;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index(Request $request)
    {
        $query = Ranking::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('posicion', 'like', "%{$search}%");
        }

        $allowedSorts = ['id', 'posicion', 'puntos', 'total_ganado', 'user_id'];
        $sort = in_array($request->get('sort'), $allowedSorts, true) ? $request->get('sort') : 'posicion';
        $dir = $request->get('dir') === 'desc' ? 'desc' : 'asc';
        $rankings = $query->orderBy($sort, $dir)->paginate((int) $request->get('per', 10))->withQueryString();
        $allRankings = Ranking::with('user')->orderBy('posicion')->take(3)->get();
        $usuarios = User::orderBy('name')->get();

        return view('rankings.index', compact('rankings', 'allRankings', 'usuarios', 'sort', 'dir'));
    }

    public function getData(Request $request)
    {
        $query = Ranking::with('user');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', $search)->orWhere('email', 'like', $search));
        }

        $allowedSorts = ['id', 'posicion', 'puntos', 'total_ganado', 'user_id'];
        $sort = in_array($request->get('sort'), $allowedSorts, true) ? $request->get('sort') : 'posicion';
        $dir = $request->get('dir') === 'desc' ? 'desc' : 'asc';

        return response()->json($query->orderBy($sort, $dir)->paginate((int) $request->get('per', 10)));
    }

    public function show($id)
    {
        return response()->json(Ranking::with('user')->findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id|unique:rankings,user_id',
            'posicion' => 'required|integer|min:1',
            'puntos' => 'required|numeric|min:0',
            'total_ganado' => 'required|numeric|min:0',
        ]);

        $ranking = Ranking::create($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $ranking, 'message' => 'Ranking creado correctamente'], 201);
        }

        return back()->with('success', 'Entrada de ranking creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $ranking = Ranking::findOrFail($id);

        $data = $request->validate([
            'user_id' => 'required|exists:users,id|unique:rankings,user_id,' . $ranking->id,
            'posicion' => 'required|integer|min:1',
            'puntos' => 'required|numeric|min:0',
            'total_ganado' => 'required|numeric|min:0',
        ]);

        $ranking->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $ranking->fresh(), 'message' => 'Ranking actualizado correctamente']);
        }

        return back()->with('success', 'Ranking actualizado correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $ranking = Ranking::findOrFail($id);
        $ranking->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Ranking eliminado correctamente']);
        }

        return back()->with('success', 'Entrada de ranking eliminada correctamente.');
    }
}
