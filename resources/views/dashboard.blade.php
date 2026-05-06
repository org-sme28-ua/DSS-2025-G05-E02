@extends('layouts.private')

@section('title', 'Lobby')
@section('topbar_title', 'Lobby')
@section('active_nav', 'lobby')

@php
    $user = auth()->user();
    $wallet = $wallet ?? $user->billetera;
    $balance = (float) ($wallet->saldoDisponible ?? 0);
    $recentBets = $recentBets ?? $user->apuestas()->with('juego')->latest('fecha')->take(5)->get();
@endphp

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Lobby</h1>
            <p class="page-subtitle">
                Punto de entrada de la zona privada. Desde aqui puedes acceder a juegos, apuestas,
                notificaciones, billetera y chat.
            </p>
        </div>
    </div>

    <div class="stack">
        <section class="stats-grid">
            <article class="stat-card">
                <p class="label">Saldo disponible</p>
                <div class="stat-value">{{ number_format($balance, 2, ',', '.') }} EUR</div>
            </article>
            <article class="stat-card">
                <p class="label">Apuestas activas</p>
                <div class="stat-value">{{ $user->apuestas()->where('estado', 'pendiente')->count() }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Notificaciones sin leer</p>
                <div class="stat-value">{{ $user->notificaciones()->where('leido', false)->count() }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Chats activos</p>
                <div class="stat-value">{{ $user->chats()->where('activo', true)->count() }}</div>
            </article>
        </section>

        <section class="hero-grid">
            <article class="panel panel-highlight">
                <p class="label">Juegos</p>
                <h2 class="section-title">Elige un juego</h2>
                <p class="muted">
                    La ruleta ya esta conectada a la billetera y a la tabla general de apuestas.
                    Bingo, slot machine y apuestas deportivas quedan preparados como siguientes pantallas.
                </p>
                <div class="game-grid">
                    <a class="game-card" href="{{ route('roulette.index') }}">
                        <span class="game-icon">🎯</span>
                        <strong>Ruleta</strong>
                        <small>Rojo, negro y verde</small>
                    </a>
                    <div class="game-card disabled">
                        <span class="game-icon">⚽</span>
                        <strong>Apuestas deportivas</strong>
                        <small>Proximamente</small>
                    </div>
                    <div class="game-card disabled">
                        <span class="game-icon">🔢</span>
                        <strong>Bingo</strong>
                        <small>Proximamente</small>
                    </div>
                    <div class="game-card disabled">
                        <span class="game-icon">🎰</span>
                        <strong>Slot machine</strong>
                        <small>Proximamente</small>
                    </div>
                </div>
            </article>

            <article class="panel">
                <p class="label">Cuenta</p>
                <div class="list">
                    <div class="list-item">
                        <div>
                            <strong>{{ $user->name }}</strong>
                            <div class="muted">{{ $user->email }}</div>
                        </div>
                        <span class="badge activo">{{ strtoupper($user->role) }}</span>
                    </div>
                    <div class="list-item">
                        <span>Nivel VIP</span>
                        <strong>{{ $user->nivel_vip ?? 0 }}</strong>
                    </div>
                    <div class="list-item">
                        <span>Puntos</span>
                        <strong>{{ $user->puntos_fidelidad ?? 0 }}</strong>
                    </div>
                </div>
                <div class="actions">
                    <a class="btn secondary" href="{{ route('private.apuestas') }}">Mis apuestas</a>
                    <a class="btn secondary" href="{{ route('billetera') }}">Billetera</a>
                </div>
            </article>
        </section>

        <section class="panel">
            <p class="label">Actividad reciente</p>

            @if ($recentBets->isEmpty())
                <p class="empty-state">Todavia no tienes apuestas registradas.</p>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Juego</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentBets as $apuesta)
                                <tr>
                                    <td>{{ $apuesta->juego->nombre ?? ('Juego #' . $apuesta->juego_id) }}</td>
                                    <td>{{ number_format((float) $apuesta->monto, 2, ',', '.') }} EUR</td>
                                    <td><span class="badge {{ $apuesta->estado }}">{{ ucfirst($apuesta->estado) }}</span></td>
                                    <td>{{ $apuesta->fecha ? \Illuminate\Support\Carbon::parse($apuesta->fecha)->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        @if ($user->role === 'admin')
            <section class="panel">
                <p class="label">Administracion</p>
                <a class="btn" href="{{ route('admin.panel') }}">Entrar al panel de administracion</a>
            </section>
        @endif
    </div>
@endsection

@push('styles')
    <style>
        .section-title {
            margin: 0 0 8px;
            font-size: 28px;
            color: #ffffff;
        }

        .game-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 14px;
            margin-top: 22px;
        }

        .game-card {
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 10px;
            padding: 18px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.10);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: #ffffff;
            text-decoration: none;
            transition: transform .16s ease, border-color .16s ease, background .16s ease;
        }

        .game-card:hover {
            transform: translateY(-3px);
            border-color: rgba(240, 192, 64, 0.55);
            background: rgba(255, 255, 255, 0.14);
        }

        .game-card.disabled {
            opacity: .72;
        }

        .game-card.disabled:hover {
            transform: none;
            border-color: rgba(255, 255, 255, 0.14);
        }

        .game-card strong {
            font-size: 19px;
        }

        .game-card small {
            color: var(--muted);
        }

        .game-icon {
            font-size: 34px;
        }
    </style>
@endpush
