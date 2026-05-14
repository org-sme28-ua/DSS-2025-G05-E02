<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nosotros — Bookie 2.0</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #1a0505; 
            --bg-card: #2a0a0a; 
            --bg-card2: #3d1212; 
            --accent: #c0392b; 
            --gold: #f0c040; 
            --text: #f5e6e6;
            --text-muted: #c4a0a0; 
            --border: rgba(255,255,255,.12);
            --radius: 16px;
        }
        
        body { 
            margin: 0; 
            font-family: 'DM Sans', sans-serif; 
            background: var(--bg); 
            color: var(--text); 
            line-height: 1.6;
        }
        
        .navbar {
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            background: var(--bg-card);
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            background: var(--gold);
            color: #1a0505;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 18px;
        }

        .btn-back {
            background: rgba(255,255,255,0.1);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
            transition: 0.2s;
        }

        .btn-back:hover {
            background: rgba(255,255,255,0.2);
        }

        .hero {
            text-align: center;
            padding: 80px 20px;
            background: radial-gradient(circle at center, var(--bg-card2) 0%, var(--bg) 100%);
            border-bottom: 1px solid var(--border);
        }

        .hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 54px;
            color: var(--gold);
            margin: 0 0 20px;
        }

        .hero p {
            font-size: 18px;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }

        .content {
            max-width: 900px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .grid-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
            margin-top: 40px;
        }

        .feature-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 30px;
            text-align: center;
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            border-color: var(--gold);
        }

        .feature-icon {
            font-size: 40px;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            color: white;
            font-size: 20px;
            margin: 0 0 15px;
        }

        .feature-card p {
            color: var(--text-muted);
            font-size: 14px;
            margin: 0;
        }
        


        .contact-panel {
            margin-top: 42px;
            background: linear-gradient(135deg, rgba(240,192,64,.14), rgba(192,57,43,.12));
            border: 1px solid rgba(240,192,64,.35);
            border-radius: var(--radius);
            padding: 28px;
            box-shadow: 0 18px 45px rgba(0,0,0,.22);
        }

        .contact-panel h3 {
            margin: 0 0 14px;
            color: var(--gold);
            font-family: 'Playfair Display', serif;
            font-size: 26px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }

        .contact-item {
            background: rgba(255,255,255,.07);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 16px;
        }

        .contact-label {
            display: block;
            color: var(--text-muted);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 4px;
        }

        .contact-value {
            color: white;
            font-weight: 700;
            word-break: break-word;
        }

        .footer {
            text-align: center;
            padding: 40px;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 60px;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="{{ route('public.home') }}" class="logo">
            <div class="logo-icon">🎰</div>
            Bookie 2.0
        </a>
        <a href="{{ route('public.home') }}" class="btn-back">Volver al Inicio</a>
    </nav>

    <header class="hero">
        <h1>Nuestra Historia</h1>
        <p>Más que un casino, somos la nueva generación del entretenimiento digital. Diseñado para ofrecer la experiencia más segura, rápida y emocionante del mercado.</p>
    </header>

    <main class="content">
        <div style="text-align: center; margin-bottom: 60px;">
            <h2 style="color: white; font-family: 'Playfair Display', serif; font-size: 32px;">¿Qué es Bookie 2.0?</h2>
            <p style="color: var(--text-muted); font-size: 16px; max-width: 700px; margin: 0 auto;">
                Nacimos con una visión clara: revolucionar la forma en la que juegas y apuestas con tus amigos. Hemos construido una plataforma desde cero donde la transparencia, la diversión y la comunidad van de la mano. Aquí no juegas contra una máquina fría, juegas en un entorno diseñado por y para apasionados del riesgo y la estrategia.
            </p>
        </div>

        <div class="grid-features">
            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <h3>Seguridad Absoluta</h3>
                <p>Implementamos los últimos estándares de encriptación y protección de datos para que tu saldo y tus apuestas estén siempre blindados.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🤝</div>
                <h3>Comunidad Privada</h3>
                <p>Añade amigos, crea chats privados y comenta las jugadas en tiempo real. La emoción se multiplica cuando la compartes.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Pagos Instantáneos</h3>
                <p>Nuestra billetera virtual procesa tus ganancias al instante. Sin esperas, sin excusas. Tu dinero está disponible cuando lo necesitas.</p>
            </div>
        </div>

        <section class="contact-panel" aria-labelledby="contacto">
            <h3 id="contacto">Contacto</h3>
            <div class="contact-grid">
                <div class="contact-item">
                    <span class="contact-label">Administrador principal</span>
                    <span class="contact-value">Alfonso Gonzales Espinosa</span>
                </div>
                <div class="contact-item">
                    <span class="contact-label">Correo</span>
                    <span class="contact-value">admin@bookie20.test</span>
                </div>
                <div class="contact-item">
                    <span class="contact-label">Teléfono</span>
                    <span class="contact-value">+34 673 258 262</span>
                </div>
            </div>
        </section>

    </main>

    <footer class="footer">
        &copy; {{ date('Y') }} Bookie 2.0. Juega con responsabilidad.
    </footer>

</body>
</html>