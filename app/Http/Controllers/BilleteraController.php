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
            $query->where('usuario', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('moneda') && $request->moneda !== '') {
            $query->where('moneda', $request->moneda);
        }
        
        $sort = $request->get('sort', 'id');
        $dir = $request->get('dir', 'asc');
        $query->orderBy($sort, $dir);
        
        $perPage = $request->get('per', 6);
        $billeteras = $query->paginate($perPage);
        
        return response()->json($billeteras);
    }
    
    public function show($id)
    {
        $billetera = Billetera::findOrFail($id);
        return response()->json($billetera);
    }
    
    public function store(Request $request)
    {
        $data = $request->validate([
            'usuario' => 'required|string|max:255|unique:billeteras',
            'saldo' => 'required|numeric|min:0',
            'moneda' => 'required|string|in:EUR,USD,GBP',
        ]);
        
        $billetera = Billetera::create($data);
        
        return response()->json(['success' => true, 'data' => $billetera, 'message' => 'Billetera creada correctamente'], 201);
    }
    
    public function update(Request $request, $id)
    {
        $billetera = Billetera::findOrFail($id);
        
        $data = $request->validate([
            'usuario' => 'sometimes|string|max:255|unique:billeteras,usuario,' . $id,
            'saldo' => 'sometimes|numeric|min:0',
            'moneda' => 'sometimes|string|in:EUR,USD,GBP',
        ]);
        
        $billetera->update($data);
        
        return response()->json(['success' => true, 'data' => $billetera, 'message' => 'Billetera actualizada correctamente']);
    }
    
    public function destroy($id)
    {
        $billetera = Billetera::findOrFail($id);
        $billetera->delete();
        
        return response()->json(['success' => true, 'message' => 'Billetera eliminada correctamente']);
    }
}