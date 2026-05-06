@extends('layouts.private')

@section('title', 'Mis apuestas')
@section('topbar_title', 'Mis apuestas')
@section('active_nav', 'apuestas')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Mis apuestas</h1>
            <p class="page-subtitle">Historial del usuario con el detalle de monto, cuota, estado y fecha.</p>
        </div>
    </div>

    <div class="stack">
        <section class="stats-grid">
            <article class="stat-card">
                <p class="label">Total</p>
                <div class="stat-value">{{ $apuestas->count() }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Pendientes</p>
                <div class="stat-value">{{ $apuestas->where('estado', 'pendiente')->count() }}</div>
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
                <p class="empty-state">Todavia no hay apuestas registradas para este usuario.</p>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Juego</th>
                                <th>Monto</th>
                                <th>Cuota</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($apuestas as $apuesta)
                                <tr>
                                    <td>{{ $apuesta->juego->nombre ?? ('Juego #' . $apuesta->juego_id) }}</td>
                                    <td>{{ number_format((float) $apuesta->monto, 2, ',', '.') }} EUR</td>
                                    <td>{{ number_format((float) $apuesta->cuota, 2, ',', '.') }}</td>
                                    <td><span class="badge {{ $apuesta->estado }}">{{ ucfirst($apuesta->estado) }}</span></td>
                                    <td>{{ $apuesta->fecha ? \Illuminate\Support\Carbon::parse($apuesta->fecha)->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
