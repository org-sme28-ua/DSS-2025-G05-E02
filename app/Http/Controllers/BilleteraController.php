<?php

namespace App\Http\Controllers;

use App\Models\Billetera;
use Illuminate\Http\Request;

class BilleteraController extends Controller
{
    public function getData(Request $request)
    {
        $query = Billetera::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('id', 'like', '%' . $search . '%')
                  ->orWhere('moneda', 'like', '%' . $search . '%');
        }

        if ($request->has('moneda') && $request->moneda !== '') {
            $query->where('moneda', $request->moneda);
        }

        $sort = $request->get('sort', 'id');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort, $dir);

        $perPage = $request->get('per', 6);
        return response()->json($query->paginate($perPage));
    }

    public function show($id)
    {
        return response()->json(Billetera::findOrFail($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'saldoDisponible' => 'required|numeric|min:0',
            'moneda' => 'required|string|max:10',
        ]);

        $billetera = Billetera::create($data);

        return response()->json([
            'success' => true,
            'data' => $billetera,
            'message' => 'Billetera creada correctamente'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $billetera = Billetera::findOrFail($id);

        $data = $request->validate([
            'saldoDisponible' => 'sometimes|numeric|min:0',
            'moneda' => 'sometimes|string|max:10',
        ]);

        $billetera->update($data);

        return response()->json([
            'success' => true,
            'data' => $billetera,
            'message' => 'Billetera actualizada correctamente'
        ]);
    }

    public function destroy($id)
    {
        $billetera = Billetera::findOrFail($id);
        $billetera->delete();

        return response()->json([
            'success' => true,
            'message' => 'Billetera eliminada correctamente'
        ]);
    }
}
