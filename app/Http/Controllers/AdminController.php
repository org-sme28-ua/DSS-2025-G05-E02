<?php

namespace App\Http\Controllers;

use App\Models\Apuesta;
use App\Models\Billetera;
use App\Models\Chat;
use App\Models\Juego;
use App\Models\Mensaje;
use App\Models\Notificacion;
use App\Models\ParametroGanancia;
use App\Models\Ranking;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    private function ensureAdmin(): void
    {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Solo los administradores pueden acceder a este panel.');
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $allowedSections = [
            'resumen', 'usuarios', 'apuestas', 'predicciones', 'juegos', 'billeteras',
            'notificaciones', 'chats', 'mensajes', 'amigos', 'rankings', 'settings', 'parametros'
        ];
        $section = in_array($request->get('section'), $allowedSections, true) ? $request->get('section') : 'resumen';

        $juegos = Juego::orderBy('nombre')->get();
        $allUsers = User::orderBy('name')->get(['id', 'name', 'email', 'role']);
        $usuariosAdmin = $allUsers;
        $tipos = Apuesta::query()->select('tipo')->whereNotNull('tipo')->distinct()->orderBy('tipo')->pluck('tipo');
        $categorias = Juego::query()->select('categoria')->whereNotNull('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        $notificationTypes = Notificacion::query()->select('tipo')->whereNotNull('tipo')->distinct()->orderBy('tipo')->pluck('tipo');
        $allChats = Chat::with(['userOne', 'userTwo'])->orderByDesc('updated_at')->get();

        $userSort = in_array($request->get('user_sort'), ['id', 'name', 'email', 'role', 'puntos_fidelidad', 'nivel_vip', 'created_at'], true)
            ? $request->get('user_sort')
            : 'id';
        $userDir = $request->get('user_dir') === 'desc' ? 'desc' : 'asc';

        $usuarios = User::query()
            ->with('billetera')
            ->withCount([
                'apuestas',
                'apuestas as apuestas_pendientes_count' => fn ($q) => $q->whereIn('estado', ['pendiente', 'aceptada']),
                'apuestas as apuestas_ganadas_count' => fn ($q) => $q->where('estado', 'ganada'),
                'apuestas as apuestas_perdidas_count' => fn ($q) => $q->where('estado', 'perdida'),
            ])
            ->withSum('apuestas as total_apostado', 'monto')
            ->when($request->filled('user_search'), function ($q) use ($request) {
                $search = '%' . $request->user_search . '%';
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('role', 'like', $search);
                });
            })
            ->when($request->filled('user_role'), fn ($q) => $q->where('role', $request->user_role))
            ->when($request->filled('user_vip'), fn ($q) => $q->where('nivel_vip', $request->user_vip))
            ->orderBy($userSort, $userDir)
            ->paginate(10, ['*'], 'usuarios_page')
            ->withQueryString();

        $apuestasQuery = Apuesta::query()
            ->with(['user', 'juego', 'admin'])
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->estado))
            ->when($request->filled('tipo'), fn ($q) => $q->where('tipo', $request->tipo))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('juego_id'), fn ($q) => $q->where('juego_id', $request->juego_id))
            ->when($request->filled('monto_min'), fn ($q) => $q->where('monto', '>=', $request->monto_min))
            ->when($request->filled('monto_max'), fn ($q) => $q->where('monto', '<=', $request->monto_max))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%' . $request->search . '%';
                $q->where(function ($inner) use ($search) {
                    $inner->where('descripcion', 'like', $search)
                        ->orWhere('seleccion', 'like', $search)
                        ->orWhere('resultado', 'like', $search)
                        ->orWhere('estado', 'like', $search)
                        ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $search)->orWhere('email', 'like', $search))
                        ->orWhereHas('juego', fn ($jq) => $jq->where('nombre', 'like', $search));
                });
            })
            ->latest('fecha');

        $apuestas = (clone $apuestasQuery)
            ->paginate(10, ['*'], 'apuestas_page')
            ->withQueryString();

        $predicciones = Apuesta::query()
            ->with(['user', 'juego', 'admin'])
            ->where('tipo', 'prediccion')
            ->when($request->filled('pred_estado'), fn ($q) => $q->where('estado', $request->pred_estado))
            ->when($request->filled('pred_user_id'), fn ($q) => $q->where('user_id', $request->pred_user_id))
            ->when($request->filled('pred_search'), function ($q) use ($request) {
                $search = '%' . $request->pred_search . '%';
                $q->where(fn ($inner) => $inner->where('descripcion', 'like', $search)->orWhere('seleccion', 'like', $search)->orWhere('resultado', 'like', $search));
            })
            ->latest('fecha')
            ->paginate(10, ['*'], 'predicciones_page')
            ->withQueryString();

        $billeteras = Billetera::query()
            ->with('user')
            ->when($request->filled('wallet_search'), function ($q) use ($request) {
                $search = '%' . $request->wallet_search . '%';
                $q->where('moneda', 'like', $search)
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $search)->orWhere('email', 'like', $search));
            })
            ->when($request->filled('wallet_user_id'), fn ($q) => $q->where('user_id', $request->wallet_user_id))
            ->when($request->filled('wallet_min'), fn ($q) => $q->where('saldoDisponible', '>=', $request->wallet_min))
            ->when($request->filled('wallet_max'), fn ($q) => $q->where('saldoDisponible', '<=', $request->wallet_max))
            ->orderByDesc('saldoDisponible')
            ->paginate(10, ['*'], 'billeteras_page')
            ->withQueryString();

        $notificaciones = Notificacion::query()
            ->with('user')
            ->when($request->filled('notif_search'), function ($q) use ($request) {
                $search = '%' . $request->notif_search . '%';
                $q->where('titulo', 'like', $search)
                    ->orWhere('mensaje', 'like', $search)
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', $search)->orWhere('email', 'like', $search));
            })
            ->when($request->filled('notif_user_id'), fn ($q) => $q->where('user_id', $request->notif_user_id))
            ->when($request->filled('notif_tipo'), fn ($q) => $q->where('tipo', $request->notif_tipo))
            ->when($request->filled('notif_leido'), fn ($q) => $q->where('leido', $request->notif_leido === '1'))
            ->latest('fecha')
            ->paginate(10, ['*'], 'notificaciones_page')
            ->withQueryString();

        $juegosAdmin = Juego::query()
            ->withCount('apuestas')
            ->withSum('apuestas as total_apostado', 'monto')
            ->when($request->filled('game_search'), function ($q) use ($request) {
                $search = '%' . $request->game_search . '%';
                $q->where(fn ($inner) => $inner->where('nombre', 'like', $search)->orWhere('categoria', 'like', $search)->orWhere('estado', 'like', $search));
            })
            ->when($request->filled('game_estado'), fn ($q) => $q->where('estado', $request->game_estado))
            ->when($request->filled('game_categoria'), fn ($q) => $q->where('categoria', $request->game_categoria))
            ->orderBy('nombre')
            ->paginate(10, ['*'], 'juegos_page')
            ->withQueryString();

        $chats = Chat::query()
            ->with(['userOne', 'userTwo', 'ultimoMensaje'])
            ->withCount('mensajes')
            ->when($request->filled('chat_search'), function ($q) use ($request) {
                $search = '%' . $request->chat_search . '%';
                $q->where('nombre', 'like', $search)
                    ->orWhereHas('userOne', fn ($uq) => $uq->where('name', 'like', $search)->orWhere('email', 'like', $search))
                    ->orWhereHas('userTwo', fn ($uq) => $uq->where('name', 'like', $search)->orWhere('email', 'like', $search));
            })
            ->when($request->filled('chat_user_id'), function ($q) use ($request) {
                $q->where(fn ($inner) => $inner->where('user_one_id', $request->chat_user_id)->orWhere('user_two_id', $request->chat_user_id)->orWhere('user_id', $request->chat_user_id));
            })
            ->when($request->filled('chat_activo'), fn ($q) => $q->where('activo', $request->chat_activo === '1'))
            ->orderByRaw('COALESCE(last_message_at, updated_at) DESC')
            ->paginate(10, ['*'], 'chats_page')
            ->withQueryString();

        $mensajes = Mensaje::query()
            ->with(['chat', 'emisor', 'receptor'])
            ->when($request->filled('msg_search'), function ($q) use ($request) {
                $search = '%' . $request->msg_search . '%';
                $q->where('contenido', 'like', $search)
                    ->orWhereHas('emisor', fn ($uq) => $uq->where('name', 'like', $search)->orWhere('email', 'like', $search))
                    ->orWhereHas('receptor', fn ($uq) => $uq->where('name', 'like', $search)->orWhere('email', 'like', $search));
            })
            ->when($request->filled('msg_user_id'), function ($q) use ($request) {
                $q->where(fn ($inner) => $inner->where('emisor_id', $request->msg_user_id)->orWhere('receptor_id', $request->msg_user_id));
            })
            ->when($request->filled('msg_chat_id'), fn ($q) => $q->where('chat_id', $request->msg_chat_id))
            ->when($request->filled('msg_editado'), fn ($q) => $q->where('editado', $request->msg_editado === '1'))
            ->latest('created_at')
            ->paginate(10, ['*'], 'mensajes_page')
            ->withQueryString();

        $amigos = DB::table('user_user')
            ->join('users as u1', 'user_user.user_id', '=', 'u1.id')
            ->join('users as u2', 'user_user.friend_id', '=', 'u2.id')
            ->select('user_user.*', 'u1.name as user_name', 'u2.name as friend_name', 'u1.email as user_email', 'u2.email as friend_email')
            ->when($request->filled('friend_user_id'), fn ($q) => $q->where(fn ($inner) => $inner->where('user_user.user_id', $request->friend_user_id)->orWhere('user_user.friend_id', $request->friend_user_id)))
            ->when($request->filled('friend_search'), function ($q) use ($request) {
                $search = '%' . $request->friend_search . '%';
                $q->where(fn ($inner) => $inner->where('u1.name', 'like', $search)->orWhere('u1.email', 'like', $search)->orWhere('u2.name', 'like', $search)->orWhere('u2.email', 'like', $search));
            })
            ->orderByDesc('user_user.created_at')
            ->paginate(10, ['*'], 'amigos_page')
            ->withQueryString();

        $rankingSearch = $request->get('search', '');
        $rankingSort = $request->get('sort', 'posicion');
        $rankingDir = $request->get('dir', 'asc');
        $rankings = Ranking::with('user')
            ->when($rankingSearch, function ($q) use ($rankingSearch) {
                $q->whereHas('user', function ($u) use ($rankingSearch) {
                    $u->where('name', 'like', "%{$rankingSearch}%")
                        ->orWhere('email', 'like', "%{$rankingSearch}%");
                })->orWhere('posicion', 'like', "%{$rankingSearch}%");
            })
            ->orderBy(in_array($rankingSort, ['id', 'posicion', 'puntos', 'total_ganado']) ? $rankingSort : 'posicion', $rankingDir === 'desc' ? 'desc' : 'asc')
            ->paginate(10, ['*'], 'rankings_page')
            ->withQueryString();

        $settings = Setting::query()
            ->when($request->filled('setting_search'), function ($q) use ($request) {
                $search = '%' . $request->setting_search . '%';
                $q->where(fn ($inner) => $inner->where('clave', 'like', $search)->orWhere('valor', 'like', $search)->orWhere('descripcion', 'like', $search));
            })
            ->when($request->filled('setting_activo'), fn ($q) => $q->where('activo', $request->setting_activo === '1'))
            ->orderBy('clave')
            ->paginate(10, ['*'], 'settings_page')
            ->withQueryString();

        $parametros = ParametroGanancia::query()
            ->with('juego')
            ->when($request->filled('param_search'), function ($q) use ($request) {
                $search = '%' . $request->param_search . '%';
                $q->whereHas('juego', fn ($jq) => $jq->where('nombre', 'like', $search));
            })
            ->when($request->filled('param_juego_id'), fn ($q) => $q->where('juego_id', $request->param_juego_id))
            ->orderBy('juego_id')
            ->paginate(10, ['*'], 'parametros_page')
            ->withQueryString();

        $totalApostado = (float) Apuesta::sum('monto');
        $totalGanadoNeto = (float) Apuesta::where('estado', 'ganada')->selectRaw('COALESCE(SUM(monto * (cuota - 1)), 0) as total')->value('total');
        $totalPerdido = (float) Apuesta::where('estado', 'perdida')->sum('monto');
        $balanceCasa = $totalPerdido - $totalGanadoNeto;

        $stats = [
            'usuarios' => User::count(),
            'apuestas_total' => Apuesta::count(),
            'apuestas_pendientes' => Apuesta::whereIn('estado', ['pendiente', 'aceptada'])->count(),
            'predicciones_pendientes' => Apuesta::where('tipo', 'prediccion')->where('estado', 'pendiente')->count(),
            'total_apostado' => $totalApostado,
            'total_ganado_neto' => $totalGanadoNeto,
            'total_perdido' => $totalPerdido,
            'balance_casa' => $balanceCasa,
            'saldo_total' => (float) Billetera::sum('saldoDisponible'),
        ];

        $timelineStart = now()->subDays(13)->startOfDay();
        $timelineBets = Apuesta::query()->where('fecha', '>=', $timelineStart)->get(['monto', 'cuota', 'estado', 'fecha']);
        $timelineByDate = $timelineBets->groupBy(fn ($bet) => optional($bet->fecha)->format('Y-m-d'));
        $adminTimeline = collect(range(0, 13))->map(function ($offset) use ($timelineStart, $timelineByDate) {
            $date = $timelineStart->copy()->addDays($offset);
            $betsForDay = $timelineByDate->get($date->format('Y-m-d'), collect());
            $apostado = (float) $betsForDay->sum('monto');
            $ganadoUsuarios = (float) $betsForDay->where('estado', 'ganada')->sum(fn ($bet) => (float) $bet->monto * ((float) $bet->cuota - 1));
            $perdidoUsuarios = (float) $betsForDay->where('estado', 'perdida')->sum('monto');
            return [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('d/m'),
                'apostado' => round($apostado, 2),
                'ganado_usuarios' => round($ganadoUsuarios, 2),
                'perdido_usuarios' => round($perdidoUsuarios, 2),
                'balance_casa' => round($perdidoUsuarios - $ganadoUsuarios, 2),
                'apuestas' => $betsForDay->count(),
            ];
        })->values();

        $chartRows = [
            ['label' => 'Total apostado', 'value' => $totalApostado, 'hint' => 'Dinero movido en apuestas'],
            ['label' => 'Ganado usuarios', 'value' => $totalGanadoNeto, 'hint' => 'Ganancia neta pagada a usuarios'],
            ['label' => 'Perdido usuarios', 'value' => $totalPerdido, 'hint' => 'Apuestas perdidas por usuarios'],
            ['label' => 'Balance global', 'value' => $balanceCasa, 'hint' => 'Perdido usuarios - ganado usuarios'],
        ];

        $topUsers = User::query()
            ->with('billetera')
            ->withCount('apuestas')
            ->withSum('apuestas as total_apostado', 'monto')
            ->orderByDesc('total_apostado')
            ->take(8)
            ->get();

        $userGameMatrix = DB::table('apuestas')
            ->join('users', 'apuestas.user_id', '=', 'users.id')
            ->join('juegos', 'apuestas.juego_id', '=', 'juegos.id')
            ->selectRaw('users.id as user_id, users.name as user_name, users.email as user_email, juegos.id as juego_id, juegos.nombre as juego_nombre, COUNT(apuestas.id) as total_apuestas, COALESCE(SUM(apuestas.monto),0) as total_apostado, COALESCE(SUM(CASE WHEN apuestas.estado = "ganada" THEN apuestas.monto * (apuestas.cuota - 1) ELSE 0 END),0) as ganado_usuarios, COALESCE(SUM(CASE WHEN apuestas.estado = "perdida" THEN apuestas.monto ELSE 0 END),0) as perdido_usuarios')
            ->groupBy('users.id', 'users.name', 'users.email', 'juegos.id', 'juegos.nombre')
            ->orderByDesc('total_apostado')
            ->limit(15)
            ->get();

        return view('layouts.admin', compact(
            'section', 'stats', 'usuarios', 'apuestas', 'predicciones', 'billeteras',
            'notificaciones', 'juegos', 'juegosAdmin', 'allUsers', 'topUsers', 'tipos',
            'chartRows', 'adminTimeline', 'rankings', 'usuariosAdmin', 'categorias',
            'notificationTypes', 'chats', 'mensajes', 'amigos', 'settings', 'parametros',
            'allChats', 'userGameMatrix'
        ));
    }

    public function userSummary(User $user)
    {
        $this->ensureAdmin();

        $user->load('billetera');
        $bets = $user->apuestas()->with('juego')->get();
        $byGame = $bets
            ->groupBy(fn ($apuesta) => optional($apuesta->juego)->nombre ?? ('Juego #' . $apuesta->juego_id))
            ->map(function ($bets, $gameName) {
                $first = $bets->first();
                $gananciaNeta = (float) $bets->where('estado', 'ganada')->sum(fn ($b) => (float) $b->monto * ((float) $b->cuota - 1));
                $perdidaNeta = (float) $bets->where('estado', 'perdida')->sum('monto');
                return [
                    'juego_id' => $first?->juego_id,
                    'nombre' => $gameName,
                    'total' => $bets->count(),
                    'apostado' => round((float) $bets->sum('monto'), 2),
                    'ganadas' => $bets->where('estado', 'ganada')->count(),
                    'perdidas' => $bets->where('estado', 'perdida')->count(),
                    'pendientes' => $bets->whereIn('estado', ['pendiente', 'aceptada'])->count(),
                    'ganancia_neta' => round($gananciaNeta, 2),
                    'perdida_neta' => round($perdidaNeta, 2),
                    'balance_casa' => round($perdidaNeta - $gananciaNeta, 2),
                ];
            })
            ->values();

        $gananciaNeta = (float) $bets->where('estado', 'ganada')->sum(fn ($b) => (float) $b->monto * ((float) $b->cuota - 1));
        $perdidaNeta = (float) $bets->where('estado', 'perdida')->sum('monto');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'saldo' => round((float) optional($user->billetera)->saldoDisponible, 2),
            ],
            'resumen' => [
                'total_apuestas' => $bets->count(),
                'total_apostado' => round((float) $bets->sum('monto'), 2),
                'ganadas' => $bets->where('estado', 'ganada')->count(),
                'perdidas' => $bets->where('estado', 'perdida')->count(),
                'pendientes' => $bets->whereIn('estado', ['pendiente', 'aceptada'])->count(),
                'ganancia_neta' => round($gananciaNeta, 2),
                'perdida_neta' => round($perdidaNeta, 2),
                'balance_neto' => round($gananciaNeta - $perdidaNeta, 2),
                'balance_casa' => round($perdidaNeta - $gananciaNeta, 2),
            ],
            'por_juego' => $byGame,
        ]);
    }

    public function gameSummary(Juego $juego)
    {
        $this->ensureAdmin();

        $bets = $juego->apuestas()->with('user')->get();
        $gananciaUsuarios = (float) $bets->where('estado', 'ganada')->sum(fn ($b) => (float) $b->monto * ((float) $b->cuota - 1));
        $perdidoUsuarios = (float) $bets->where('estado', 'perdida')->sum('monto');

        $users = $bets
            ->groupBy('user_id')
            ->map(function ($bets, $userId) {
                $first = $bets->first();
                $ganancia = (float) $bets->where('estado', 'ganada')->sum(fn ($b) => (float) $b->monto * ((float) $b->cuota - 1));
                $perdida = (float) $bets->where('estado', 'perdida')->sum('monto');
                return [
                    'user_id' => (int) $userId,
                    'name' => $first?->user?->name ?? ('Usuario #' . $userId),
                    'email' => $first?->user?->email ?? '',
                    'total_apuestas' => $bets->count(),
                    'total_apostado' => round((float) $bets->sum('monto'), 2),
                    'ganadas' => $bets->where('estado', 'ganada')->count(),
                    'perdidas' => $bets->where('estado', 'perdida')->count(),
                    'pendientes' => $bets->whereIn('estado', ['pendiente', 'aceptada'])->count(),
                    'ganancia_neta' => round($ganancia, 2),
                    'perdida_neta' => round($perdida, 2),
                    'balance_casa' => round($perdida - $ganancia, 2),
                ];
            })
            ->sortByDesc('total_apostado')
            ->values();

        return response()->json([
            'juego' => [
                'id' => $juego->id,
                'nombre' => $juego->nombre,
                'categoria' => $juego->categoria,
                'estado' => $juego->estado,
            ],
            'resumen' => [
                'total_apuestas' => $bets->count(),
                'usuarios_unicos' => $bets->pluck('user_id')->unique()->count(),
                'total_apostado' => round((float) $bets->sum('monto'), 2),
                'ganado_usuarios' => round($gananciaUsuarios, 2),
                'perdido_usuarios' => round($perdidoUsuarios, 2),
                'balance_casa' => round($perdidoUsuarios - $gananciaUsuarios, 2),
                'pendientes' => $bets->whereIn('estado', ['pendiente', 'aceptada'])->count(),
            ],
            'usuarios' => $users,
        ]);
    }

    public function resolvePrediction(Request $request, Apuesta $apuesta)
    {
        $this->ensureAdmin();

        $data = $request->validate([
            'action' => ['required', 'in:aceptar,rechazar,ganada,perdida'],
            'resultado' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($apuesta->tipo !== 'prediccion') {
            return back()->with('error', 'Esta acción solo se puede usar con apuestas de tipo predicción.');
        }

        try {
            DB::transaction(function () use ($apuesta, $data) {
                $bet = Apuesta::whereKey($apuesta->id)->lockForUpdate()->firstOrFail();
                $user = User::whereKey($bet->user_id)->firstOrFail();
                $wallet = Billetera::firstOrCreate(['user_id' => $user->id], ['saldoDisponible' => 0, 'moneda' => 'EUR']);
                $wallet = Billetera::whereKey($wallet->id)->lockForUpdate()->first();
                $balanceBefore = (float) $wallet->saldoDisponible;
                $action = $data['action'];
                $resultado = $data['resultado'] ?: null;

                if ($action === 'aceptar') {
                    if ($bet->estado !== 'pendiente') {
                        throw new \RuntimeException('Solo se pueden aceptar predicciones pendientes.');
                    }
                    $bet->estado = 'aceptada';
                    $bet->resultado = $resultado ?: 'Aceptada por administración';
                    $bet->admin_id = auth()->id();
                    $bet->save();
                    Notificacion::crearNotificacion($user->id, 'Predicción aceptada', 'Tu predicción ha sido aceptada y queda pendiente de resolución.', 'apuesta');
                    return;
                }

                if ($action === 'rechazar') {
                    if ($bet->estado !== 'pendiente') {
                        throw new \RuntimeException('Solo se pueden rechazar predicciones pendientes.');
                    }
                    $wallet->saldoDisponible = $balanceBefore + (float) $bet->monto;
                    $wallet->save();
                    $bet->estado = 'rechazada';
                    $bet->resultado = $resultado ?: 'Rechazada por administración';
                    $bet->balance_despues = $wallet->saldoDisponible;
                    $bet->admin_id = auth()->id();
                    $bet->resuelta_at = now();
                    $bet->save();
                    Notificacion::crearNotificacion($user->id, 'Predicción rechazada', 'Tu predicción fue rechazada y la apuesta ha sido reembolsada.', 'apuesta');
                    return;
                }

                if (!in_array($bet->estado, ['aceptada', 'pendiente'], true)) {
                    throw new \RuntimeException('Esta predicción ya está resuelta o rechazada.');
                }

                if ($action === 'ganada') {
                    $premio = (float) $bet->monto * (float) $bet->cuota;
                    $wallet->saldoDisponible = $balanceBefore + $premio;
                    $wallet->save();
                    $bet->estado = 'ganada';
                    $bet->resultado = $resultado ?: 'Predicción cumplida';
                    $bet->balance_despues = $wallet->saldoDisponible;
                    $message = 'Tu predicción ha sido marcada como ganada. Premio abonado: ' . number_format($premio, 2, ',', '.') . ' EUR.';
                } else {
                    $bet->estado = 'perdida';
                    $bet->resultado = $resultado ?: 'Predicción no cumplida';
                    $bet->balance_despues = $wallet->saldoDisponible;
                    $message = 'Tu predicción ha sido marcada como perdida.';
                }

                $bet->admin_id = auth()->id();
                $bet->resuelta_at = now();
                $bet->save();

                Notificacion::crearNotificacion($user->id, 'Predicción resuelta', $message, 'apuesta');
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Predicción actualizada correctamente.');
    }
}
