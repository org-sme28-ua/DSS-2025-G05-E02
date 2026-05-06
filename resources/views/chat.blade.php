@extends('layouts.private')

@section('title', 'Chat')
@section('topbar_title', 'Chat')
@section('active_nav', 'chat')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Chat</h1>
            <p class="page-subtitle">Resumen de conversaciones activas y mensajes recientes del usuario.</p>
        </div>
    </div>

    <div class="stack">
        <section class="hero-grid">
            <article class="panel">
                <p class="label">Chats</p>

                @if ($chats->isEmpty())
                    <p class="empty-state">Todavia no hay chats activos para este usuario.</p>
                @else
                    <div class="list">
                        @foreach ($chats as $chat)
                            <article class="list-item">
                                <div>
                                    <strong>{{ $chat->nombre }}</strong>
                                    <div class="muted">{{ $chat->mensajes_count }} mensajes</div>
                                </div>
                                <span class="badge {{ $chat->activo ? 'activo' : 'inactivo' }}">
                                    {{ $chat->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </article>
                        @endforeach
                    </div>
                @endif
            </article>

            <article class="panel">
                <p class="label">Mensajes recientes</p>

                @if ($mensajes->isEmpty())
                    <p class="empty-state">Todavia no hay mensajes para mostrar.</p>
                @else
                    <div class="list">
                        @foreach ($mensajes as $mensaje)
                            <article class="list-item">
                                <div>
                                    <strong>{{ $mensaje->chat->nombre ?? 'Chat' }}</strong>
                                    <div class="muted">{{ $mensaje->contenido }}</div>
                                    <div class="muted">{{ $mensaje->fechaHora ? \Illuminate\Support\Carbon::parse($mensaje->fechaHora)->format('d/m/Y H:i') : '-' }}</div>
                                </div>
                                <span class="badge">{{ $mensaje->emisor->name ?? 'Usuario' }}</span>
                            </article>
                        @endforeach
                    </div>
                @endif
            </article>
        </section>
    </div>
@endsection
