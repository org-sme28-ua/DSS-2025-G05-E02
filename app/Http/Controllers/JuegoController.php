<?php

namespace App\Http\Controllers;

use App\Models\Juego;
use Illuminate\Http\Request;

class JuegoController extends Controller
{
    /**
     * Muestra la lista de juegos con filtros y paginación.
     */
    public function index(Request $request)
    {
        // 1. Recoger los datos de la base de datos
        $juegos = \App\Models\Juego::paginate(10);

        // 2. IMPORTANTE: Devolver la VISTA, no un JSON
        // Si devuelves response()->json(), verás solo texto.
        // Si devuelves la vista, verás tu diseño con la tabla.
        return view('admin.juegos.index', compact('juegos'));
    }

    /**
     * Muestra el formulario para crear un nuevo juego.
     */
    public function create()
    {
        return view('admin.juegos.create');
    }

    /**
     * Guarda un nuevo juego en la base de datos.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'    => 'required|string|max:255|unique:juegos',
            'categoria' => 'required|string|in:casino,deportes,virtual',
            'estado'    => 'required|string|in:abierta,cerrada,en_juego',
        ]);
        
        Juego::create($data);
        
        // REDIRECCIÓN CON MENSAJE DE ÉXITO
        return redirect()->route('juegos.index')->with('success', 'Juego creado correctamente.');
    }

    /**
     * Muestra un juego específico (opcional en admin, suele usarse edit).
     */
    public function show(Juego $juego)
    {
        return view('admin.juegos.show', compact('juego'));
    }

    /**
     * Muestra el formulario para editar un juego existente.
     */
    public function edit(Juego $juego)
    {
        return view('admin.juegos.edit', compact('juego'));
    }

    /**
     * Actualiza el juego en la base de datos.
     */
    public function update(Request $request, Juego $juego)
    {
        $data = $request->validate([
            'nombre'    => 'sometimes|string|max:255|unique:juegos,nombre,' . $juego->id,
            'categoria' => 'sometimes|string|in:casino,deportes,virtual',
            'estado'    => 'sometimes|string|in:abierta,cerrada,en_juego',
        ]);
        
        $juego->update($data);
        
        // REDIRECCIÓN CON MENSAJE DE ÉXITO
        return redirect()->route('juegos.index')->with('success', 'Juego actualizado correctamente.');
    }

    /**
     * Elimina un juego de la base de datos.
     */
    public function destroy(Juego $juego)
    {
        $juego->delete();
        
        // REDIRECCIÓN CON MENSAJE DE ÉXITO
        return redirect()->route('juegos.index')->with('success', 'Juego eliminado correctamente.');
    }
}