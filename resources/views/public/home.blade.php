<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookie 2.0</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-16x16.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('assets/favicon/favicon-48x48.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/favicon/favicon-96x96.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/favicon/android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('assets/favicon/android-chrome-512x512.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/casino/favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <style>
        :root {
            --bg: #8c3434;
            --bg-dark: #641f1f;
            --panel: #2a0a0a;
            --panel-strong: #3a1212;
            --panel-soft: rgba(255,255,255,.10);
            --text: #fff7f2;
            --muted: #e6caca;
            --gold: #f3c64b;
            --accent: #c44949;
            --border: rgba(255,255,255,.16);
            --shadow: 0 24px 70px rgba(0,0,0,.32);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 20% 10%, rgba(243,198,75,.18), transparent 28%),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,.10), transparent 30%),
                linear-gradient(135deg, var(--bg), var(--bg-dark));
        }

        a { color: inherit; }

        .landing {
            width: min(1180px, calc(100% - 44px));
            margin: 0 auto;
            padding: 28px 0 48px;
        }

        .top-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 28px;
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 900;
            letter-spacing: .3px;
            text-decoration: none;
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
            box-shadow: 0 14px 26px rgba(0,0,0,.25);
        }

        .nav-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-items: center;
        }

        .nav-link {
            color: var(--muted);
            text-decoration: none;
            font-weight: bold;
            margin-right: 15px;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--gold);
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(320px, 1.05fr) minmax(320px, .95fr);
            gap: 28px;
            align-items: center;
            margin-bottom: 34px;
            animation: fadeUp .46s ease both;
        }

        .card {
            background: rgba(42,10,10,.74);
            border: 1px solid var(--border);
            border-radius: 28px;
            box-shadow: var(--shadow);
            padding: 42px;
            backdrop-filter: blur(10px);
        }

        .hero h1 {
            margin: 0 0 16px;
            font-size: clamp(42px, 6vw, 76px);
            line-height: .92;
            letter-spacing: -.04em;
        }

        .hero p,
        .section-lead {
            margin: 0 0 30px;
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
            font-weight: 900;
            transition: transform .16s ease, background .16s ease, box-shadow .16s ease;
            border: 1px solid rgba(255,255,255,.20);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px rgba(0,0,0,.20);
        }

        .btn-primary {
            background: var(--gold);
            color: #351010;
            border-color: var(--gold);
            animation: goldPulse 2.4s ease-in-out infinite;
        }

        .btn-secondary {
            background: var(--panel-soft);
            color: var(--text);
        }

        .image-frame {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            border: 2px solid rgba(243,198,75,.52);
            outline: 1px solid rgba(255,255,255,.14);
            outline-offset: -8px;
            box-shadow: var(--shadow);
            background: rgba(255,255,255,.08);
        }

        .image-frame img {
            display: block;
            width: 100%;
            height: 100%;
            min-height: 420px;
            object-fit: cover;
            transition: transform .6s ease, filter .6s ease;
        }

        .image-frame:hover img {
            transform: scale(1.05);
            filter: saturate(1.12) contrast(1.08);
        }

        .image-caption {
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 18px;
            border: 1px solid rgba(255,255,255,.18);
            background: rgba(42,10,10,.76);
            backdrop-filter: blur(8px);
            font-weight: 900;
        }

        .section {
            margin-top: 34px;
            animation: fadeUp .52s ease both;
        }

        .section-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 18px;
        }

        .section-title {
            margin: 0;
            font-size: clamp(28px, 4vw, 44px);
            line-height: 1;
        }

        .media-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .media-card {
            background: rgba(42,10,10,.68);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 12px;
            box-shadow: 0 18px 45px rgba(0,0,0,.24);
            transition: transform .2s ease, border-color .2s ease;
        }

        .media-card:hover {
            transform: translateY(-5px);
            border-color: rgba(243,198,75,.52);
        }

        .media-card .image-frame {
            border-radius: 18px;
            box-shadow: none;
        }

        .media-card img {
            min-height: 220px;
        }

        .media-card h3 {
            margin: 14px 8px 6px;
            color: #fff;
        }

        .media-card p {
            margin: 0 8px 10px;
            color: var(--muted);
            line-height: 1.5;
        }

        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .review-card {
            border: 1px solid rgba(243,198,75,.36);
            border-radius: 24px;
            padding: 22px;
            background:
                linear-gradient(145deg, rgba(243,198,75,.10), transparent 42%),
                rgba(42,10,10,.72);
            box-shadow: 0 18px 45px rgba(0,0,0,.24);
            min-height: 190px;
            transition: transform .2s ease, border-color .2s ease;
        }

        .review-card:hover {
            transform: translateY(-4px);
            border-color: rgba(243,198,75,.66);
        }

        .review-score {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            color: var(--gold);
            font-size: 20px;
            font-weight: 900;
        }

        .review-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .review-name {
            margin-top: 18px;
            color: #fff;
            font-weight: 900;
        }

        .review-photo-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-top: 18px;
        }

        .review-photo-card {
            border: 1px solid rgba(243,198,75,.36);
            border-radius: 24px;
            padding: 12px;
            background: rgba(42,10,10,.72);
            box-shadow: 0 18px 45px rgba(0,0,0,.24);
            transition: transform .2s ease, border-color .2s ease;
        }

        .review-photo-card:hover {
            transform: translateY(-4px);
            border-color: rgba(243,198,75,.66);
        }

        .review-photo-card .image-frame {
            border-radius: 18px;
            box-shadow: none;
        }

        .review-photo-card img {
            min-height: 260px;
        }

        .review-photo-caption {
            padding: 14px 10px 8px;
            color: var(--muted);
            line-height: 1.5;
        }

        .review-photo-caption strong {
            display: block;
            color: #fff;
            margin-bottom: 4px;
        }

        .footer {
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--muted);
            font-size: 14px;
        }

        .footer a {
            color: var(--gold);
            text-decoration: none;
            font-weight: bold;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes goldPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(243,198,75,.28); }
            50% { box-shadow: 0 0 0 10px rgba(243,198,75,0), 0 14px 26px rgba(243,198,75,.18); }
        }

        @media (max-width: 900px) {
            .hero,
            .media-grid,
            .reviews-grid,
            .review-photo-grid {
                grid-template-columns: 1fr;
            }

            .card { padding: 28px; }
            .image-frame img { min-height: 300px; }
            .footer { flex-direction: column; gap: 15px; text-align: center; }
        }

        @media (prefers-reduced-motion: reduce) {
            .btn-primary { animation: none; }
            .hero, .section { animation: none; }
        }

        @media (max-width: 560px) {
            .landing { width: min(100% - 28px, 1180px); padding-top: 18px; }
            .top-nav { align-items: flex-start; flex-direction: column; gap: 20px; }
            .nav-actions { justify-content: flex-start; width: 100%; }
            .nav-link { margin-bottom: 10px; width: 100%; }
            .btn { width: 100%; }
            .actions { width: 100%; }
        }
    </style>
</head>
<body>
    <main class="landing">
        <nav class="top-nav" aria-label="Navegación principal">
            <a class="logo" href="{{ route('public.home') }}">
                <span class="logo-icon">🎰</span>
                <span>Bookie 2.0</span>
            </a>
            <div class="nav-actions">
                <a class="nav-link" href="{{ route('public.about') }}">Sobre Nosotros</a>
                <a class="btn btn-secondary" href="{{ route('login') }}">Iniciar sesión</a>
                <a class="btn btn-primary" href="{{ route('register') }}">Registrarse</a>
            </div>
        </nav>

        <section class="hero">
            <article class="card">
                <h1>Casino rápido y social</h1>
                <p>
                    Entra a una zona privada con ruleta, predicciones, dados, cara o cruz, billetera, rankings, notificaciones y chat entre usuarios.
                </p>
                <div class="actions">
                    <a class="btn btn-primary" href="{{ route('register') }}">Crear cuenta</a>
                    <a class="btn btn-secondary" href="{{ route('login') }}">Ya tengo cuenta</a>
                </div>
            </article>

            <aside class="image-frame" aria-label="Imagen principal de casino">
                <img src="{{ asset('assets/casino/home-hero.jpg') }}" alt="Ambiente de casino Bookie 2.0">
                <div class="image-caption">
                    <span>Juega con tu saldo</span>
                    <span>⭐ 10/10</span>
                </div>
            </aside>
        </section>

        <section class="section">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Qué encontrarás dentro</h2>
                    <p class="section-lead">Una experiencia visual con estética de casino, juegos rápidos y gestión completa de usuario.</p>
                </div>
            </div>

            <div class="media-grid">
                <article class="media-card">
                    <div class="image-frame">
                        <img src="{{ asset('assets/casino/home-casino-1.jpg') }}" alt="Ruleta y luces de casino">
                    </div>
                    <h3>Juegos rápidos</h3>
                    <p>Ruleta, dados y moneda con resultados inmediatos y balance actualizado.</p>
                </article>

                <article class="media-card">
                    <div class="image-frame">
                        <img src="{{ asset('assets/casino/home-casino-2.jpg') }}" alt="Cartas y fichas de casino">
                    </div>
                    <h3>Predicciones</h3>
                    <p>Crea predicciones con tu saldo y deja que el administrador las revise.</p>
                </article>

                <article class="media-card">
                    <div class="image-frame">
                        <img src="{{ asset('assets/casino/home-casino-3.jpg') }}" alt="Fichas de casino sobre mesa">
                    </div>
                    <h3>Panel social</h3>
                    <p>Chat, notificaciones, rankings y una billetera integrada en la experiencia.</p>
                </article>
            </div>
        </section>

        <section class="section">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Reviews de usuarios</h2>
                    <p class="section-lead">Opiniones de la comunidad sobre la experiencia Bookie 2.0.</p>
                </div>
            </div>

            <div class="reviews-grid">
                <article class="review-card">
                    <div class="review-score">10/10 <span>★★★★★</span></div>
                    <p>“La ruleta se siente clara y directa. Me gusta que el saldo cambie al momento y que pueda revisar mis apuestas después.”</p>
                    <div class="review-name">Marcos R.</div>
                </article>

                <article class="review-card">
                    <div class="review-score">9/10 <span>★★★★★</span></div>
                    <p>“El sistema de predicciones con revisión del administrador aporta control y hace que cada apuesta tenga más contexto.”</p>
                    <div class="review-name">Laura M.</div>
                </article>

                <article class="review-card">
                    <div class="review-score">10/10 <span>★★★★★</span></div>
                    <p>“La parte privada reúne juegos, chat, rankings y billetera sin perder el estilo de casino del proyecto.”</p>
                    <div class="review-name">Sergio P.</div>
                </article>
            </div>

            <div class="review-photo-grid">
                <article class="review-photo-card">
                    <div class="image-frame">
                        <img src="{{ asset('assets/casino/review-user-1.jpg') }}" alt="Usuario satisfecho de Bookie 2.0">
                    </div>
                    <div class="review-photo-caption">
                        <strong>Comunidad activa</strong>
                        Usuarios compartiendo jugadas, resultados y conversaciones dentro de la plataforma.
                    </div>
                </article>
                <article class="review-photo-card">
                    <div class="image-frame">
                        <img src="{{ asset('assets/casino/review-user-2.jpg') }}" alt="Usuario valorando Bookie 2.0">
                    </div>
                    <div class="review-photo-caption">
                        <strong>Experiencia completa</strong>
                        Juegos, billetera, rankings, predicciones y chat reunidos en un mismo entorno.
                    </div>
                </article>
            </div>
        </section>
        
        <footer class="footer">
            <div>
                &copy; {{ date('Y') }} Bookie 2.0. Juega con responsabilidad.
            </div>
            <div>
                <a href="{{ route('public.about') }}">Conoce más sobre nosotros</a>
            </div>
        </footer>
    </main>
</body>
</html>