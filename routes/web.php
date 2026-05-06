<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApuestaController;
use App\Http\Controllers\BilleteraController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ParametroGananciaController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Models\User;

// ============================================================
// RUTAS PÚBLICAS
// ============================================================

Route::view('/', 'public.home')->name('public.home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ============================================================
// RUTAS PRIVADAS
// ============================================================

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/billetera', function () {
        $user = auth()->user();

        if (!$user->billetera) {
            $user->billetera()->create([
                'saldoDisponible' => 0,
                'moneda' => 'EUR',
            ]);
            $user->load('billetera');
        }

        $apuestas = $user->apuestas()
            ->with('juego')
            ->latest('fecha')
            ->take(5)
            ->get();

        return view('billetera', [
            'billetera' => $user->billetera,
            'apuestas' => $apuestas,
            'totalApuestas' => $user->apuestas()->count(),
            'apuestasPendientes' => $user->apuestas()->where('estado', 'pendiente')->count(),
            'apuestasGanadas' => $user->apuestas()->where('estado', 'ganada')->count(),
        ]);
    })->name('billetera');

    Route::get('/mis-apuestas', function () {
        $apuestas = auth()->user()->apuestas()
            ->with('juego')
            ->latest('fecha')
            ->get();

        return view('apuestas', [
            'apuestas' => $apuestas,
        ]);
    })->name('private.apuestas');

    Route::get('/mis-notificaciones', function () {
        $notificaciones = \App\Models\Notificacion::query()
            ->where('user_id', auth()->id())
            ->latest('fecha')
            ->get();

        return view('notificaciones', [
            'notificaciones' => $notificaciones,
        ]);
    })->name('private.notificaciones');

    Route::get('/chat', function () {
        $chats = auth()->user()->chats()
            ->withCount('mensajes')
            ->latest()
            ->get();

        $mensajes = \App\Models\Mensaje::query()
            ->with(['chat', 'emisor'])
            ->whereIn('chat_id', $chats->pluck('id'))
            ->latest('fechaHora')
            ->take(8)
            ->get();

        return view('chat', [
            'chats' => $chats,
            'mensajes' => $mensajes,
        ]);
    })->name('private.chat');

    Route::view('/configuracion', 'configuracion')->name('private.configuracion');

    // --- ADMIN DASHBOARD ---
    Route::get('/admin', function () {
        $usuarios = User::paginate(10);
        return view('layouts.admin', compact('usuarios'));
    })->name('admin.panel');

    // --- ADMIN USUARIOS (vistas de edición) ---
    Route::get('/admin/usuarios/{user}/edit', function ($user) {
        return "Formulario para editar al usuario: " . $user;
    })->name('usuarios.edit');

    Route::delete('/admin/usuarios/{user}', function ($user) {
        return "Usuario eliminado";
    })->name('usuarios.destroy');

    // --- ADMIN JUEGOS (vista) ---
    Route::get('/admin/juegos-lista', function () {
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
            'parametros_ganancia' => \App\Models\ParametroGanancia::class,
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

    Route::prefix('admin')->group(function () {
        // --- USUARIOS ---
        Route::get('/usuarios/data', [UserController::class, 'getData'])->name('admin.usuarios.data');
        Route::get('/usuarios/{user}', [UserController::class, 'ver'])->name('admin.usuarios.show');
        Route::post('/usuarios', [UserController::class, 'crear'])->name('admin.usuarios.store');
        Route::put('/usuarios/{user}', [UserController::class, 'actualizar'])->name('admin.usuarios.update');
        Route::delete('/usuarios/{user}', [UserController::class, 'eliminar'])->name('admin.usuarios.destroy');

        // --- APUESTAS ---
        Route::get('/apuestas/data', [ApuestaController::class, 'getData'])->name('admin.apuestas.data');
        Route::get('/apuestas/{apuesta}', [ApuestaController::class, 'show'])->name('admin.apuestas.show');
        Route::post('/apuestas', [ApuestaController::class, 'store'])->name('admin.apuestas.store');
        Route::put('/apuestas/{apuesta}', [ApuestaController::class, 'update'])->name('admin.apuestas.update');
        Route::delete('/apuestas/{apuesta}', [ApuestaController::class, 'destroy'])->name('admin.apuestas.destroy');

        // --- JUEGOS ---
        Route::get('/juegos/data', [JuegoController::class, 'getData'])->name('admin.juegos.data');
        Route::get('/juegos/{juego}', [JuegoController::class, 'show'])->name('admin.juegos.show');
        Route::post('/juegos', [JuegoController::class, 'store'])->name('admin.juegos.store');
        Route::put('/juegos/{juego}', [JuegoController::class, 'update'])->name('admin.juegos.update');
        Route::delete('/juegos/{juego}', [JuegoController::class, 'destroy'])->name('admin.juegos.destroy');

        // --- BILLETERAS ---
        Route::get('/billeteras/data', [BilleteraController::class, 'getData'])->name('admin.billeteras.data');
        Route::get('/billeteras/{billetera}', [BilleteraController::class, 'show'])->name('admin.billeteras.show');
        Route::post('/billeteras', [BilleteraController::class, 'store'])->name('admin.billeteras.store');
        Route::put('/billeteras/{billetera}', [BilleteraController::class, 'update'])->name('admin.billeteras.update');
        Route::delete('/billeteras/{billetera}', [BilleteraController::class, 'destroy'])->name('admin.billeteras.destroy');

        // --- NOTIFICACIONES ---
        Route::get('/notificaciones/data', [NotificacionController::class, 'getData'])->name('admin.notificaciones.data');
        Route::get('/notificaciones/{notificacion}', [NotificacionController::class, 'show'])->name('admin.notificaciones.show');
        Route::post('/notificaciones', [NotificacionController::class, 'store'])->name('admin.notificaciones.store');
        Route::put('/notificaciones/{notificacion}', [NotificacionController::class, 'update'])->name('admin.notificaciones.update');
        Route::delete('/notificaciones/{notificacion}', [NotificacionController::class, 'destroy'])->name('admin.notificaciones.destroy');

        // --- CHATS ---
        Route::get('/chats/data', [ChatController::class, 'getData'])->name('admin.chats.data');
        Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('admin.chats.show');
        Route::post('/chats', [ChatController::class, 'store'])->name('admin.chats.store');
        Route::put('/chats/{chat}', [ChatController::class, 'update'])->name('admin.chats.update');
        Route::delete('/chats/{chat}', [ChatController::class, 'destroy'])->name('admin.chats.destroy');

        // --- MENSAJES ---
        Route::get('/mensajes/data', [MensajeController::class, 'listar'])->name('admin.mensajes.data');
        Route::get('/mensajes/{mensaje}', [MensajeController::class, 'ver'])->name('admin.mensajes.show');
        Route::post('/mensajes', [MensajeController::class, 'crear'])->name('admin.mensajes.store');
        Route::put('/mensajes/{mensaje}', [MensajeController::class, 'actualizar'])->name('admin.mensajes.update');
        Route::delete('/mensajes/{mensaje}', [MensajeController::class, 'eliminar'])->name('admin.mensajes.destroy');

        // --- RANKINGS ---
        Route::get('/rankings/data', [RankingController::class, 'getData'])->name('admin.rankings.data');
        Route::get('/rankings/{ranking}', [RankingController::class, 'show'])->name('admin.rankings.show');
        Route::post('/rankings', [RankingController::class, 'store'])->name('admin.rankings.store');
        Route::put('/rankings/{ranking}', [RankingController::class, 'update'])->name('admin.rankings.update');
        Route::delete('/rankings/{ranking}', [RankingController::class, 'destroy'])->name('admin.rankings.destroy');

        // --- SETTINGS ---
        Route::get('/settings/data', [SettingController::class, 'getData'])->name('admin.settings.data');
        Route::get('/settings/{setting}', [SettingController::class, 'show'])->name('admin.settings.show');
        Route::post('/settings', [SettingController::class, 'store'])->name('admin.settings.store');
        Route::put('/settings/{setting}', [SettingController::class, 'update'])->name('admin.settings.update');
        Route::delete('/settings/{setting}', [SettingController::class, 'destroy'])->name('admin.settings.destroy');

        // --- PARÁMETROS DE GANANCIA ---
        Route::get('/parametros-ganancia/data', [ParametroGananciaController::class, 'getData'])->name('admin.parametros_ganancia.data');
        Route::get('/parametros-ganancia/{parametro}', [ParametroGananciaController::class, 'show'])->name('admin.parametros_ganancia.show');
        Route::post('/parametros-ganancia', [ParametroGananciaController::class, 'store'])->name('admin.parametros_ganancia.store');
        Route::put('/parametros-ganancia/{parametro}', [ParametroGananciaController::class, 'update'])->name('admin.parametros_ganancia.update');
        Route::delete('/parametros-ganancia/{parametro}', [ParametroGananciaController::class, 'destroy'])->name('admin.parametros_ganancia.destroy');
    });

    // ============================================================
    // API RUTAS ORIGINALES (sin prefijo admin)
    // ============================================================

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

    Route::controller(JuegoController::class)->group(function () {
        Route::get('juegos', 'getData');
        Route::get('juegos/{juego}', 'show');
        Route::post('juegos', 'store');
        Route::put('juegos/{juego}', 'update');
        Route::delete('juegos/{juego}', 'destroy');
    });

    Route::controller(BilleteraController::class)->group(function () {
        Route::get('billeteras', 'getData');
        Route::get('billeteras/{billetera}', 'show');
        Route::post('billeteras', 'store');
        Route::put('billeteras/{billetera}', 'update');
        Route::delete('billeteras/{billetera}', 'destroy');
    });

    Route::controller(NotificacionController::class)->group(function () {
        Route::get('notificaciones', 'listar');
        Route::get('notificaciones/{notificacion}', 'ver');
        Route::post('notificaciones', 'crear');
        Route::put('notificaciones/{notificacion}', 'actualizar');
        Route::delete('notificaciones/{notificacion}', 'eliminar');
    });

    Route::controller(ChatController::class)->group(function () {
        Route::get('chats', 'getData');
        Route::get('chats/{chat}', 'show');
        Route::post('chats', 'store');
        Route::put('chats/{chat}', 'update');
        Route::delete('chats/{chat}', 'destroy');
    });

    Route::controller(MensajeController::class)->group(function () {
        Route::get('mensajes', 'listar');
        Route::get('mensajes/{mensaje}', 'ver');
        Route::post('mensajes', 'crear');
        Route::put('mensajes/{mensaje}', 'actualizar');
        Route::delete('mensajes/{mensaje}', 'eliminar');
    });

    Route::controller(RankingController::class)->group(function () {
        Route::get('rankings', 'getData');
        Route::get('rankings/{ranking}', 'show');
        Route::post('rankings', 'store');
        Route::put('rankings/{ranking}', 'update');
        Route::delete('rankings/{ranking}', 'destroy');
    });

    Route::controller(SettingController::class)->group(function () {
        Route::get('settings', 'listar');
        Route::get('settings/{setting}', 'ver');
        Route::post('settings', 'crear');
        Route::put('settings/{setting}', 'actualizar');
        Route::delete('settings/{setting}', 'eliminar');
    });

    Route::controller(ParametroGananciaController::class)->group(function () {
        Route::get('parametros-ganancia', 'getData');
        Route::get('parametros-ganancia/{parametro}', 'show');
        Route::post('parametros-ganancia', 'store');
        Route::put('parametros-ganancia/{parametro}', 'update');
        Route::delete('parametros-ganancia/{parametro}', 'destroy');
    });
});
