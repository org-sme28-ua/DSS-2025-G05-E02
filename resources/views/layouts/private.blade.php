<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Zona privada') | Bookie 2.0</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --app-bg: #1a0505;
            --sidebar-bg: #6f2f2f;
            --sidebar-pill: rgba(45, 10, 10, 0.58);
            --surface: #2a0a0a;
            --surface-strong: #3a1313;
            --border: rgba(255, 255, 255, 0.10);
            --text: #f7ecec;
            --muted: #d9bbbb;
            --gold: #f0c040;
            --danger: #ef9a9a;
            --success: #b8efc5;
            --warning: #ffe08a;
            --shadow: 0 18px 40px rgba(0, 0, 0, 0.20);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            background: var(--app-bg);
            overflow: hidden;
        }

        .private-layout {
            display: flex;
            height: 100vh;
        }

        .private-sidebar {
            width: 252px;
            min-width: 252px;
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            padding: 24px 18px;
            gap: 26px;
            height: 100vh;
            position: sticky;
            top: 0;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 8px;
        }

        .sidebar-logo-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--gold);
            color: #4b1717;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: var(--shadow);
        }

        .sidebar-brand-text {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-height: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 11px 14px;
            border-radius: 999px;
            color: #fff7f2;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: background .18s ease, transform .18s ease;
            font-family: inherit;
            text-align: left;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.10);
            transform: translateX(1px);
        }

        .sidebar-link.active {
            background: var(--sidebar-pill);
        }

        .sidebar-icon {
            width: 18px;
            text-align: center;
            font-size: 16px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 8px;
        }

        .sidebar-footer form {
            margin: 0;
        }

        .sidebar-button {
            color: #ffe5d9;
        }

        .private-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .private-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 12px 24px;
            border-bottom: 1px solid var(--border);
            background: #210707;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .topbar-title {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            color: var(--gold);
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 600;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #c0392b, #e74c3c);
            border: 2px solid var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
        }

        .private-content {
            flex: 1;
            padding: 28px;
        }

        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 24px;
        }

        .page-title {
            margin: 0;
            font-family: 'Playfair Display', serif;
            font-size: clamp(32px, 5vw, 46px);
            color: #ffffff;
        }

        .page-subtitle {
            margin: 8px 0 0;
            max-width: 760px;
            color: var(--muted);
            line-height: 1.6;
        }

        .panel {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            box-shadow: var(--shadow);
        }

        .panel-highlight {
            background:
                linear-gradient(135deg, rgba(240, 192, 64, 0.22), rgba(255, 255, 255, 0.05)),
                var(--surface-strong);
        }

        .label {
            margin: 0 0 10px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .muted {
            color: var(--muted);
            line-height: 1.6;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }

        .stat-card {
            background: var(--surface-strong);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px;
        }

        .stat-value {
            margin-top: 8px;
            font-size: 28px;
            font-weight: 700;
            color: #ffffff;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: minmax(280px, 1.15fr) minmax(260px, 0.85fr);
            gap: 18px;
            align-items: stretch;
        }

        .balance {
            margin: 0;
            font-size: clamp(42px, 7vw, 72px);
            font-weight: 900;
            line-height: 1;
            color: var(--gold);
        }

        .currency {
            font-size: 20px;
            color: #ffffff;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 12px 18px;
            background: var(--gold);
            color: #4b1717;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            font-family: inherit;
        }

        .btn.secondary {
            background: rgba(255, 255, 255, 0.10);
            color: #ffffff;
        }

        .stack {
            display: grid;
            gap: 18px;
        }

        .list {
            display: grid;
            gap: 12px;
        }

        .list-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 16px;
            border-radius: 14px;
            background: var(--surface-strong);
            border: 1px solid var(--border);
        }

        .list-item strong,
        .table td strong {
            color: #ffffff;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.10);
            font-size: 12px;
            font-weight: 700;
        }

        .badge.ganada,
        .badge.leida,
        .badge.activo {
            color: var(--success);
        }

        .badge.pendiente {
            color: var(--warning);
        }

        .badge.perdida,
        .badge.no-leida,
        .badge.inactivo {
            color: var(--danger);
        }

        .table-wrap {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .table th {
            font-size: 12px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .empty-state {
            margin: 0;
            padding: 18px;
            border-radius: 14px;
            background: var(--surface-strong);
            border: 1px solid var(--border);
            color: var(--muted);
        }

        @media (max-width: 920px) {
            body {
                overflow: auto;
            }

            .private-layout {
                flex-direction: column;
                height: auto;
            }

            .private-sidebar {
                width: auto;
                min-width: 0;
                height: auto;
                position: static;
            }

            .hero-grid {
                grid-template-columns: 1fr;
            }

            .private-main {
                height: auto;
                overflow: visible;
            }

            .private-topbar {
                padding: 14px 18px;
            }

            .private-content {
                padding: 20px 18px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php($activeNav = trim($__env->yieldContent('active_nav', 'lobby')))

    <div class="private-layout">
        @include('partials.private-sidebar', ['activeNav' => $activeNav])

        <div class="private-main">
            <div class="private-topbar">
                <span class="topbar-title">@yield('topbar_title', 'Lobby')</span>
                <div class="user-chip">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                    <span>{{ auth()->user()->name }}</span>
                </div>
            </div>

            <main class="private-content">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
