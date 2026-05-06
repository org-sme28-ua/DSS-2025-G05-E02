<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zona privada | Bookie 2.0</title>
    <style>
        :root {
            --bg: #8c3434;
            --bg-dark: #641f1f;
            --sidebar: #4a1010;
            --card: rgba(255,255,255,.10);
            --text: #fff7f2;
            --muted: #e6caca;
            --gold: #f3c64b;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            color: var(--text);
            background: linear-gradient(135deg, var(--bg), var(--bg-dark));
        }
        .layout { display: flex; min-height: 100vh; }
        aside {
            width: 240px;
            background: var(--sidebar);
            padding: 22px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }
        .logo { display: flex; align-items: center; gap: 10px; font-weight: 900; }
        .logo-icon { width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; background: var(--gold); color: #351010; }
        nav { display: grid; gap: 10px; }
        nav a, .logout-btn {
            color: var(--text);
            text-decoration: none;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.10);
            font: inherit;
            text-align: left;
            cursor: pointer;
        }
        main { flex: 1; padding: 28px; }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 28px;
        }
        .user { color: var(--muted); }
        h1 { margin: 0 0 10px; font-size: clamp(32px, 5vw, 54px); }
        .intro { margin: 0 0 28px; max-width: 760px; color: var(--muted); line-height: 1.55; }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
        }
        .card {
            min-height: 170px;
            border-radius: 22px;
            background: var(--card);
            border: 1px solid rgba(255,255,255,.14);
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 18px 45px rgba(0,0,0,.18);
        }
        .card strong { font-size: 24px; }
        .card span { font-size: 38px; }
        .card-link { color: var(--text); text-decoration: none; transition: transform .16s, border-color .16s; }
        .card-link:hover { transform: translateY(-3px); border-color: rgba(243,198,75,.46); }
        .admin-link {
            display: inline-block;
            margin-top: 26px;
            color: #351010;
            background: var(--gold);
            padding: 12px 18px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 900;
        }
        @media (max-width: 780px) {
            .layout { flex-direction: column; }
            aside { width: auto; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <aside>
            <div class="logo"><span class="logo-icon">🎰</span><span>Bookie 2.0</span></div>
            <nav>
                <a href="{{ route('dashboard') }}">🏠 Lobby privado</a>
                <a href="{{ route('roulette.index') }}">🎯 Ruleta</a>
                <a href="#">💬 Chat</a>
                <a href="#">🏆 Rankings</a>
                <a href="#">💳 Billetera</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-btn" type="submit">↩ Cerrar sesión</button>
                </form>
            </nav>
        </aside>

        <main>
            <div class="topbar">
                <div>
                    <h1>Zona privada</h1>
                    <p class="user">Sesión iniciada como {{ auth()->user()->name }}</p>
                </div>
            </div>

            <p class="intro">
                Esta pantalla es el punto de entrada privado. De momento es un placeholder: después aquí podremos conectar el lobby real, chats, juegos, rankings y recargas de dinero ficticio.
            </p>

            <section class="grid">
                <a class="card card-link" href="{{ route('roulette.index') }}"><strong>Ruleta</strong><span>🎯</span></a>
                <article class="card"><strong>Apuestas deportivas</strong><span>⚽</span></article>
                <article class="card"><strong>Bingo</strong><span>🔢</span></article>
                <article class="card"><strong>Slot machine</strong><span>🎰</span></article>
            </section>

            @if (auth()->user()->role === 'admin')
                <a class="admin-link" href="{{ route('admin.panel') }}">Entrar al panel de administración</a>
            @endif
        </main>
    </div>
</body>
</html>
