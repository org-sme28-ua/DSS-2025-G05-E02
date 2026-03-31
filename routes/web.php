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

// ============================================================
// RUTAS PÚBLICAS
// ============================================================

// --- HOME ---
Route::get('/', function () {
    return view('home', [
        'juegos' => Juego::all(),
        'ultimas_apuestas' => Apuesta::with(['user','juego'])->latest('fecha')->take(5)->get(),
        'chats' => Chat::with('user')->latest()->take(5)->get(),
    ]);
});

// ============================================================
// ADMIN PANEL (VISTAS)
// ============================================================

// --- ADMIN DASHBOARD ---
Route::get('/admin', function () {
    $usuarios = User::paginate(10);
    return view('admin.usuarios.index', compact('usuarios'));
})->name('usuarios.index');

// --- ADMIN USUARIOS (vistas de edición) ---
Route::get('/admin/usuarios/{user}/edit', function ($user) {
    return "Formulario para editar al usuario: " . $user;
})->name('usuarios.edit');

Route::delete('/admin/usuarios/{user}', function ($user) {
    return "Usuario eliminado";
})->name('usuarios.destroy');

// --- ADMIN JUEGOS (vista) ---
Route::get('/admin/juegos-lista', function() {
    return "Lista de juegos (puedes crear una vista para esto luego)";
})->name('juegos.index');

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
})->name('admin.tablas');

// ============================================================
// API RUTAS PARA ADMIN (CRUD para JavaScript)
// ============================================================

// --- RUTAS PARA USUARIOS ---
Route::prefix('admin')->group(function () {
    Route::get('/usuarios/data', [UserController::class, 'getData'])->name('admin.usuarios.data');
    Route::get('/usuarios/{user}', [UserController::class, 'ver'])->name('admin.usuarios.show');
    Route::post('/usuarios', [UserController::class, 'crear'])->name('admin.usuarios.store');
    Route::put('/usuarios/{user}', [UserController::class, 'actualizar'])->name('admin.usuarios.update');
    Route::delete('/usuarios/{user}', [UserController::class, 'eliminar'])->name('admin.usuarios.destroy');
});

// --- RUTAS PARA APUESTAS ---
Route::prefix('admin')->group(function () {
Route::get('/apuestas/data', [ApuestaController::class, 'getData'])->name('admin.apuestas.data');    
Route::get('/apuestas/{apuesta}', [ApuestaController::class, 'show'])->name('admin.apuestas.show');
    Route::post('/apuestas', [ApuestaController::class, 'store'])->name('admin.apuestas.store');
    Route::put('/apuestas/{apuesta}', [ApuestaController::class, 'update'])->name('admin.apuestas.update');
    Route::delete('/apuestas/{apuesta}', [ApuestaController::class, 'destroy'])->name('admin.apuestas.destroy');
});

// --- RUTAS PARA JUEGOS ---
Route::prefix('admin')->group(function () {
    Route::get('/juegos/data', [JuegoController::class, 'listar'])->name('admin.juegos.data');
    Route::get('/juegos/{juego}', [JuegoController::class, 'ver'])->name('admin.juegos.show');
    Route::post('/juegos', [JuegoController::class, 'crear'])->name('admin.juegos.store');
    Route::put('/juegos/{juego}', [JuegoController::class, 'actualizar'])->name('admin.juegos.update');
    Route::delete('/juegos/{juego}', [JuegoController::class, 'eliminar'])->name('admin.juegos.destroy');
});

// --- RUTAS PARA BILLETERAS ---
Route::prefix('admin')->group(function () {
    Route::get('/billeteras/data', [BilleteraController::class, 'listar'])->name('admin.billeteras.data');
    Route::get('/billeteras/{billetera}', [BilleteraController::class, 'ver'])->name('admin.billeteras.show');
    Route::post('/billeteras', [BilleteraController::class, 'crear'])->name('admin.billeteras.store');
    Route::put('/billeteras/{billetera}', [BilleteraController::class, 'actualizar'])->name('admin.billeteras.update');
    Route::delete('/billeteras/{billetera}', [BilleteraController::class, 'eliminar'])->name('admin.billeteras.destroy');
});

// --- RUTAS PARA NOTIFICACIONES ---
Route::prefix('admin')->group(function () {
    Route::get('/notificaciones/data', [NotificacionController::class, 'listar'])->name('admin.notificaciones.data');
    Route::get('/notificaciones/{notificacion}', [NotificacionController::class, 'ver'])->name('admin.notificaciones.show');
    Route::post('/notificaciones', [NotificacionController::class, 'crear'])->name('admin.notificaciones.store');
    Route::put('/notificaciones/{notificacion}', [NotificacionController::class, 'actualizar'])->name('admin.notificaciones.update');
    Route::delete('/notificaciones/{notificacion}', [NotificacionController::class, 'eliminar'])->name('admin.notificaciones.destroy');
});

// --- RUTAS PARA CHATS ---
Route::prefix('admin')->group(function () {
    Route::get('/chats/data', [ChatController::class, 'listar'])->name('admin.chats.data');
    Route::get('/chats/{chat}', [ChatController::class, 'ver'])->name('admin.chats.show');
    Route::post('/chats', [ChatController::class, 'crear'])->name('admin.chats.store');
    Route::put('/chats/{chat}', [ChatController::class, 'actualizar'])->name('admin.chats.update');
    Route::delete('/chats/{chat}', [ChatController::class, 'eliminar'])->name('admin.chats.destroy');
});

// --- RUTAS PARA MENSAJES ---
Route::prefix('admin')->group(function () {
    Route::get('/mensajes/data', [MensajeController::class, 'listar'])->name('admin.mensajes.data');
    Route::get('/mensajes/{mensaje}', [MensajeController::class, 'ver'])->name('admin.mensajes.show');
    Route::post('/mensajes', [MensajeController::class, 'crear'])->name('admin.mensajes.store');
    Route::put('/mensajes/{mensaje}', [MensajeController::class, 'actualizar'])->name('admin.mensajes.update');
    Route::delete('/mensajes/{mensaje}', [MensajeController::class, 'eliminar'])->name('admin.mensajes.destroy');
});

// --- RUTAS PARA RANKINGS ---
Route::prefix('admin')->group(function () {
    Route::get('/rankings/data', [RankingController::class, 'listar'])->name('admin.rankings.data');
    Route::get('/rankings/{ranking}', [RankingController::class, 'ver'])->name('admin.rankings.show');
    Route::post('/rankings', [RankingController::class, 'crear'])->name('admin.rankings.store');
    Route::put('/rankings/{ranking}', [RankingController::class, 'actualizar'])->name('admin.rankings.update');
    Route::delete('/rankings/{ranking}', [RankingController::class, 'eliminar'])->name('admin.rankings.destroy');
});

// --- RUTAS PARA SETTINGS ---
Route::prefix('admin')->group(function () {
    Route::get('/settings/data', [SettingController::class, 'getData'])->name('admin.settings.data');
    Route::get('/settings/{setting}', [SettingController::class, 'show'])->name('admin.settings.show');
    Route::post('/settings', [SettingController::class, 'store'])->name('admin.settings.store');
    Route::put('/settings/{setting}', [SettingController::class, 'update'])->name('admin.settings.update');
    Route::delete('/settings/{setting}', [SettingController::class, 'destroy'])->name('admin.settings.destroy');
});

// ============================================================
// API RUTAS ORIGINALES (sin prefijo admin)
// ============================================================

// --- USUARIOS API ---
Route::controller(UserController::class)->group(function () {
    Route::get('users', 'listar');
    Route::get('users/{user}', 'ver');
    Route::post('users', 'crear');
    Route::put('users/{user}', 'actualizar');
    Route::delete('users/{user}', 'eliminar');
});

// --- APUESTAS API ---
Route::controller(ApuestaController::class)->group(function () {
    Route::get('apuestas', 'listar');
    Route::get('apuestas/{apuesta}', 'ver');
    Route::post('apuestas', 'crear');
    Route::put('apuestas/{apuesta}', 'actualizar');
    Route::delete('apuestas/{apuesta}', 'eliminar');
});

// --- JUEGOS API ---
Route::controller(JuegoController::class)->group(function () {
    Route::get('juegos', 'listar');
    Route::get('juegos/{juego}', 'ver');
    Route::post('juegos', 'crear');
    Route::put('juegos/{juego}', 'actualizar');
    Route::delete('juegos/{juego}', 'eliminar');
});

// --- BILLETERAS API ---
Route::controller(BilleteraController::class)->group(function () {
    Route::get('billeteras', 'listar');
    Route::get('billeteras/{billetera}', 'ver');
    Route::post('billeteras', 'crear');
    Route::put('billeteras/{billetera}', 'actualizar');
    Route::delete('billeteras/{billetera}', 'eliminar');
});

// --- NOTIFICACIONES API ---
Route::controller(NotificacionController::class)->group(function () {
    Route::get('notificaciones', 'listar');
    Route::get('notificaciones/{notificacion}', 'ver');
    Route::post('notificaciones', 'crear');
    Route::put('notificaciones/{notificacion}', 'actualizar');
    Route::delete('notificaciones/{notificacion}', 'eliminar');
});

// --- CHATS API ---
Route::controller(ChatController::class)->group(function () {
    Route::get('chats', 'listar');
    Route::get('chats/{chat}', 'ver');
    Route::post('chats', 'crear');
    Route::put('chats/{chat}', 'actualizar');
    Route::delete('chats/{chat}', 'eliminar');
});

// --- MENSAJES API ---
Route::controller(MensajeController::class)->group(function () {
    Route::get('mensajes', 'listar');
    Route::get('mensajes/{mensaje}', 'ver');
    Route::post('mensajes', 'crear');
    Route::put('mensajes/{mensaje}', 'actualizar');
    Route::delete('mensajes/{mensaje}', 'eliminar');
});

// --- RANKINGS API ---
Route::controller(RankingController::class)->group(function () {
    Route::get('rankings', 'listar');
    Route::get('rankings/{ranking}', 'ver');
    Route::post('rankings', 'crear');
    Route::put('rankings/{ranking}', 'actualizar');
    Route::delete('rankings/{ranking}', 'eliminar');
});

// --- SETTINGS API ---
Route::controller(SettingController::class)->group(function () {
    Route::get('settings', 'listar');
    Route::get('settings/{setting}', 'ver');
    Route::post('settings', 'crear');
    Route::put('settings/{setting}', 'actualizar');
    Route::delete('settings/{setting}', 'eliminar');
});