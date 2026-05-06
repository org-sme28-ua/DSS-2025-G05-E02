<aside class="private-sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo-icon">&#127920;</div>
        <span class="sidebar-brand-text">Bookie 2.0</span>
    </div>

    <nav class="sidebar-nav">
        <a class="sidebar-link {{ $activeNav === 'lobby' ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <span class="sidebar-icon">&#128202;</span>
            <span>Lobby</span>
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
        </a>
    </nav>

    <div class="sidebar-footer">
        <a class="sidebar-link {{ $activeNav === 'configuracion' ? 'active' : '' }}" href="{{ route('private.configuracion') }}">
            <span class="sidebar-icon">&#9881;</span>
            <span>Configuracion</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="sidebar-link sidebar-button" type="submit">
                <span class="sidebar-icon">&#128682;</span>
                <span>Cerrar sesion</span>
            </button>
        </form>
    </div>
</aside>
