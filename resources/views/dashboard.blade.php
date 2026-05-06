@extends('layouts.private')

@section('title', 'Lobby')
@section('topbar_title', 'Lobby')
@section('active_nav', 'lobby')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Lobby</h1>
            <p class="page-subtitle">
                Este es el punto de entrada de la zona privada. Desde aqui el usuario accede a sus apuestas,
                notificaciones, billetera y conversaciones.
            </p>
        </div>
    </div>

    <div class="stack">
        <section class="stats-grid">
            <article class="stat-card">
                <p class="label">Saldo disponible</p>
                <div class="stat-value">{{ number_format((float) (auth()->user()->billetera->saldoDisponible ?? 0), 2, ',', '.') }} EUR</div>
            </article>
            <article class="stat-card">
                <p class="label">Apuestas activas</p>
                <div class="stat-value">{{ auth()->user()->apuestas()->where('estado', 'pendiente')->count() }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Notificaciones sin leer</p>
                <div class="stat-value">{{ auth()->user()->notificaciones()->where('leido', false)->count() }}</div>
            </article>
            <article class="stat-card">
                <p class="label">Chats activos</p>
                <div class="stat-value">{{ auth()->user()->chats()->where('activo', true)->count() }}</div>
            </article>
        </section>

        <section class="hero-grid">
            <article class="panel panel-highlight">
                <p class="label">Accesos rapidos</p>
                <div class="actions">
                    <a class="btn" href="{{ route('private.apuestas') }}">Mis apuestas</a>
                    <a class="btn secondary" href="{{ route('private.notificaciones') }}">Notificaciones</a>
                    <a class="btn secondary" href="{{ route('billetera') }}">Billetera</a>
                    <a class="btn secondary" href="{{ route('private.chat') }}">Chat</a>
                </div>
            </article>

            <article class="panel">
                <p class="label">Cuenta</p>
                <div class="list">
                    <div class="list-item">
                        <div>
                            <strong>{{ auth()->user()->name }}</strong>
                            <div class="muted">{{ auth()->user()->email }}</div>
                        </div>
                        <span class="badge activo">{{ strtoupper(auth()->user()->role) }}</span>
                    </div>
                </div>
            </article>
        </section>

        @if (auth()->user()->role === 'admin')
            <section class="panel">
                <p class="label">Administracion</p>
                <a class="btn" href="{{ route('admin.panel') }}">Entrar al panel de administracion</a>
            </section>
        @endif
    </div>
@endsection
