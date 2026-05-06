@extends('layouts.private')

@section('title', 'Notificaciones')
@section('topbar_title', 'Notificaciones')
@section('active_nav', 'notificaciones')

@php
    $typeLabels = [
        'apuesta' => 'Apuesta',
        'promo' => 'Promo',
        'alerta' => 'Alerta',
        'chat' => 'Chat',
        'info' => 'Info',
        'mensaje' => 'Mensaje',
        'sistema' => 'Sistema',
    ];

    $typeIcons = [
        'apuesta' => '🎲',
        'promo' => '🎁',
        'alerta' => '⚠️',
        'chat' => '💬',
        'info' => 'ℹ️',
        'mensaje' => '✉️',
        'sistema' => '🔔',
    ];
@endphp

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Notificaciones</h1>
            <p class="page-subtitle">
                Centro de avisos del usuario. Aqui se muestran resultados de apuestas, promociones,
                alertas y mensajes del sistema.
            </p>
        </div>

        @if (($notificacionesSinLeer ?? 0) > 0)
            <form method="POST" action="{{ route('private.notificaciones.markAll') }}">
                @csrf
                <button class="btn" type="submit">Marcar todas como leidas</button>
            </form>
        @endif
    </div>

    <div class="stack">
        @if (session('success'))
            <div class="notice-success">{{ session('success') }}</div>
        @endif

        <section class="stats-grid">
            <article class="stat-card">
                <p class="label">Total</p>
                <div class="stat-value">{{ $totalNotificaciones ?? $notificaciones->count() }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Sin leer</p>
                <div class="stat-value">{{ $notificacionesSinLeer ?? $notificaciones->where('leido', false)->count() }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Leidas</p>
                <div class="stat-value">{{ $notificacionesLeidas ?? $notificaciones->where('leido', true)->count() }}</div>
            </article>
        </section>

        <section class="panel">
            <div class="notifications-toolbar">
                <p class="label">Bandeja</p>
                <div class="filter-tabs" aria-label="Filtros de notificaciones">
                    <a class="filter-tab {{ ($filter ?? 'todas') === 'todas' ? 'active' : '' }}" href="{{ route('private.notificaciones') }}">Todas</a>
                    <a class="filter-tab {{ ($filter ?? '') === 'sin-leer' ? 'active' : '' }}" href="{{ route('private.notificaciones', ['estado' => 'sin-leer']) }}">Sin leer</a>
                    <a class="filter-tab {{ ($filter ?? '') === 'leidas' ? 'active' : '' }}" href="{{ route('private.notificaciones', ['estado' => 'leidas']) }}">Leidas</a>
                </div>
            </div>

            @if ($notificaciones->isEmpty())
                <p class="empty-state">No hay notificaciones disponibles para este filtro.</p>
            @else
                <div class="notification-list">
                    @foreach ($notificaciones as $notificacion)
                        <article class="notification-card {{ $notificacion->leido ? 'read' : 'unread' }}">
                            <div class="notification-icon" aria-hidden="true">{{ $typeIcons[$notificacion->tipo] ?? '🔔' }}</div>

                            <div class="notification-body">
                                <div class="notification-heading">
                                    <strong>{{ $notificacion->titulo }}</strong>
                                    <span class="badge {{ $notificacion->leido ? 'leida' : 'no-leida' }}">
                                        {{ $notificacion->leido ? 'Leida' : 'Nueva' }}
                                    </span>
                                </div>
                                <p>{{ $notificacion->mensaje }}</p>
                                <div class="notification-meta">
                                    <span>{{ $typeLabels[$notificacion->tipo] ?? ucfirst($notificacion->tipo) }}</span>
                                    <span>{{ $notificacion->fecha ? \Illuminate\Support\Carbon::parse($notificacion->fecha)->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                            </div>

                            <div class="notification-actions">
                                @unless ($notificacion->leido)
                                    <form method="POST" action="{{ route('private.notificaciones.read', $notificacion) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn secondary compact" type="submit">Marcar leida</button>
                                    </form>
                                @endunless

                                <form method="POST" action="{{ route('private.notificaciones.destroy', $notificacion) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn secondary compact danger" type="submit">Eliminar</button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection

@push('styles')
    <style>
        .notice-success {
            padding: 14px 16px;
            border-radius: 14px;
            background: rgba(34, 197, 94, 0.13);
            border: 1px solid rgba(34, 197, 94, 0.28);
            color: var(--success);
            font-weight: 700;
        }

        .notifications-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .notifications-toolbar .label {
            margin-bottom: 0;
        }

        .filter-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .filter-tab {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            padding: 8px 12px;
            color: var(--muted);
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--border);
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
        }

        .filter-tab.active {
            color: #4b1717;
            background: var(--gold);
        }

        .notification-list {
            display: grid;
            gap: 12px;
        }

        .notification-card {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 14px;
            align-items: start;
            padding: 16px;
            border-radius: 16px;
            background: var(--surface-strong);
            border: 1px solid var(--border);
        }

        .notification-card.unread {
            border-color: rgba(240, 192, 64, 0.35);
            background: linear-gradient(135deg, rgba(240, 192, 64, 0.10), rgba(255, 255, 255, 0.04)), var(--surface-strong);
        }

        .notification-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: rgba(255, 255, 255, 0.10);
            font-size: 20px;
        }

        .notification-heading {
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .notification-body p {
            margin: 0 0 10px;
            color: var(--muted);
            line-height: 1.55;
        }

        .notification-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            color: var(--muted);
            font-size: 12px;
        }

        .notification-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .notification-actions form {
            margin: 0;
        }

        .btn.compact {
            padding: 8px 11px;
            font-size: 12px;
            white-space: nowrap;
        }

        .btn.danger {
            color: var(--danger);
        }

        @media (max-width: 760px) {
            .notifications-toolbar,
            .notification-card,
            .notification-heading {
                grid-template-columns: 1fr;
                flex-direction: column;
                align-items: stretch;
            }

            .notification-actions {
                flex-direction: row;
                flex-wrap: wrap;
            }
        }
    </style>
@endpush
