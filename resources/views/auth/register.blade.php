<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | Bookie 2.0</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
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
            max-width: 470px;
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
        input {
            width: 100%;
            padding: 12px 13px;
            margin-bottom: 15px;
            border: 1px solid rgba(255,255,255,.20);
            border-radius: 10px;
            background: rgba(255,255,255,.10);
            color: var(--text);
        }
        input:focus { outline: 2px solid rgba(243,198,75,.35); }
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
        .error ul { margin: 0; padding-left: 18px; }
        .hint { margin-top: -8px; margin-bottom: 14px; color: var(--muted); font-size: 13px; }
        .footer { margin-top: 18px; text-align: center; color: var(--muted); }
        .footer a { color: var(--gold); font-weight: 800; text-decoration: none; }
    </style>
</head>
<body>
    <div class="card">
        <a class="back" href="{{ route('public.home') }}">← Volver a la página pública</a>
        <h1>Crear cuenta</h1>
        <p class="subtitle">Crea tu cuenta de jugador. Al registrarte se creará también tu billetera ficticia inicial.</p>

        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}">
            @csrf

            <label for="name">Nombre de usuario</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" autocomplete="name" required autofocus>

            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="email" required>

            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" autocomplete="new-password" required>
            <div class="hint">Mínimo 8 caracteres.</div>

            <label for="password_confirmation">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password" required>

            <button type="submit">Registrarme</button>
        </form>

        <p class="footer">¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
    </div>
</body>
</html>
