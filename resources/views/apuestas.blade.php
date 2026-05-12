@extends('layouts.private')

@section('title', 'Mis apuestas')
@section('topbar_title', 'Mis apuestas')
@section('active_nav', 'apuestas')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Mis apuestas</h1>
            <p class="page-subtitle">Historial general de tus apuestas, predicciones y juegos disponibles.</p>
        </div>
    </div>

    <div class="stack">
        <section class="stats-grid">
            <article class="stat-card">
                <p class="label">Total</p>
                <div class="stat-value">{{ $apuestas->count() }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Pendientes / aceptadas</p>
                <div class="stat-value">{{ $apuestas->whereIn('estado', ['pendiente', 'aceptada'])->count() }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Ganadas</p>
                <div class="stat-value">{{ $apuestas->where('estado', 'ganada')->count() }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Perdidas</p>
                <div class="stat-value">{{ $apuestas->where('estado', 'perdida')->count() }}</div>
            </article>
        </section>

        <section class="panel">
            <p class="label">Listado</p>

            @if ($apuestas->isEmpty())
                <p class="empty-state">Todavía no hay apuestas registradas para este usuario.</p>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Juego</th>
                                <th>Detalle</th>
                                <th>Monto</th>
                                <th>Cuota</th>
                                <th>Estado</th>
                                <th>Resultado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($apuestas as $apuesta)
                                <tr>
                                    <td>
                                        <strong>{{ $apuesta->juego->nombre ?? ('Juego #' . $apuesta->juego_id) }}</strong>
                                        <div class="muted">{{ ucfirst($apuesta->tipo ?? 'general') }}</div>
                                    </td>
                                    <td>
                                        @if ($apuesta->descripcion)
                                            <div>{{ $apuesta->descripcion }}</div>
                                        @endif
                                        @if ($apuesta->seleccion)
                                            <div class="muted">Selección: {{ $apuesta->seleccion }}</div>
                                        @endif
                                    </td>
                                    <td>{{ number_format((float) $apuesta->monto, 2, ',', '.') }} EUR</td>
                                    <td>{{ number_format((float) $apuesta->cuota, 2, ',', '.') }}</td>
                                    <td><span class="badge {{ $apuesta->estado }}">{{ $apuesta->estadoEtiqueta() }}</span></td>
                                    <td>{{ $apuesta->resultado ?: '-' }}</td>
                                    <td>{{ $apuesta->fecha ? $apuesta->fecha->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
