<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtenemos todas las tablas de tu BD (menos las de sistema)
        $allTables = Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
        $excluded = ['migrations', 'failed_jobs', 'password_reset_tokens', 'personal_access_tokens', 'sessions'];
        $tables = array_diff($allTables, $excluded);

        // 2. Tabla seleccionada (por defecto la primera de tu modelo)
        $tableName = $request->input('tabla', reset($tables));
        
        // 3. Requisitos E02-T04: Búsqueda, Orden y Paginación [cite: 52, 53, 54]
        $search = $request->input('search');
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'asc');

        $query = DB::table($tableName);

        if ($search) {
            // Buscamos en la primera columna que no sea ID (suele ser 'nombre' o 'titulo')
            $columns = Schema::getColumnListing($tableName);
            $searchColumn = $columns[1] ?? 'id'; 
            $query->where($searchColumn, 'LIKE', "%{$search}%");
        }

        $items = $query->orderBy($sort, $order)->paginate(10); // Paginación 
        $columns = Schema::getColumnListing($tableName);

        return view('index', compact('items', 'tableName', 'tables', 'columns'));
    }

    public function changeTable(Request $request) {
        return redirect()->route('home', ['tabla' => $request->tabla]);
    }

    public function destroy($tabla, $id) {
        DB::table($tabla)->where('id', $id)->delete(); // Borrar objeto 
        return back()->with('success', 'Eliminado correctamente');
    }
}