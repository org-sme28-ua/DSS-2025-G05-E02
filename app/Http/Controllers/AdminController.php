<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Apuesta;
use App\Models\Billetera;
use App\Models\Juego;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ranking;
use App\Models\RankingSemanal;
use Carbon\CarbonImmutable;

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

        $section = $request->get('section', 'resumen');
        $juegos = Juego::orderBy('nombre')->get();
        $allUsers = User::orderBy('name')->get(['id', 'name', 'email']);

        $usuarios = User::query()
            ->with('billetera')
            ->withCount([
                'apuestas',
                'apuestas as apuestas_pendientes_count' => fn ($q) => $q->whereIn('estado', ['pendiente', 'aceptada']),
                'apuestas as apuestas_ganadas_count' => fn ($q) => $q->where('estado', 'ganada'),
                'apuestas as apuestas_perdidas_count' => fn ($q) => $q->where('estado', 'perdida'),
            ])
            ->withSum('apuestas as total_apostado', 'monto')
            ->orderBy('id')
            ->paginate(8, ['*'], 'usuarios_page')
            ->withQueryString();

        $apuestasQuery = Apuesta::query()
            ->with(['user', 'juego', 'admin'])
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->estado))
            ->when($request->filled('tipo'), fn ($q) => $q->where('tipo', $request->tipo))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('juego_id'), fn ($q) => $q->where('juego_id', $request->juego_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%' . $request->search . '%';
                $q->where(function ($inner) use ($search) {
                    $inner->where('descripcion', 'like', $search)
                        ->orWhere('seleccion', 'like', $search)
                        ->orWhere('resultado', 'like', $search)
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
            ->latest('fecha')
            ->paginate(10, ['*'], 'predicciones_page')
            ->withQueryString();

        $billeteras = Billetera::query()
            ->with('user')
            ->orderByDesc('saldoDisponible')
            ->paginate(10, ['*'], 'billeteras_page')
            ->withQueryString();

        $notificaciones = Notificacion::query()
            ->with('user')
            ->latest('fecha')
            ->paginate(8, ['*'], 'notificaciones_page')
            ->withQueryString();

        $juegosAdmin = Juego::query()
            ->withCount('apuestas')
            ->withSum('apuestas as total_apostado', 'monto')
            ->orderBy('nombre')
            ->paginate(8, ['*'], 'juegos_page')
            ->withQueryString();

        $totalApostado = (float) Apuesta::sum('monto');
        $totalGanadoNeto = (float) Apuesta::where('estado', 'ganada')
            ->selectRaw('COALESCE(SUM(monto * (cuota - 1)), 0) as total')
            ->value('total');
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
        $timelineBets = Apuesta::query()
            ->where('fecha', '>=', $timelineStart)
            ->get(['monto', 'cuota', 'estado', 'fecha']);

        $timelineByDate = $timelineBets->groupBy(fn ($bet) => optional($bet->fecha)->format('Y-m-d'));

        $adminTimeline = collect(range(0, 13))->map(function ($offset) use ($timelineStart, $timelineByDate) {
            $date = $timelineStart->copy()->addDays($offset);
            $dateKey = $date->format('Y-m-d');
            $betsForDay = $timelineByDate->get($dateKey, collect());

            $apostado = (float) $betsForDay->sum('monto');
            $ganadoUsuarios = (float) $betsForDay
                ->where('estado', 'ganada')
                ->sum(fn ($bet) => (float) $bet->monto * ((float) $bet->cuota - 1));
            $perdidoUsuarios = (float) $betsForDay
                ->where('estado', 'perdida')
                ->sum('monto');

            return [
                'date' => $dateKey,
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

        $tipos = Apuesta::query()
            ->select('tipo')
            ->whereNotNull('tipo')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo');

        $rankingSearch = $request->get('search', '');
        $rankingSort   = $request->get('sort', 'posicion');
        $rankingDir    = $request->get('dir', 'asc');

        $rankings = Ranking::with('user')
            ->when($rankingSearch, function ($q) use ($rankingSearch) {
                $q->whereHas('user', function ($u) use ($rankingSearch) {
                    $u->where('name', 'like', "%{$rankingSearch}%")
                      ->orWhere('email', 'like', "%{$rankingSearch}%");
                })->orWhere('posicion', 'like', "%{$rankingSearch}%");
            })
            ->orderBy(
                in_array($rankingSort, ['id', 'posicion', 'puntos', 'total_ganado']) ? $rankingSort : 'posicion',
                $rankingDir === 'desc' ? 'desc' : 'asc'
            )
            ->paginate(10, ['*'], 'rankings_page')
            ->withQueryString();

        $usuariosAdmin = User::orderBy('name')->get(['id', 'name', 'email']);


        return view('layouts.admin', compact(
            'section',
            'stats',
            'usuarios',
            'apuestas',
            'predicciones',
            'billeteras',
            'notificaciones',
            'juegos',
            'juegosAdmin',
            'allUsers',
            'topUsers',
            'tipos',
            'chartRows',
            'adminTimeline',
            'rankings',     
            'usuariosAdmin'
        ));
    }

    public function userSummary(User $user)
    {
        $this->ensureAdmin();

        $user->load('billetera');

        $byGame = $user->apuestas()
            ->with('juego')
            ->get()
            ->groupBy(fn ($apuesta) => optional($apuesta->juego)->nombre ?? ('Juego #' . $apuesta->juego_id))
            ->map(function ($bets, $gameName) {
                return [
                    'nombre' => $gameName,
                    'total' => $bets->count(),
                    'apostado' => round((float) $bets->sum('monto'), 2),
                    'ganadas' => $bets->where('estado', 'ganada')->count(),
                    'perdidas' => $bets->where('estado', 'perdida')->count(),
                    'pendientes' => $bets->whereIn('estado', ['pendiente', 'aceptada'])->count(),
                    'ganancia_neta' => round((float) $bets->where('estado', 'ganada')->sum(fn ($b) => $b->monto * ($b->cuota - 1)), 2),
                    'perdida_neta' => round((float) $bets->where('estado', 'perdida')->sum('monto'), 2),
                ];
            })
            ->values();

        $bets = $user->apuestas;
        $gananciaNeta = $bets->where('estado', 'ganada')->sum(fn ($b) => $b->monto * ($b->cuota - 1));
        $perdidaNeta = $bets->where('estado', 'perdida')->sum('monto');

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
                'ganancia_neta' => round((float) $gananciaNeta, 2),
                'perdida_neta' => round((float) $perdidaNeta, 2),
                'balance_neto' => round((float) ($gananciaNeta - $perdidaNeta), 2),
            ],
            'por_juego' => $byGame,
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
                $wallet = Billetera::firstOrCreate(
                    ['user_id' => $user->id],
                    ['saldoDisponible' => 0, 'moneda' => 'EUR']
                );

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

                    Notificacion::crearNotificacion(
                        $user->id,
                        'Predicción aceptada',
                        'Tu predicción ha sido aceptada y queda pendiente de resolución.',
                        'apuesta'
                    );

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

                    Notificacion::crearNotificacion(
                        $user->id,
                        'Predicción rechazada',
                        'Tu predicción fue rechazada y la apuesta ha sido reembolsada.',
                        'apuesta'
                    );

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

                Notificacion::crearNotificacion(
                    $user->id,
                    'Predicción resuelta',
                    $message,
                    'apuesta'
                );
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Predicción actualizada correctamente.');
    }
 public function generarTopSemanal()
    {
        $this->ensureAdmin();

        $hoy   = \Carbon\CarbonImmutable::now();
        $semana = (int) $hoy->format('W');   // número ISO de semana
        $anio   = (int) $hoy->format('o');   // año ISO (puede diferir del año del calendario en sem. 1 y 53)

        $inicioSemana = $hoy->startOfWeek();  // lunes
        $finSemana    = $hoy->endOfWeek();    // domingo

        // ── 1. Sacar el Top 5 actual del ranking global ──────────────────────
        $top5 = \App\Models\Ranking::with('user')
            ->orderBy('posicion')
            ->take(5)
            ->get();

        if ($top5->isEmpty()) {
            return redirect()
                ->route('admin.panel', ['section' => 'rankings'])
                ->with('error', 'No hay jugadores en el ranking todavía.');
        }

        // ── 2. Borrar snapshot anterior de esta misma semana (idempotente) ───
        \App\Models\RankingSemanal::where('semana', $semana)
            ->where('anio', $anio)
            ->delete();

        // ── 3. Insertar el nuevo Top 5 ───────────────────────────────────────
        foreach ($top5 as $idx => $r) {
            \App\Models\RankingSemanal::create([
                'semana'       => $semana,
                'anio'         => $anio,
                'fecha_inicio' => $inicioSemana->toDateString(),
                'fecha_fin'    => $finSemana->toDateString(),
                'posicion'     => $idx + 1,
                'user_id'      => $r->user_id,
                'puntos'       => $r->puntos,
                'total_ganado' => $r->total_ganado,
            ]);
        }

        // ── 4. Mantener solo las últimas 4 semanas ───────────────────────────
        // Construimos la lista de las 4 semanas más recientes (anio+semana desc)
        $semanas = \App\Models\RankingSemanal::select('anio', 'semana')
            ->distinct()
            ->orderByDesc('anio')
            ->orderByDesc('semana')
            ->get();

        if ($semanas->count() > 4) {
            $aEliminar = $semanas->slice(4); // semanas sobrantes
            foreach ($aEliminar as $s) {
                \App\Models\RankingSemanal::where('anio', $s->anio)
                    ->where('semana', $s->semana)
                    ->delete();
            }
        }

        return redirect()
            ->route('admin.panel', ['section' => 'rankings'])
            ->with('success', "Top 5 de la semana {$semana}/{$anio} guardado correctamente.");
    }
}
