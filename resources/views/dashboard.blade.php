@extends('layouts.private')

@section('title', 'Dashboard')
@section('topbar_title', 'Dashboard')
@section('active_nav', 'dashboard')

@section('content')
    @php
        $money = fn ($value) => number_format((float) $value, 2, ',', '.') . ' EUR';
        $chartValues = [
            'Saldo' => max(0, (float) ($dashboardStats['saldo'] ?? 0)),
            'Apostado' => max(0, (float) ($dashboardStats['total_apostado'] ?? 0)),
            'Ganancias' => max(0, (float) ($dashboardStats['ganancia_neta'] ?? 0)),
            'Pérdidas' => max(0, (float) ($dashboardStats['perdida_neta'] ?? 0)),
        ];
        $chartMax = max(1, max($chartValues));
    @endphp

    <style>
        .dashboard-chart { display:grid; grid-template-columns:repeat(4,minmax(90px,1fr)); gap:14px; align-items:end; min-height:240px; padding-top:12px; }
        .chart-col { display:grid; gap:10px; align-items:end; min-height:220px; }
        .chart-bar-wrap { height:160px; display:flex; align-items:end; justify-content:center; border-radius:14px; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.08); padding:8px; }
        .chart-bar { width:100%; max-width:62px; min-height:8px; border-radius:12px 12px 6px 6px; background:linear-gradient(180deg,var(--gold),rgba(240,192,64,.35)); box-shadow:0 12px 28px rgba(0,0,0,.25); }
        .chart-label { text-align:center; color:var(--muted); font-size:13px; font-weight:800; }
        .chart-value { text-align:center; color:#fff; font-size:12px; }
        .mini-status { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:12px; }
        @media (max-width:700px){ .dashboard-chart{grid-template-columns:1fr 1fr;} }
    </style>

    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">
                Resumen privado de tu saldo, apuestas activas, ganancias y pérdidas. Los juegos están ahora en su propia pestaña.
            </p>
        </div>
    </div>

    <div class="stack">
        <section class="stats-grid">
            <article class="stat-card">
                <p class="label">Saldo disponible</p>
                <div class="stat-value">{{ $money($dashboardStats['saldo'] ?? 0) }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Apuestas activas</p>
                <div class="stat-value">{{ $dashboardStats['apuestas_activas'] ?? 0 }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Ganadas</p>
                <div class="stat-value">{{ $dashboardStats['apuestas_ganadas'] ?? 0 }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Perdidas</p>
                <div class="stat-value">{{ $dashboardStats['apuestas_perdidas'] ?? 0 }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Notificaciones nuevas</p>
                <div class="stat-value">{{ $dashboardStats['notificaciones_nuevas'] ?? 0 }}</div>
            </article>
        </section>

        <section class="hero-grid">
            <article class="panel panel-highlight">
                <p class="label">Gráfico de balance</p>
                <h2 style="margin:0 0 8px; color:#fff;">Actividad económica</h2>
                <p class="muted">Compara saldo actual, total apostado, ganancias netas y pérdidas acumuladas.</p>

                <div class="dashboard-chart" aria-label="Gráfico de actividad del usuario">
                    @foreach ($chartValues as $label => $value)
                        @php($height = max(6, round(($value / $chartMax) * 100)))
                        <div class="chart-col">
                            <div class="chart-bar-wrap"><div class="chart-bar" style="height: {{ $height }}%;"></div></div>
                            <div class="chart-label">{{ $label }}</div>
                            <div class="chart-value">{{ $money($value) }}</div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="panel">
                <p class="label">Balance neto</p>
                <p class="balance">{{ $money($dashboardStats['balance_neto'] ?? 0) }}</p>
                <div class="mini-status">
                    <div class="list-item"><span>Ganancia neta</span><strong>{{ $money($dashboardStats['ganancia_neta'] ?? 0) }}</strong></div>
                    <div class="list-item"><span>Pérdida neta</span><strong>{{ $money($dashboardStats['perdida_neta'] ?? 0) }}</strong></div>
                    <div class="list-item"><span>Total apostado</span><strong>{{ $money($dashboardStats['total_apostado'] ?? 0) }}</strong></div>
                </div>
                <div class="actions">
                    <a class="btn" href="{{ route('private.games') }}">Ir a juegos</a>
                    <a class="btn secondary" href="{{ route('billetera') }}">Ver billetera</a>
                </div>
            </article>
        </section>

        <section class="hero-grid">
            <article class="panel">
                <p class="label">Últimas apuestas</p>
                @if ($recentBets->isEmpty())
                    <p class="empty-state">Todavía no hay apuestas registradas.</p>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr><th>Juego</th><th>Detalle</th><th>Monto</th><th>Estado</th><th>Fecha</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($recentBets as $bet)
                                    <tr>
                                        <td>{{ $bet->juego->nombre ?? ('Juego #' . $bet->juego_id) }}</td>
                                        <td>{{ $bet->descripcion ?: ($bet->seleccion ?: '-') }}</td>
                                        <td>{{ $money($bet->monto) }}</td>
                                        <td><span class="badge {{ $bet->estado }}">{{ $bet->estadoEtiqueta() }}</span></td>
                                        <td>{{ $bet->fecha ? $bet->fecha->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </article>

            <article class="panel panel-highlight">
                <p class="label">Accesos rápidos</p>
                <div class="actions">
                    <a class="btn" href="{{ route('private.games') }}">Juegos</a>
                    <a class="btn secondary" href="{{ route('private.apuestas') }}">Mis apuestas</a>
                    <a class="btn secondary" href="{{ route('private.notificaciones') }}">Notificaciones</a>
                    <a class="btn secondary" href="{{ route('private.chat') }}">Chat</a>
                    @if (auth()->user()->role === 'admin')
                        <a class="btn secondary" href="{{ route('admin.panel') }}">Panel admin</a>
                    @endif
                </div>
            </article>
        </section>
    </div>
@endsection
