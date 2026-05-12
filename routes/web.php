<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApuestaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BilleteraController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CoinFlipController;
use App\Http\Controllers\DiceController;
use App\Http\Controllers\JuegoController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ParametroGananciaController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\RouletteController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Models\Apuesta;
use App\Models\Billetera;
use App\Models\Mensaje;
use App\Models\Notificacion;
use Illuminate\Support\Facades\Route;

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
    Route::get('/dashboard', function () {
        $user = auth()->user();

        $billetera = Billetera::firstOrCreate(
            ['user_id' => $user->id],
            ['saldoDisponible' => 0, 'moneda' => 'EUR']
        );

        $gananciaNeta = (float) $user->apuestas()
            ->where('estado', 'ganada')
            ->selectRaw('COALESCE(SUM(monto * (cuota - 1)), 0) as total')
            ->value('total');

        $perdidaNeta = (float) $user->apuestas()
            ->where('estado', 'perdida')
            ->sum('monto');

        $dashboardStats = [
            'saldo' => (float) $billetera->saldoDisponible,
            'total_apostado' => (float) $user->apuestas()->sum('monto'),
            'ganancia_neta' => $gananciaNeta,
            'perdida_neta' => $perdidaNeta,
            'balance_neto' => $gananciaNeta - $perdidaNeta,
            'apuestas_activas' => $user->apuestas()->whereIn('estado', ['pendiente', 'aceptada'])->count(),
            'apuestas_ganadas' => $user->apuestas()->where('estado', 'ganada')->count(),
            'apuestas_perdidas' => $user->apuestas()->where('estado', 'perdida')->count(),
            'notificaciones_nuevas' => $user->notificaciones()->where('leido', false)->count(),
        ];

        $recentBets = $user->apuestas()
            ->with('juego')
            ->latest('fecha')
            ->take(6)
            ->get();

        return view('dashboard', compact('dashboardStats', 'recentBets'));
    })->name('dashboard');

    Route::view('/juegos', 'games')->name('private.games');

    Route::get('/billetera', [BilleteraController::class, 'index'])->name('billetera');
    Route::post('/billetera/ingresar', [BilleteraController::class, 'deposit'])->name('billetera.deposit');
    Route::post('/billetera/retirar', [BilleteraController::class, 'withdraw'])->name('billetera.withdraw');

    Route::get('/mis-apuestas', function () {
        $apuestas = auth()->user()->apuestas()
            ->with('juego')
            ->latest('fecha')
            ->get();

        return view('apuestas', compact('apuestas'));
    })->name('private.apuestas');


    Route::get('/rankings', function () {
        $search = request('search', '');
        $sort   = in_array(request('sort'), ['posicion','puntos','total_ganado','id']) ? request('sort') : 'posicion';
        $dir    = request('dir', 'asc') === 'desc' ? 'desc' : 'asc';
     
        $rankings = \App\Models\Ranking::with('user')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $dir)
            ->paginate(15)
            ->withQueryString();
     
        // Top 3 para el podio (siempre por posición)
        $top3 = \App\Models\Ranking::with('user')
            ->orderBy('posicion')
            ->take(3)
            ->get();
     
        return view('rankings', compact('rankings', 'top3'));
    })->name('private.rankings');




    Route::get('/mis-notificaciones', function () {
        $notificaciones = Notificacion::query()
            ->where('user_id', auth()->id())
            ->latest('fecha')
            ->get();

        return view('notificaciones', compact('notificaciones'));
    })->name('private.notificaciones');

    Route::post('/mis-notificaciones/marcar-todas', function () {
        Notificacion::where('user_id', auth()->id())->where('leido', false)->update(['leido' => true]);
        return back()->with('success', 'Todas las notificaciones se han marcado como leídas.');
    })->name('private.notificaciones.read_all');

    Route::post('/mis-notificaciones/{notificacion}/leer', function (Notificacion $notificacion) {
        abort_unless($notificacion->user_id === auth()->id(), 403);
        $notificacion->update(['leido' => true]);
        return back();
    })->name('private.notificaciones.read');

    Route::get('/chat', [ChatController::class, 'index'])->name('private.chat');
    Route::post('/chat/start', [ChatController::class, 'start'])->name('private.chat.start');
    Route::get('/chat/{chat}', [ChatController::class, 'showConversation'])->name('private.chat.show');
    Route::post('/chat/{chat}/mensaje', [ChatController::class, 'sendMessage'])->name('private.chat.message');

    Route::view('/configuracion', 'configuracion')->name('private.configuracion');

    Route::get('/ruleta', [RouletteController::class, 'index'])->name('roulette.index');
    Route::post('/ruleta', [RouletteController::class, 'play'])->name('roulette.play');

    Route::get('/prediccion', [PredictionController::class, 'index'])->name('prediction.index');
    Route::post('/prediccion', [PredictionController::class, 'store'])->name('prediction.store');

    Route::get('/dados', [DiceController::class, 'index'])->name('dice.index');
    Route::post('/dados', [DiceController::class, 'play'])->name('dice.play');

    Route::get('/cara-o-cruz', [CoinFlipController::class, 'index'])->name('coin.index');
    Route::post('/cara-o-cruz', [CoinFlipController::class, 'play'])->name('coin.play');

    // ============================================================
    // PANEL DE ADMINISTRACIÓN
    // ============================================================
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.panel');
    Route::get('/admin/usuarios/{user}/resumen', [AdminController::class, 'userSummary'])->name('admin.users.summary');
    Route::post('/admin/apuestas/{apuesta}/resolver', [AdminController::class, 'resolvePrediction'])->name('admin.predictions.resolve');

    // ============================================================
    // API RUTAS PARA ADMIN
    // ============================================================
    Route::prefix('admin')->group(function () {
        Route::get('/usuarios/data', [UserController::class, 'getData'])->name('admin.usuarios.data');
        Route::get('/usuarios/{user}', [UserController::class, 'ver'])->name('admin.usuarios.show');
        Route::post('/usuarios', [UserController::class, 'crear'])->name('admin.usuarios.store');
        Route::put('/usuarios/{user}', [UserController::class, 'actualizar'])->name('admin.usuarios.update');
        Route::delete('/usuarios/{user}', [UserController::class, 'eliminar'])->name('admin.usuarios.destroy');

        Route::get('/apuestas/data', [ApuestaController::class, 'getData'])->name('admin.apuestas.data');
        Route::get('/apuestas/{apuesta}', [ApuestaController::class, 'show'])->name('admin.apuestas.show');
        Route::post('/apuestas', [ApuestaController::class, 'store'])->name('admin.apuestas.store');
        Route::put('/apuestas/{apuesta}', [ApuestaController::class, 'update'])->name('admin.apuestas.update');
        Route::delete('/apuestas/{apuesta}', [ApuestaController::class, 'destroy'])->name('admin.apuestas.destroy');

        Route::get('/juegos/data', [JuegoController::class, 'getData'])->name('admin.juegos.data');
        Route::get('/juegos/{juego}', [JuegoController::class, 'show'])->name('admin.juegos.show');
        Route::post('/juegos', [JuegoController::class, 'store'])->name('admin.juegos.store');
        Route::put('/juegos/{juego}', [JuegoController::class, 'update'])->name('admin.juegos.update');
        Route::delete('/juegos/{juego}', [JuegoController::class, 'destroy'])->name('admin.juegos.destroy');

        Route::get('/billeteras/data', [BilleteraController::class, 'getData'])->name('admin.billeteras.data');
        Route::get('/billeteras/{billetera}', [BilleteraController::class, 'show'])->name('admin.billeteras.show');
        Route::post('/billeteras', [BilleteraController::class, 'store'])->name('admin.billeteras.store');
        Route::put('/billeteras/{billetera}', [BilleteraController::class, 'update'])->name('admin.billeteras.update');
        Route::delete('/billeteras/{billetera}', [BilleteraController::class, 'destroy'])->name('admin.billeteras.destroy');

        Route::get('/notificaciones/data', [NotificacionController::class, 'getData'])->name('admin.notificaciones.data');
        Route::get('/notificaciones/{notificacion}', [NotificacionController::class, 'show'])->name('admin.notificaciones.show');
        Route::post('/notificaciones', [NotificacionController::class, 'store'])->name('admin.notificaciones.store');
        Route::put('/notificaciones/{notificacion}', [NotificacionController::class, 'update'])->name('admin.notificaciones.update');
        Route::delete('/notificaciones/{notificacion}', [NotificacionController::class, 'destroy'])->name('admin.notificaciones.destroy');

        Route::get('/chats/data', [ChatController::class, 'getData'])->name('admin.chats.data');
        Route::get('/chats/{chat}', [ChatController::class, 'show'])->name('admin.chats.show');
        Route::post('/chats', [ChatController::class, 'store'])->name('admin.chats.store');
        Route::put('/chats/{chat}', [ChatController::class, 'update'])->name('admin.chats.update');
        Route::delete('/chats/{chat}', [ChatController::class, 'destroy'])->name('admin.chats.destroy');

        Route::get('/mensajes/data', [MensajeController::class, 'listar'])->name('admin.mensajes.data');
        Route::get('/mensajes/{mensaje}', [MensajeController::class, 'ver'])->name('admin.mensajes.show');
        Route::post('/mensajes', [MensajeController::class, 'crear'])->name('admin.mensajes.store');
        Route::put('/mensajes/{mensaje}', [MensajeController::class, 'actualizar'])->name('admin.mensajes.update');
        Route::delete('/mensajes/{mensaje}', [MensajeController::class, 'eliminar'])->name('admin.mensajes.destroy');



        Route::get('/rankings/data', [RankingController::class, 'getData'])->name('admin.rankings.data');
        Route::get('/rankings/{ranking}', [RankingController::class, 'show'])->name('admin.rankings.show');
        Route::post('/rankings', [RankingController::class, 'store'])->name('admin.rankings.store');
        Route::put('/rankings/{ranking}', [RankingController::class, 'update'])->name('admin.rankings.update');
        Route::delete('/rankings/{ranking}', [RankingController::class, 'destroy'])->name('admin.rankings.destroy');

        Route::get('/settings/data', [SettingController::class, 'getData'])->name('admin.settings.data');
        Route::get('/settings/{setting}', [SettingController::class, 'show'])->name('admin.settings.show');
        Route::post('/settings', [SettingController::class, 'store'])->name('admin.settings.store');
        Route::put('/settings/{setting}', [SettingController::class, 'update'])->name('admin.settings.update');
        Route::delete('/settings/{setting}', [SettingController::class, 'destroy'])->name('admin.settings.destroy');

        Route::get('/parametros-ganancia/data', [ParametroGananciaController::class, 'getData'])->name('admin.parametros_ganancia.data');
        Route::get('/parametros-ganancia/{parametro}', [ParametroGananciaController::class, 'show'])->name('admin.parametros_ganancia.show');
        Route::post('/parametros-ganancia', [ParametroGananciaController::class, 'store'])->name('admin.parametros_ganancia.store');
        Route::put('/parametros-ganancia/{parametro}', [ParametroGananciaController::class, 'update'])->name('admin.parametros_ganancia.update');
        Route::delete('/parametros-ganancia/{parametro}', [ParametroGananciaController::class, 'destroy'])->name('admin.parametros_ganancia.destroy');
    });
});
