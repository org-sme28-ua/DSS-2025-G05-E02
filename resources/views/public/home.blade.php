<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookie 2.0</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <style>
        :root {
            --bg: #8c3434;
            --bg-dark: #641f1f;
            --panel: #2a0a0a;
            --panel-soft: rgba(255,255,255,.10);
            --text: #fff7f2;
            --muted: #e6caca;
            --gold: #f3c64b;
            --accent: #c44949;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 20% 10%, rgba(243,198,75,.20), transparent 28%),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,.12), transparent 30%),
                linear-gradient(135deg, var(--bg), var(--bg-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
        }
        .hero {
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 28px;
            align-items: center;
        }
        .card {
            background: rgba(42,10,10,.72);
            border: 1px solid rgba(255,255,255,.16);
            border-radius: 28px;
            box-shadow: 0 24px 70px rgba(0,0,0,.32);
            padding: 42px;
            backdrop-filter: blur(10px);
        }
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
            font-weight: 800;
            letter-spacing: .3px;
        }
        .logo-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: var(--gold);
            color: #3b0f0f;
            font-size: 26px;
        }
        h1 {
            margin: 0 0 16px;
            font-size: clamp(38px, 6vw, 68px);
            line-height: .95;
        }
        p {
            margin: 0 0 30px;
            max-width: 620px;
            color: var(--muted);
            font-size: 18px;
            line-height: 1.55;
        }
        .actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 48px;
            padding: 0 22px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 800;
            transition: transform .16s, background .16s;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-primary {
            background: var(--gold);
            color: #351010;
        }
        .btn-secondary {
            background: var(--panel-soft);
            color: var(--text);
            border: 1px solid rgba(255,255,255,.20);
        }
        .preview {
            display: grid;
            gap: 14px;
        }
        .preview-box {
            min-height: 112px;
            border-radius: 20px;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.16);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px;
            color: var(--text);
            font-size: 22px;
            font-weight: 900;
        }
        .preview-box span:last-child { font-size: 34px; }
        @media (max-width: 760px) {
            .hero { grid-template-columns: 1fr; }
            .card { padding: 28px; }
        }
    </style>
</head>
<body>
    <main class="hero">
        <section class="card">
            <div class="logo">
                <span class="logo-icon">🎰</span>
                <span>Bookie 2.0</span>
            </div>
            <h1>Tu lobby de apuestas ficticias</h1>
            <p>
                Esta es la página pública. Cualquiera puede verla sin iniciar sesión. Para entrar en la zona privada de juegos, chats, rankings y billetera, primero hay que acceder o crear una cuenta.
            </p>
            <div class="actions">
                <a class="btn btn-primary" href="{{ route('login') }}">Iniciar sesión</a>
                <a class="btn btn-secondary" href="{{ route('register') }}">Registrarse</a>
            </div>
        </section>

        <aside class="preview" aria-label="Próximas secciones privadas">
            <div class="preview-box"><span>Ruleta</span><span>🎯</span></div>
            <div class="preview-box"><span>Apuestas</span><span>⚽</span></div>
            <div class="preview-box"><span>Bingo</span><span>🔢</span></div>
            <div class="preview-box"><span>Slot</span><span>🎰</span></div>
        </aside>
    </main>
</body>
</html>
