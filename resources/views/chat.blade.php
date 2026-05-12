@extends('layouts.private')

@section('title', 'Chat')
@section('topbar_title', 'Chat')
@section('active_nav', 'chat')

@section('content')
    <style>
        .chat-layout {
            display: grid;
            grid-template-columns: minmax(280px, 0.85fr) minmax(360px, 1.45fr);
            gap: 18px;
            min-height: calc(100vh - 150px);
        }

        .chat-sidebar-panel,
        .chat-main-panel {
            min-height: 640px;
        }

        .chat-sidebar-panel {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .chat-main-panel {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .chat-form-card {
            background: var(--surface-strong);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px;
        }

        .chat-list {
            display: grid;
            gap: 10px;
            overflow: auto;
            padding-right: 4px;
        }

        .chat-card {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr) auto;
            gap: 12px;
            align-items: center;
            padding: 13px;
            border: 1px solid var(--border);
            border-radius: 16px;
            color: var(--text);
            text-decoration: none;
            background: rgba(255, 255, 255, 0.04);
            transition: background .18s ease, border-color .18s ease, transform .18s ease;
        }

        .chat-card {
            animation: chatFadeIn .34s ease both;
        }

        .chat-card:hover,
        .chat-card.active {
            background: rgba(240, 192, 64, 0.12);
            border-color: rgba(240, 192, 64, 0.38);
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 12px 26px rgba(0,0,0,.18);
        }

        .chat-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #8e2f2f, #d85445);
            border: 2px solid rgba(240, 192, 64, .55);
            color: #fff;
            font-size: 13px;
            font-weight: 900;
        }

        .chat-name {
            display: block;
            color: #fff;
            font-weight: 800;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-preview {
            color: var(--muted);
            font-size: 13px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-top: 2px;
        }

        .chat-meta {
            display: grid;
            justify-items: end;
            gap: 6px;
            color: var(--muted);
            font-size: 12px;
        }

        .unread-pill {
            min-width: 24px;
            height: 24px;
            border-radius: 999px;
            background: var(--gold);
            color: #4b1717;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 900;
            padding: 0 7px;
        }

        .chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--border);
        }

        .chat-header-user {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .message-list {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow-y: auto;
            padding: 22px 6px 22px 0;
        }

        .message-row {
            display: flex;
        }

        .message-row.mine {
            justify-content: flex-end;
        }

        .message-bubble {
            max-width: min(620px, 78%);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 12px 14px;
            background: var(--surface-strong);
        }

        .message-row.mine .message-bubble {
            background: rgba(240, 192, 64, 0.18);
            border-color: rgba(240, 192, 64, 0.36);
        }

        .message-author {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 5px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
        }

        .message-text {
            white-space: pre-wrap;
            line-height: 1.55;
            color: #fff;
        }

        .chat-composer {
            border-top: 1px solid var(--border);
            padding-top: 16px;
        }

        .suggested-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin: 16px 0 8px;
            color: #fff;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .suggested-heading span:last-child {
            color: var(--gold);
            font-size: 12px;
            text-transform: none;
            letter-spacing: 0;
        }

        .suggested-users {
            display: grid;
            gap: 8px;
            margin-top: 8px;
        }

        .suggested-user {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border: 1px solid var(--border);
            background: rgba(255,255,255,.04);
            color: var(--text);
            border-radius: 12px;
            padding: 10px 12px;
            font: inherit;
            cursor: pointer;
            text-align: left;
        }

        .suggested-user {
            transition: transform .18s ease, border-color .18s ease, background .18s ease, box-shadow .18s ease;
        }

        .suggested-user:hover {
            border-color: rgba(240, 192, 64, .45);
            background: rgba(240, 192, 64, .10);
            transform: translateX(3px);
            box-shadow: 0 10px 22px rgba(0,0,0,.16);
        }

        .message-row {
            animation: chatFadeIn .28s ease both;
        }

        @keyframes chatFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 980px) {
            .chat-layout {
                grid-template-columns: 1fr;
            }

            .chat-sidebar-panel,
            .chat-main-panel {
                min-height: auto;
            }
        }
    </style>

    <div class="page-header">
        <div>
            <h1 class="page-title">Chat</h1>
            <p class="page-subtitle">Mensajes privados entre usuarios. Busca a alguien por email o nombre, abre conversación y empieza a hablar.</p>
        </div>
    </div>

    <div class="stack">
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert error">
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="chat-layout">
            <aside class="panel chat-sidebar-panel">
                <div class="chat-form-card">
                    <p class="label">Nuevo chat</p>
                    <form method="POST" action="{{ route('private.chat.start') }}" class="form-grid">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="recipient">Email o nombre de usuario</label>
                            <input
                                id="recipient"
                                class="form-control"
                                type="text"
                                name="recipient"
                                value="{{ old('recipient') }}"
                                placeholder="ej. lucia@bookie20.test"
                                autocomplete="off"
                            >
                        </div>
                        <button class="btn" type="submit">Abrir chat</button>
                    </form>

                    @if ($suggestedUsers->isNotEmpty())
                        <div class="suggested-heading">
                            <span>Recomendaciones</span>
                            <span>Máx. 4</span>
                        </div>
                        <div class="suggested-users">
                            @foreach ($suggestedUsers as $suggestedUser)
                                <form method="POST" action="{{ route('private.chat.start') }}">
                                    @csrf
                                    <input type="hidden" name="recipient" value="{{ $suggestedUser->email }}">
                                    <button class="suggested-user" type="submit">
                                        <span>
                                            <strong>{{ $suggestedUser->name }}</strong><br>
                                            <span class="muted">{{ $suggestedUser->email }}</span>
                                        </span>
                                        <span class="badge {{ $suggestedUser->role === 'admin' ? 'pendiente' : 'info' }}">{{ $suggestedUser->role }}</span>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <p class="label">Conversaciones</p>

                    @if ($chats->isEmpty())
                        <p class="empty-state">Todavía no tienes conversaciones. Abre un chat usando el buscador de arriba.</p>
                    @else
                        <div class="chat-list">
                            @foreach ($chats as $chat)
                                @php
                                    $other = $chat->other_user;
                                    $initials = $other
                                        ? collect(explode(' ', $other->name))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('')
                                        : 'CH';
                                    $lastMessage = $chat->ultimoMensaje;
                                @endphp
                                <a class="chat-card {{ optional($activeChat)->id === $chat->id ? 'active' : '' }}" href="{{ route('private.chat.show', $chat) }}">
                                    <div class="chat-avatar">{{ strtoupper($initials) }}</div>
                                    <div style="min-width:0;">
                                        <span class="chat-name">{{ $other->name ?? $chat->nombre }}</span>
                                        <div class="chat-preview">
                                            @if ($lastMessage)
                                                {{ $lastMessage->emisor_id === auth()->id() ? 'Tú: ' : '' }}{{ \Illuminate\Support\Str::limit($lastMessage->contenido, 58) }}
                                            @else
                                                Conversación abierta. Envía el primer mensaje.
                                            @endif
                                        </div>
                                    </div>
                                    <div class="chat-meta">
                                        <span>{{ $lastMessage?->created_at?->format('d/m H:i') ?? $chat->created_at?->format('d/m') }}</span>
                                        @if ($chat->unread_count > 0)
                                            <span class="unread-pill">{{ $chat->unread_count }}</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </aside>

            <article class="panel chat-main-panel">
                @if (! $activeChat)
                    <div class="empty-state" style="margin:auto; max-width:520px; text-align:center;">
                        <strong style="display:block; color:#fff; font-size:22px; margin-bottom:8px;">Selecciona o abre una conversación</strong>
                        <span>Cuando entres en un chat, aquí aparecerán los mensajes y el cuadro para escribir.</span>
                    </div>
                @else
                    @php
                        $activeOther = $activeChat->other_user;
                        $activeInitials = $activeOther
                            ? collect(explode(' ', $activeOther->name))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('')
                            : 'CH';
                    @endphp

                    <div class="chat-header">
                        <div class="chat-header-user">
                            <div class="chat-avatar">{{ strtoupper($activeInitials) }}</div>
                            <div style="min-width:0;">
                                <strong class="chat-name">{{ $activeOther->name ?? $activeChat->nombre }}</strong>
                                <div class="muted">{{ $activeOther->email ?? 'Chat privado' }}</div>
                            </div>
                        </div>
                        <span class="badge {{ $activeChat->activo ? 'activo' : 'inactivo' }}">{{ $activeChat->activo ? 'Activo' : 'Inactivo' }}</span>
                    </div>

                    <div class="message-list">
                        @forelse ($messages as $message)
                            <div class="message-row {{ $message->emisor_id === auth()->id() ? 'mine' : '' }}">
                                <div class="message-bubble">
                                    <div class="message-author">
                                        <span>{{ $message->emisor_id === auth()->id() ? 'Tú' : ($message->emisor->name ?? 'Usuario') }}</span>
                                        <span>{{ $message->created_at?->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="message-text">{{ $message->contenido }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state" style="margin:auto; max-width:520px; text-align:center;">
                                <strong style="display:block; color:#fff; font-size:20px; margin-bottom:8px;">Aún no hay mensajes</strong>
                                <span>Escribe el primer mensaje para iniciar la conversación.</span>
                            </div>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('private.chat.message', $activeChat) }}" class="chat-composer">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="contenido">Mensaje</label>
                            <textarea id="contenido" name="contenido" class="form-control" rows="3" placeholder="Escribe tu mensaje...">{{ old('contenido') }}</textarea>
                        </div>
                        <div class="actions" style="margin-top:12px; justify-content:flex-end;">
                            <button class="btn" type="submit">Enviar mensaje</button>
                        </div>
                    </form>
                @endif
            </article>
        </section>
    </div>
@endsection
