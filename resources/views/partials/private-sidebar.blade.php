@php
    $unreadChatMessages = 0;
    if (auth()->check() && \Illuminate\Support\Facades\Schema::hasColumn('mensajes', 'read_at')) {
        $unreadChatMessages = \App\Models\Mensaje::where('receptor_id', auth()->id())->whereNull('read_at')->count();
    }
@endphp

<aside class="private-sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo-icon">&#127920;</div>
        <span class="sidebar-brand-text">Bookie 2.0</span>
    </div>

    <nav class="sidebar-nav">
        <a class="sidebar-link {{ $activeNav === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <span class="sidebar-icon">&#128202;</span>
            <span>Dashboard</span>
        </a>
        <a class="sidebar-link {{ $activeNav === 'games' ? 'active' : '' }}" href="{{ route('private.games') }}">
            <span class="sidebar-icon">&#127918;</span>
            <span>Juegos</span>
        </a>
        <a class="sidebar-link {{ $activeNav === 'apuestas' ? 'active' : '' }}" href="{{ route('private.apuestas') }}">
            <span class="sidebar-icon">&#128203;</span>
            <span>Mis apuestas</span>
        </a>
        <a class="sidebar-link {{ $activeNav === 'notificaciones' ? 'active' : '' }}" href="{{ route('private.notificaciones') }}">
            <span class="sidebar-icon">&#128276;</span>
            <span>Notificaciones</span>
        </a>
        <a class="sidebar-link {{ $activeNav === 'billetera' ? 'active' : '' }}" href="{{ route('billetera') }}">
            <span class="sidebar-icon">&#128179;</span>
            <span>Billetera</span>
        </a>
        <a class="sidebar-link {{ $activeNav === 'chat' ? 'active' : '' }}" href="{{ route('private.chat') }}">
            <span class="sidebar-icon">&#128172;</span>
            <span>Chat</span>
            @if ($unreadChatMessages > 0)
                <span class="badge pendiente" style="margin-left:auto; padding:3px 8px;">{{ $unreadChatMessages }}</span>
            @endif
        </a>
        {{-- ── Rankings ── --}}
        <a class="sidebar-link {{ $activeNav === 'rankings' ? 'active' : '' }}" href="{{ route('private.rankings') }}">
            <span class="sidebar-icon">&#127942;</span>
            <span>Rankings</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a class="sidebar-link {{ $activeNav === 'configuracion' ? 'active' : '' }}" href="{{ route('private.configuracion') }}">
            <span class="sidebar-icon">&#9881;</span>
            <span>Configuración</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="sidebar-link sidebar-button" type="submit">
                <span class="sidebar-icon">&#128682;</span>
                <span>Cerrar sesión</span>
            </button>
        </form>
    </div>
</aside>
