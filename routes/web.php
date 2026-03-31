<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApuestaController;
use App\Http\Controllers\BilleteraController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;

use App\Models\Apuesta;
use App\Models\Chat;
use App\Models\Juego;
use App\Models\User;

// --- HOME ---
Route::get('/', function () {
    return view('home', [
        'juegos' => Juego::all(),
        'ultimas_apuestas' => Apuesta::with(['user','juego'])->latest('fecha')->take(5)->get(),
        'chats' => Chat::with('user')->latest()->take(5)->get(),
    ]);
});

// --- ADMIN PANEL (USUARIOS) ---
// Añadido ->name('usuarios.index') para que el Sidebar funcione
Route::get('/admin', function () {
    $usuarios = User::paginate(10);
    return view('admin.usuarios.index', compact('usuarios'));
})->name('usuarios.index');

Route::get('/admin/usuarios/{user}/edit', function ($user) {
    return "Formulario para editar al usuario: " . $user;
})->name('usuarios.edit');

Route::delete('/admin/usuarios/{user}', function ($user) {
    return "Usuario eliminado";
})->name('usuarios.destroy');

// --- TABLAS DINÁMICAS ---
Route::get('/admin/tabla/{tabla}', function ($tabla) {
    $tablas = [
        'users' => \App\Models\User::class,
        'apuestas' => \App\Models\Apuesta::class,
        'billeteras' => \App\Models\Billetera::class,
        'chats' => \App\Models\Chat::class,
        'juegos' => \App\Models\Juego::class,
        'mensajes' => \App\Models\Mensaje::class,
        'notificaciones' => \App\Models\Notificacion::class,
        'rankings' => \App\Models\Ranking::class,
        'settings' => \App\Models\Setting::class,
    ];

    if (!array_key_exists($tabla, $tablas)) {
        abort(404);
    }

    $modelo = $tablas[$tabla];
    $registros = $modelo::all();

    return view('admin.table', [
        'tablaActual' => $tabla,
        'registros' => $registros,
        'tablas' => array_keys($tablas),
    ]);
})->name('admin.tablas'); // Añadido nombre por si lo necesitas

// --- JUEGOS INDEX ---
// Añadida esta ruta porque tu imagen muestra que el sidebar también busca 'juegos.index'
Route::get('/admin/juegos-lista', function() {
    return "Lista de juegos (puedes crear una vista para esto luego)";
})->name('juegos.index');


// --- API / CONTROLLERS ---
Route::controller(UserController::class)->group(function () {
    Route::get('users', 'listar');
    Route::get('users/{user}', 'ver');
    Route::post('users', 'crear');
    Route::put('users/{user}', 'actualizar');
    Route::delete('users/{user}', 'eliminar');
});

Route::controller(ApuestaController::class)->group(function () {
    Route::get('apuestas', 'listar');
    Route::get('apuestas/{apuesta}', 'ver');
    Route::post('apuestas', 'crear');
    Route::put('apuestas/{apuesta}', 'actualizar');
    Route::delete('apuestas/{apuesta}', 'eliminar');
});

// ... (El resto de tus controladores están bien como los tenías)
