<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Bookie 2.0</title>
    <style>
        :root {
            --bg: #8c3434;
            --bg-dark: #641f1f;
            --card: #2a0a0a;
            --text: #fff7f2;
            --muted: #e6caca;
            --gold: #f3c64b;
            --danger-bg: #fee2e2;
            --danger: #991b1b;
        }
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, var(--bg), var(--bg-dark));
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 22px;
            color: var(--text);
        }
        .card {
            width: 100%;
            max-width: 430px;
            background: rgba(42,10,10,.88);
            padding: 30px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,.14);
            box-shadow: 0 20px 60px rgba(0,0,0,.28);
        }
        .back {
            display: inline-block;
            margin-bottom: 18px;
            color: var(--muted);
            text-decoration: none;
            font-size: 14px;
        }
        h1 { margin: 0 0 8px; font-size: 28px; }
        .subtitle { margin: 0 0 24px; color: var(--muted); line-height: 1.4; }
        label { display: block; margin-bottom: 6px; font-weight: 700; }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 13px;
            margin-bottom: 15px;
            border: 1px solid rgba(255,255,255,.20);
            border-radius: 10px;
            background: rgba(255,255,255,.10);
            color: var(--text);
        }
        input:focus { outline: 2px solid rgba(243,198,75,.35); }
        .row { display: flex; align-items: center; gap: 8px; margin-bottom: 18px; }
        button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 999px;
            background: var(--gold);
            color: #351010;
            font-weight: 900;
            cursor: pointer;
        }
        .error {
            background: var(--danger-bg);
            color: var(--danger);
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 16px;
        }
        .footer { margin-top: 18px; text-align: center; color: var(--muted); }
        .footer a { color: var(--gold); font-weight: 800; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <a class="back" href="{{ route('public.home') }}">← Volver a la página pública</a>
        <h1>Iniciar sesión</h1>
        <p class="subtitle">Entra con tu correo y contraseña para acceder a la zona privada.</p>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf

            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required autofocus>

            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" autocomplete="current-password" required>

            <div class="row">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember" style="margin:0;font-weight:400;">Recordarme</label>
            </div>

            <button type="submit">Entrar</button>
        </form>

        <p class="footer">¿Todavía no tienes cuenta? <a href="{{ route('register') }}">Regístrate</a></p>
    </div>
</body>
</html>
