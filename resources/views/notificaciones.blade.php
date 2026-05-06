@extends('layouts.private')

@section('title', 'Notificaciones')
@section('topbar_title', 'Notificaciones')
@section('active_nav', 'notificaciones')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Notificaciones</h1>
            <p class="page-subtitle">Centro de avisos del usuario con el estado de lectura y el contenido recibido.</p>
        </div>
    </div>

    <div class="stack">
        <section class="stats-grid">
            <article class="stat-card">
                <p class="label">Total</p>
                <div class="stat-value">{{ $notificaciones->count() }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Sin leer</p>
                <div class="stat-value">{{ $notificaciones->where('leido', false)->count() }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Leidas</p>
                <div class="stat-value">{{ $notificaciones->where('leido', true)->count() }}</div>
            </article>
        </section>

        <section class="panel">
            <p class="label">Bandeja</p>

            @if ($notificaciones->isEmpty())
                <p class="empty-state">No hay notificaciones disponibles para este usuario.</p>
            @else
                <div class="list">
                    @foreach ($notificaciones as $notificacion)
                        <article class="list-item">
                            <div>
                                <strong>{{ $notificacion->titulo }}</strong>
                                <div class="muted">{{ $notificacion->mensaje }}</div>
                                <div class="muted">{{ $notificacion->fecha ? \Illuminate\Support\Carbon::parse($notificacion->fecha)->format('d/m/Y H:i') : '-' }}</div>
                            </div>
                            <span class="badge {{ $notificacion->leido ? 'leida' : 'no-leida' }}">
                                {{ $notificacion->leido ? 'Leida' : 'Nueva' }}
                            </span>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
