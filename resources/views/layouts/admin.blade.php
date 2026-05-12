@php
    $money = fn ($value) => number_format((float) $value, 2, ',', '.') . ' EUR';
    $active = fn ($name) => $section === $name ? 'active' : '';
    $statusClass = fn ($estado) => match ($estado) {
        'ganada' => 'badge-success',
        'perdida', 'rechazada' => 'badge-danger',
        'pendiente', 'aceptada' => 'badge-warning',
        default => 'badge-muted',
    };
    $sectionUrl = fn ($name, $extra = []) => route('admin.panel', array_merge(['section' => $name], $extra));
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Bookie 2.0 — Panel de Administración</title>
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
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root {
    --bg:#1a0505; --bg-card:#2a0a0a; --bg-card2:#3d1212; --sidebar:#4a1010;
    --sidebar-active:#2a0606; --accent:#c0392b; --gold:#f0c040; --text:#f5e6e6;
    --text-muted:#c4a0a0; --border:rgba(255,255,255,.12); --success:#2ecc71;
    --danger:#e74c3c; --warning:#f39c12; --info:#3498db; --radius:12px; --radius-sm:8px;
  }
  * { box-sizing:border-box; }
  body { margin:0; font-family:'DM Sans',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }
  a { color:inherit; }
  .admin-layout { display:flex; min-height:100vh; }
  .sidebar { width:252px; min-width:252px; background:var(--sidebar); display:flex; flex-direction:column; position:sticky; top:0; height:100vh; overflow-y:auto; }
  .sidebar-logo { display:flex; align-items:center; gap:10px; padding:20px 20px 16px; border-bottom:1px solid var(--border); }
  .logo-icon { width:40px; height:40px; background:var(--gold); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:20px; }
  .logo-text { font-family:'Playfair Display',serif; font-size:18px; color:white; }
  .logo-badge { margin-left:auto; background:var(--accent); color:white; font-size:10px; font-weight:700; padding:2px 7px; border-radius:20px; }
  .sidebar-section { padding:12px 14px 4px; font-size:10px; font-weight:700; letter-spacing:1.2px; color:var(--text-muted); text-transform:uppercase; }
  .sidebar-nav { flex:1; padding:8px 12px; display:flex; flex-direction:column; gap:4px; }
  .nav-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--radius-sm); color:var(--text-muted); font-size:13px; font-weight:600; text-decoration:none; transition:.15s; }
  .nav-item:hover { background:rgba(255,255,255,.08); color:var(--text); }
  .nav-item.active { background:var(--sidebar-active); color:white; border-left:3px solid var(--gold); }
  .sidebar-bottom { padding:12px; border-top:1px solid var(--border); }
  .main { flex:1; min-width:0; }
  .topbar { background:#2a0808; padding:12px 24px; display:flex; align-items:center; gap:14px; border-bottom:1px solid var(--border); position:sticky; top:0; z-index:10; }
  .topbar-title { font-family:'Playfair Display',serif; font-size:16px; color:var(--gold); }
  .topbar-spacer { flex:1; }
  .user-chip { display:flex; align-items:center; gap:8px; color:var(--text-muted); font-weight:700; font-size:13px; }
  .user-avatar { width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg,#c0392b,#e74c3c); display:flex; align-items:center; justify-content:center; font-weight:900; color:white; border:2px solid var(--gold); }
  .content { padding:24px 28px; }
  .page-header { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:22px; }
  .page-title { margin:0; font-family:'Playfair Display',serif; font-size:30px; color:white; }
  .page-subtitle { margin:5px 0 0; font-size:13px; color:var(--text-muted); max-width:780px; line-height:1.5; }
  .stats-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:14px; margin-bottom:22px; }
  .stat-card { background:var(--bg-card2); border-radius:var(--radius); padding:16px 18px; border:1px solid var(--border); }
  .stat-label { font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase; letter-spacing:.5px; margin-bottom:6px; }
  .stat-value { font-size:25px; font-weight:800; color:white; }
  .grid-2 { display:grid; grid-template-columns:minmax(320px,1.1fr) minmax(280px,.9fr); gap:16px; align-items:start; }
  .panel { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; margin-bottom:18px; animation:adminFadeUp .34s ease both; transition:border-color .18s ease, box-shadow .18s ease, transform .18s ease; }
  .panel:hover { border-color:rgba(240,192,64,.22); box-shadow:0 16px 32px rgba(0,0,0,.18); }
  .panel-pad { padding:18px; }
  .panel-title { margin:0 0 12px; font-size:16px; color:white; font-weight:800; }
  .toolbar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; padding:14px 16px; border-bottom:1px solid var(--border); background:var(--bg-card2); }
  .input-sm { background:rgba(255,255,255,.08); border:1px solid var(--border); border-radius:var(--radius-sm); padding:8px 12px; color:var(--text); font-size:12px; font-family:inherit; }
  .input-sm:focus { outline:none; border-color:rgba(255,255,255,.3); }
  .input-sm::placeholder { color:var(--text-muted); }
  select.input-sm option { background:#2a0a0a; }
  .btn { padding:8px 14px; border-radius:var(--radius-sm); border:1px solid var(--border); background:rgba(255,255,255,.10); color:var(--text); font-family:inherit; font-size:12px; font-weight:700; cursor:pointer; transition:background .15s ease, transform .15s ease, box-shadow .15s ease; display:inline-flex; align-items:center; justify-content:center; gap:6px; text-decoration:none; line-height:1.1; white-space:nowrap; }
  .btn:hover { background:rgba(255,255,255,.18); transform:translateY(-1px); box-shadow:0 8px 16px rgba(0,0,0,.18); }
  .btn-primary { background:var(--accent); border-color:var(--accent); color:white; }
  .btn-gold { background:var(--gold); border-color:var(--gold); color:#3b1212; animation:goldPulse 2.4s ease-in-out infinite; }
  .btn-danger { background:rgba(231,76,60,.18); border-color:rgba(231,76,60,.35); color:#ffadad; }
  .btn-success { background:rgba(46,204,113,.18); border-color:rgba(46,204,113,.35); color:#9ff0bd; }
  .btn-sm { padding:6px 10px; font-size:11px; min-height:30px; }
  .table-scroll { width:100%; overflow-x:auto; }
  table { width:100%; border-collapse:collapse; font-size:13px; }
  th { padding:12px 14px; text-align:left; color:var(--text-muted); font-weight:800; font-size:11px; text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid var(--border); white-space:nowrap; }
  td { padding:12px 14px; border-bottom:1px solid rgba(255,255,255,.06); vertical-align:middle; }
  tr:hover td { background:rgba(255,255,255,.03); }
  .muted { color:var(--text-muted); font-size:12px; line-height:1.45; }
  .badge { display:inline-flex; align-items:center; padding:4px 9px; border-radius:20px; font-size:11px; font-weight:800; letter-spacing:.2px; white-space:nowrap; }
  .badge-success { background:rgba(46,204,113,.18); color:#2ecc71; border:1px solid rgba(46,204,113,.3); }
  .badge-danger { background:rgba(231,76,60,.18); color:#e74c3c; border:1px solid rgba(231,76,60,.3); }
  .badge-warning { background:rgba(243,156,18,.18); color:#f39c12; border:1px solid rgba(243,156,18,.3); }
  .badge-info { background:rgba(52,152,219,.18); color:#3498db; border:1px solid rgba(52,152,219,.3); }
  .badge-muted { background:rgba(255,255,255,.08); color:#aaa; border:1px solid rgba(255,255,255,.1); }
  table th:last-child, table td:last-child { text-align:right; }
  td.actions, td.actions-cell { text-align:right; white-space:nowrap; min-width:260px; width:1%; vertical-align:middle; }
  td.actions { display:table-cell; }
  .action-buttons { display:inline-flex; justify-content:flex-end; align-items:center; gap:6px; flex-wrap:nowrap; min-height:32px; vertical-align:middle; }
  .actions:not(td) { display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end; align-items:center; }
  .inline-form { display:inline-flex; gap:6px; align-items:center; justify-content:flex-end; flex-wrap:nowrap; width:auto; min-height:34px; vertical-align:middle; }
  .inline-form .input-sm { width:170px; max-width:170px; height:32px; }
  .alert { padding:12px 14px; border-radius:var(--radius); margin-bottom:14px; border:1px solid var(--border); background:var(--bg-card2); }
  .alert-success { color:#9ff0bd; } .alert-error { color:#ffadad; }
  .pagination-wrap { padding:12px 16px; background:var(--bg-card2); border-top:1px solid var(--border); }
  .pagination-wrap:empty { display:none; }
  .custom-pagination { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
  .pagination-summary { color:var(--text-muted); font-size:12px; }
  .pagination-buttons { display:flex; align-items:center; gap:6px; flex-wrap:wrap; }
  .page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:34px; min-height:32px; padding:7px 10px; border-radius:10px; background:rgba(255,255,255,.08); border:1px solid var(--border); color:var(--text); text-decoration:none; font-size:12px; font-weight:800; }
  .page-btn:hover { background:rgba(255,255,255,.16); }
  .page-btn.active { background:var(--gold); border-color:var(--gold); color:#3b1212; }
  .page-btn.disabled { opacity:.45; cursor:not-allowed; }
  .admin-timeline { display:grid; gap:14px; margin-top:14px; }
  .timeline-line-chart { position:relative; min-height:300px; padding:14px; border:1px solid var(--border); border-radius:14px; background:rgba(255,255,255,.04); overflow-x:auto; }
  .line-chart-svg { display:block; width:100%; min-width:760px; height:280px; }
  .chart-grid { stroke:rgba(255,255,255,.10); stroke-width:1; }
  .chart-axis { stroke:rgba(255,255,255,.24); stroke-width:1.5; }
  .chart-line { fill:none; stroke-width:3; stroke-linecap:round; stroke-linejoin:round; vector-effect:non-scaling-stroke; }
  .chart-line.apostado { stroke:var(--gold); }
  .chart-line.ganado { stroke:var(--success); }
  .chart-line.perdido { stroke:var(--danger); }
  .chart-point { stroke:#2a0a0a; stroke-width:2; vector-effect:non-scaling-stroke; }
  .chart-point.apostado { fill:var(--gold); }
  .chart-point.ganado { fill:var(--success); }
  .chart-point.perdido { fill:var(--danger); }
  .chart-label { fill:var(--text-muted); font-size:12px; font-weight:800; }
  .chart-value-label { fill:var(--text-muted); font-size:11px; }
  .chart-legend { display:flex; gap:14px; flex-wrap:wrap; color:var(--text-muted); font-size:12px; margin-bottom:8px; }
  .legend-dot { width:10px; height:10px; display:inline-block; border-radius:50%; margin-right:6px; background:var(--gold); }
  .legend-dot.green { background:var(--success); }
  .legend-dot.red { background:var(--danger); }
  .summary-modal { display:none; position:fixed; inset:0; background:rgba(0,0,0,.74); z-index:1000; align-items:center; justify-content:center; padding:20px; }
  .summary-modal.open { display:flex; }
  .modal-box { width:min(760px,96vw); max-height:86vh; overflow:auto; background:var(--bg-card); border:1px solid var(--border); border-radius:18px; padding:22px; box-shadow:0 30px 80px rgba(0,0,0,.35); }
  .modal-head { display:flex; justify-content:space-between; gap:12px; align-items:center; margin-bottom:16px; }
  .mini-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:10px; margin:12px 0; }
  .mini-card { background:var(--bg-card2); border:1px solid var(--border); border-radius:12px; padding:12px; }
  .mini-card b { display:block; font-size:18px; color:white; margin-top:4px; }
  @media (max-width:980px) { .admin-layout { flex-direction:column; } .sidebar { width:auto; min-width:0; height:auto; position:static; } .grid-2 { grid-template-columns:1fr; } .content { padding:18px; } }
  @media (max-width:760px) { .inline-form { flex-wrap:wrap; justify-content:flex-start; } td.actions, td.actions-cell { white-space:normal; min-width:210px; } .action-buttons { flex-wrap:wrap; justify-content:flex-end; } }
  @keyframes adminFadeUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
  @keyframes goldPulse { 0%,100% { box-shadow:0 0 0 0 rgba(240,192,64,.26); } 50% { box-shadow:0 0 0 8px rgba(240,192,64,0), 0 12px 24px rgba(240,192,64,.16); } }
  @media (prefers-reduced-motion: reduce) { .btn-gold { animation:none; } .panel, .stat-card { animation:none; } }
  .stat-card { animation:adminFadeUp .3s ease both; transition:transform .15s ease, border-color .15s ease; }
  .stat-card:hover { transform:translateY(-2px); border-color:rgba(240,192,64,.26); }
</style>
</head>
<body>
<div class="admin-layout">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <a class="nav-item {{ $active('resumen') }}" href="{{ $sectionUrl('resumen') }}">📊 Resumen</a>
      <div class="sidebar-section">Gestión</div>
      <a class="nav-item {{ $active('usuarios') }}" href="{{ $sectionUrl('usuarios') }}">👥 Usuarios</a>
      <a class="nav-item {{ $active('apuestas') }}" href="{{ $sectionUrl('apuestas') }}">📋 Apuestas</a>
      <a class="nav-item {{ $active('predicciones') }}" href="{{ $sectionUrl('predicciones') }}">🔮 Predicciones</a>
      <a class="nav-item {{ $active('juegos') }}" href="{{ $sectionUrl('juegos') }}">🎮 Juegos</a>
      <a class="nav-item {{ $active('billeteras') }}" href="{{ $sectionUrl('billeteras') }}">💳 Billeteras</a>
      <a class="nav-item {{ $active('notificaciones') }}" href="{{ $sectionUrl('notificaciones') }}">🔔 Notificaciones</a>
<a class="nav-item {{ $active('rankings') }}" href="{{ $sectionUrl('rankings') }}">🏆 Rankings</a>
    </nav>
    <div class="sidebar-bottom">
      <a class="nav-item" href="{{ route('dashboard') }}">↩ Volver al dashboard</a>
      <form method="POST" action="{{ route('logout') }}">@csrf<button class="nav-item" style="border:0;background:transparent;width:100%;cursor:pointer;" type="submit">🚪 Cerrar sesión</button></form>
    </div>
  </aside>

  <main class="main">
    <div class="topbar">
      <span class="topbar-title">Panel de Administración</span>
      <div class="topbar-spacer"></div>
      <div class="user-chip"><div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div><span>{{ auth()->user()->name }}</span></div>
    </div>

    <div class="content">
      @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
      @if (session('error'))<div class="alert alert-error">{{ session('error') }}</div>@endif

      <div class="page-header">
        <div>
          <h1 class="page-title">{{ ucfirst($section) }}</h1>
          <p class="page-subtitle">Control de usuarios, apuestas, juegos, predicciones, billeteras y actividad general de la plataforma.</p>
        </div>
      </div>

      <section class="stats-row">
        <article class="stat-card"><div class="stat-label">Usuarios</div><div class="stat-value">{{ $stats['usuarios'] }}</div></article>
        <article class="stat-card"><div class="stat-label">Apuestas totales</div><div class="stat-value">{{ $stats['apuestas_total'] }}</div></article>
        <article class="stat-card"><div class="stat-label">Pendientes</div><div class="stat-value">{{ $stats['apuestas_pendientes'] }}</div></article>
        <article class="stat-card"><div class="stat-label">Predicciones por aceptar</div><div class="stat-value">{{ $stats['predicciones_pendientes'] }}</div></article>
        <article class="stat-card"><div class="stat-label">Total apostado</div><div class="stat-value">{{ $money($stats['total_apostado']) }}</div></article>
        <article class="stat-card"><div class="stat-label">Saldo total usuarios</div><div class="stat-value">{{ $money($stats['saldo_total']) }}</div></article>
      </section>

      @if ($section === 'resumen')
        <div class="grid-2">
          <section class="panel">
            <div class="panel-pad">
              <h2 class="panel-title">Usuarios con más actividad</h2>
              <p class="muted">Pulsa “Resumen” para ver apuestas por juego, dinero apostado, ganado y perdido.</p>
            </div>
            <div class="table-scroll">
              <table>
                <thead><tr><th>Usuario</th><th>Apuestas</th><th>Total apostado</th><th>Saldo</th><th>Acciones</th></tr></thead>
                <tbody>
                  @forelse ($topUsers as $usuario)
                    <tr>
                      <td><strong>{{ $usuario->name }}</strong><div class="muted">{{ $usuario->email }}</div></td>
                      <td>{{ $usuario->apuestas_count }}</td>
                      <td>{{ $money($usuario->total_apostado ?? 0) }}</td>
                      <td>{{ $money(optional($usuario->billetera)->saldoDisponible ?? 0) }}</td>
                      <td class="actions"><div class="action-buttons"><button class="btn btn-sm" onclick="showUserSummary({{ $usuario->id }})">Resumen</button><a class="btn btn-sm" href="{{ $sectionUrl('apuestas', ['user_id' => $usuario->id]) }}">Apuestas</a></div></td>
                    </tr>
                  @empty
                    <tr><td colspan="5" class="muted">No hay usuarios.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </section>

          <section class="panel">
            <div class="panel-pad">
              <h2 class="panel-title">Balance general</h2>
              <div class="mini-grid">
                <div class="mini-card"><span class="muted">Ganado neto usuarios</span><b>{{ $money($stats['total_ganado_neto']) }}</b></div>
                <div class="mini-card"><span class="muted">Perdido usuarios</span><b>{{ $money($stats['total_perdido']) }}</b></div>
                <div class="mini-card"><span class="muted">Balance global casa</span><b>{{ $money($stats['balance_casa']) }}</b></div>
              </div>
              <div class="actions">
                <a class="btn btn-gold" href="{{ $sectionUrl('predicciones', ['pred_estado' => 'pendiente']) }}">Revisar predicciones pendientes</a>
                <a class="btn" href="{{ $sectionUrl('apuestas') }}">Ver todas las apuestas</a>
              </div>
            </div>
          </section>
        </div>

        <section class="panel">
          <div class="panel-pad">
            <h2 class="panel-title">Evolución económica de los últimos 14 días</h2>
            <p class="muted">Evolución temporal de dinero apostado, ganado por usuarios y perdido por usuarios.</p>
            @php
              $timeline = collect($adminTimeline ?? []);
              $chartWidth = 1000;
              $chartHeight = 280;
              $leftPad = 58;
              $rightPad = 24;
              $topPad = 24;
              $bottomPad = 48;
              $plotWidth = $chartWidth - $leftPad - $rightPad;
              $plotHeight = $chartHeight - $topPad - $bottomPad;
              $maxTimeline = max(1, $timeline->max(fn ($row) => max(
                  (float) $row['apostado'],
                  (float) $row['ganado_usuarios'],
                  (float) $row['perdido_usuarios']
              )));
              $pointLine = function (string $key) use ($timeline, $maxTimeline, $leftPad, $topPad, $plotWidth, $plotHeight) {
                  $count = max(1, $timeline->count() - 1);

                  return $timeline->values()->map(function ($row, $index) use ($key, $maxTimeline, $leftPad, $topPad, $plotWidth, $plotHeight, $count) {
                      $x = $leftPad + (($plotWidth / $count) * $index);
                      $y = $topPad + $plotHeight - (((float) $row[$key] / $maxTimeline) * $plotHeight);

                      return round($x, 2) . ',' . round($y, 2);
                  })->implode(' ');
              };
              $chartSeries = [
                  'apostado' => ['label' => 'Total apostado', 'class' => 'apostado', 'points' => $pointLine('apostado')],
                  'ganado_usuarios' => ['label' => 'Ganado por usuarios', 'class' => 'ganado', 'points' => $pointLine('ganado_usuarios')],
                  'perdido_usuarios' => ['label' => 'Perdido por usuarios', 'class' => 'perdido', 'points' => $pointLine('perdido_usuarios')],
              ];
            @endphp
            <div class="chart-legend">
              <span><i class="legend-dot"></i>Total apostado</span>
              <span><i class="legend-dot green"></i>Ganado por usuarios</span>
              <span><i class="legend-dot red"></i>Perdido por usuarios</span>
            </div>
            <div class="admin-timeline">
              <div class="timeline-line-chart" aria-label="Gráfico temporal del panel de administración">
                <svg class="line-chart-svg" viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" preserveAspectRatio="none" role="img">
                  <title>Evolución de dinero apostado, ganado y perdido por usuarios</title>
                  @foreach ([0, .25, .5, .75, 1] as $step)
                    @php
                      $y = $topPad + $plotHeight - ($plotHeight * $step);
                      $value = $maxTimeline * $step;
                    @endphp
                    <line class="chart-grid" x1="{{ $leftPad }}" y1="{{ $y }}" x2="{{ $chartWidth - $rightPad }}" y2="{{ $y }}" />
                    <text class="chart-value-label" x="8" y="{{ $y + 4 }}">{{ number_format($value, 0, ',', '.') }}</text>
                  @endforeach

                  <line class="chart-axis" x1="{{ $leftPad }}" y1="{{ $topPad }}" x2="{{ $leftPad }}" y2="{{ $topPad + $plotHeight }}" />
                  <line class="chart-axis" x1="{{ $leftPad }}" y1="{{ $topPad + $plotHeight }}" x2="{{ $chartWidth - $rightPad }}" y2="{{ $topPad + $plotHeight }}" />

                  @foreach ($chartSeries as $series)
                    <polyline class="chart-line {{ $series['class'] }}" points="{{ $series['points'] }}" />
                  @endforeach

                  @foreach ($timeline->values() as $index => $row)
                    @php
                      $count = max(1, $timeline->count() - 1);
                      $x = $leftPad + (($plotWidth / $count) * $index);
                    @endphp
                    @foreach ($chartSeries as $key => $series)
                      @php
                        $y = $topPad + $plotHeight - (((float) $row[$key] / $maxTimeline) * $plotHeight);
                      @endphp
                      <circle class="chart-point {{ $series['class'] }}" cx="{{ $x }}" cy="{{ $y }}" r="4">
                        <title>{{ $row['label'] }} · {{ $series['label'] }}: {{ $money($row[$key]) }}</title>
                      </circle>
                    @endforeach
                    @if ($index === 0 || $index === $timeline->count() - 1 || $index % 3 === 0)
                      <text class="chart-label" x="{{ $x }}" y="{{ $chartHeight - 16 }}" text-anchor="middle">{{ $row['label'] }}</text>
                    @endif
                  @endforeach
                </svg>
              </div>
            </div>
          </div>
        </section>
      @endif

      @if ($section === 'usuarios')
        <section class="panel">
          <div class="panel-pad"><h2 class="panel-title">Usuarios</h2><p class="muted">Desde aquí puedes ver resumen por usuario o filtrar sus apuestas.</p></div>
          <div class="table-scroll">
            <table>
              <thead><tr><th>ID</th><th>Usuario</th><th>Rol</th><th>Apuestas</th><th>Total apostado</th><th>Saldo</th><th>Estado</th><th>Acciones</th></tr></thead>
              <tbody>
                @foreach ($usuarios as $usuario)
                  <tr>
                    <td>#{{ $usuario->id }}</td>
                    <td><strong>{{ $usuario->name }}</strong><div class="muted">{{ $usuario->email }}</div></td>
                    <td><span class="badge badge-info">{{ strtoupper($usuario->role) }}</span></td>
                    <td>{{ $usuario->apuestas_count }}</td>
                    <td>{{ $money($usuario->total_apostado ?? 0) }}</td>
                    <td>{{ $money(optional($usuario->billetera)->saldoDisponible ?? 0) }}</td>
                    <td><span class="badge badge-warning">{{ $usuario->apuestas_pendientes_count }} activas</span></td>
                    <td class="actions"><div class="action-buttons"><button class="btn btn-sm" onclick="showUserSummary({{ $usuario->id }})">Resumen</button><a class="btn btn-sm" href="{{ $sectionUrl('apuestas', ['user_id' => $usuario->id]) }}">Apuestas</a><a class="btn btn-sm" href="{{ $sectionUrl('predicciones', ['pred_user_id' => $usuario->id]) }}">Predicciones</a></div></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <div class="pagination-wrap">@include('partials.pagination', ['paginator' => $usuarios])</div>
        </section>
      @endif

      @if ($section === 'apuestas')
        <section class="panel">
          <form class="toolbar" method="GET" action="{{ route('admin.panel') }}">
            <input type="hidden" name="section" value="apuestas">
            <input class="input-sm" name="search" value="{{ request('search') }}" placeholder="Buscar usuario, juego, detalle..." style="width:240px;">
            <select class="input-sm" name="estado"><option value="">Estado</option>@foreach(['pendiente','aceptada','rechazada','ganada','perdida'] as $estado)<option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ ucfirst($estado) }}</option>@endforeach</select>
            <select class="input-sm" name="tipo"><option value="">Tipo</option>@foreach($tipos as $tipo)<option value="{{ $tipo }}" @selected(request('tipo') === $tipo)>{{ ucfirst($tipo) }}</option>@endforeach</select>
            <select class="input-sm" name="user_id"><option value="">Usuario</option>@foreach($allUsers as $usuario)<option value="{{ $usuario->id }}" @selected((string)request('user_id') === (string)$usuario->id)>{{ $usuario->name }}</option>@endforeach</select>
            <select class="input-sm" name="juego_id"><option value="">Juego</option>@foreach($juegos as $juego)<option value="{{ $juego->id }}" @selected((string)request('juego_id') === (string)$juego->id)>{{ $juego->nombre }}</option>@endforeach</select>
            <button class="btn btn-primary" type="submit">Filtrar</button>
            <a class="btn" href="{{ $sectionUrl('apuestas') }}">Limpiar</a>
          </form>
          @include('partials.admin-apuestas-table', ['apuestas' => $apuestas, 'money' => $money, 'statusClass' => $statusClass, 'sectionUrl' => $sectionUrl])
        </section>
      @endif

      @if ($section === 'predicciones')
        <section class="panel">
          <form class="toolbar" method="GET" action="{{ route('admin.panel') }}">
            <input type="hidden" name="section" value="predicciones">
            <select class="input-sm" name="pred_estado"><option value="">Estado</option>@foreach(['pendiente','aceptada','rechazada','ganada','perdida'] as $estado)<option value="{{ $estado }}" @selected(request('pred_estado') === $estado)>{{ ucfirst($estado) }}</option>@endforeach</select>
            <select class="input-sm" name="pred_user_id"><option value="">Usuario</option>@foreach($allUsers as $usuario)<option value="{{ $usuario->id }}" @selected((string)request('pred_user_id') === (string)$usuario->id)>{{ $usuario->name }}</option>@endforeach</select>
            <button class="btn btn-primary" type="submit">Filtrar</button>
            <a class="btn" href="{{ $sectionUrl('predicciones') }}">Limpiar</a>
          </form>
          <div class="table-scroll">
            <table>
              <thead><tr><th>ID</th><th>Usuario</th><th>Predicción</th><th>Monto</th><th>Estado</th><th>Resultado</th><th>Admin</th><th>Acciones</th></tr></thead>
              <tbody>
                @forelse ($predicciones as $bet)
                  <tr>
                    <td>#{{ $bet->id }}</td>
                    <td><strong>{{ $bet->user->name ?? ('User #' . $bet->user_id) }}</strong><div class="muted">{{ $bet->user->email ?? '' }}</div></td>
                    <td><strong>{{ $bet->descripcion }}</strong><div class="muted">Selección: {{ $bet->seleccion }}</div><div class="muted">{{ $bet->fecha ? $bet->fecha->format('d/m/Y H:i') : '-' }}</div></td>
                    <td>{{ $money($bet->monto) }}<div class="muted">Cuota {{ number_format((float)$bet->cuota, 2, ',', '.') }}</div></td>
                    <td><span class="badge {{ $statusClass($bet->estado) }}">{{ $bet->estadoEtiqueta() }}</span></td>
                    <td>{{ $bet->resultado ?: '-' }}</td>
                    <td>{{ $bet->admin->name ?? '-' }}</td>
                    <td class="actions-cell">
                      @if ($bet->estado === 'pendiente')
                        <form class="inline-form" method="POST" action="{{ route('admin.predictions.resolve', $bet) }}">
                          @csrf
                          <input class="input-sm" name="resultado" placeholder="Comentario opcional">
                          <button class="btn btn-success btn-sm" name="action" value="aceptar">Aceptar</button>
                          <button class="btn btn-danger btn-sm" name="action" value="rechazar">Rechazar</button>
                        </form>
                      @elseif ($bet->estado === 'aceptada')
                        <form class="inline-form" method="POST" action="{{ route('admin.predictions.resolve', $bet) }}">
                          @csrf
                          <input class="input-sm" name="resultado" placeholder="Resultado real">
                          <button class="btn btn-success btn-sm" name="action" value="ganada">Ganada</button>
                          <button class="btn btn-danger btn-sm" name="action" value="perdida">Perdida</button>
                        </form>
                      @else
                        <span class="muted">Sin acciones</span>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="8" class="muted">No hay predicciones con estos filtros.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="pagination-wrap">@include('partials.pagination', ['paginator' => $predicciones])</div>
        </section>
      @endif

      @if ($section === 'juegos')
        <section class="panel">
          <div class="panel-pad"><h2 class="panel-title">Juegos</h2><p class="muted">La antigua tarjeta de Deportes se ha sustituido por Predicción en la pestaña de Juegos.</p></div>
          <div class="table-scroll"><table><thead><tr><th>ID</th><th>Nombre</th><th>Categoría</th><th>Estado</th><th>Apuestas</th><th>Total apostado</th><th>Acciones</th></tr></thead><tbody>
            @foreach($juegosAdmin as $juego)
              <tr><td>#{{ $juego->id }}</td><td><strong>{{ $juego->nombre }}</strong></td><td>{{ $juego->categoria }}</td><td><span class="badge badge-info">{{ $juego->estado }}</span></td><td>{{ $juego->apuestas_count }}</td><td>{{ $money($juego->total_apostado ?? 0) }}</td><td class="actions"><div class="action-buttons"><a class="btn btn-sm" href="{{ $sectionUrl('apuestas', ['juego_id' => $juego->id]) }}">Ver apuestas</a></div></td></tr>
            @endforeach
          </tbody></table></div>
          <div class="pagination-wrap">@include('partials.pagination', ['paginator' => $juegosAdmin])</div>
        </section>
      @endif

      @if ($section === 'billeteras')
        <section class="panel">
          <div class="panel-pad"><h2 class="panel-title">Billeteras</h2><p class="muted">Saldos actuales de usuarios.</p></div>
          <div class="table-scroll"><table><thead><tr><th>ID</th><th>Usuario</th><th>Saldo</th><th>Moneda</th><th>Acciones</th></tr></thead><tbody>
            @foreach($billeteras as $wallet)
              <tr><td>#{{ $wallet->id }}</td><td><strong>{{ $wallet->user->name ?? ('User #' . $wallet->user_id) }}</strong></td><td>{{ $money($wallet->saldoDisponible) }}</td><td>{{ $wallet->moneda }}</td><td class="actions"><div class="action-buttons"><button class="btn btn-sm" onclick="showUserSummary({{ $wallet->user_id }})">Resumen usuario</button></div></td></tr>
            @endforeach
          </tbody></table></div>
          <div class="pagination-wrap">@include('partials.pagination', ['paginator' => $billeteras])</div>
        </section>
      @endif

      @if ($section === 'notificaciones')
        <section class="panel">
          <div class="panel-pad"><h2 class="panel-title">Notificaciones</h2><p class="muted">Mensajes enviados a usuarios por apuestas, predicciones y sistema.</p></div>
          <div class="table-scroll"><table><thead><tr><th>ID</th><th>Usuario</th><th>Tipo</th><th>Título</th><th>Mensaje</th><th>Leída</th><th>Fecha</th></tr></thead><tbody>
            @foreach($notificaciones as $notificacion)
              <tr><td>#{{ $notificacion->id }}</td><td>{{ $notificacion->user->name ?? ('User #' . $notificacion->user_id) }}</td><td>{{ $notificacion->tipo }}</td><td><strong>{{ $notificacion->titulo }}</strong></td><td class="muted">{{ $notificacion->mensaje }}</td><td><span class="badge {{ $notificacion->leido ? 'badge-success' : 'badge-warning' }}">{{ $notificacion->leido ? 'Sí' : 'No' }}</span></td><td>{{ $notificacion->fecha ? \Illuminate\Support\Carbon::parse($notificacion->fecha)->format('d/m/Y H:i') : '-' }}</td></tr>
            @endforeach
          </tbody></table></div>
          <div class="pagination-wrap">@include('partials.pagination', ['paginator' => $notificaciones])</div>
        </section>
      @endif
{{-- ══════════════════════════════════════════════════════════
           SECCIÓN RANKINGS — pegar antes de </div></main> en admin.blade.php
           ══════════════════════════════════════════════════════════ --}}
      @if ($section === 'rankings')

        {{-- ── Toolbar búsqueda + ordenación ── --}}
        <form method="GET" action="{{ route('admin.panel') }}" class="toolbar" style="border-radius:var(--radius) var(--radius) 0 0;">
          <input type="hidden" name="section" value="rankings">
          <input  class="input-sm" name="search" placeholder="🔍  Buscar jugador..." style="width:200px;"
                  value="{{ request('search') }}">
          <select class="input-sm" name="sort" onchange="this.form.submit()">
            <option value="posicion"     {{ request('sort','posicion')==='posicion'     ? 'selected':'' }}>Ordenar: Posición</option>
            <option value="puntos"       {{ request('sort')==='puntos'                  ? 'selected':'' }}>Ordenar: Puntos</option>
            <option value="total_ganado" {{ request('sort')==='total_ganado'            ? 'selected':'' }}>Ordenar: Total ganado</option>
            <option value="id"           {{ request('sort')==='id'                      ? 'selected':'' }}>Ordenar: ID</option>
          </select>
          <select class="input-sm" name="dir" onchange="this.form.submit()">
            <option value="asc"  {{ request('dir','asc')==='asc'  ? 'selected':'' }}>↑ Ascendente</option>
            <option value="desc" {{ request('dir')==='desc'       ? 'selected':'' }}>↓ Descendente</option>
          </select>
          <button type="submit" class="btn btn-primary">Buscar</button>
          <a href="{{ $sectionUrl('rankings') }}" class="btn">Limpiar</a>
          <div style="margin-left:auto;">
            <button type="button" class="btn btn-gold" onclick="document.getElementById('modal-ranking-crear').style.display='flex'">
              + Nueva entrada
            </button>
          </div>
        </form>

        {{-- ── Tabla ── --}}
        <section class="panel" style="border-radius:0 0 var(--radius) var(--radius);margin-top:0;">
          <div class="table-scroll">
            <table>
              <thead>
                <tr>
                  @php
                    $s = request('sort','posicion');
                    $d = request('dir','asc');
                    $sortCols = ['posicion'=>'Posición','user_id'=>'Jugador','puntos'=>'Puntos','total_ganado'=>'Total ganado'];
                  @endphp
                  @foreach($sortCols as $col => $lbl)
                  @php
                    $newDir = ($s===$col && $d==='asc') ? 'desc' : 'asc';
                    $arrow  = $s===$col ? ($d==='asc' ? ' ↑' : ' ↓') : '';
                  @endphp
                  <th>
                    <a href="{{ route('admin.panel') }}?{{ http_build_query(array_merge(request()->except(['sort','dir','page']),['section'=>'rankings','sort'=>$col,'dir'=>$newDir])) }}"
                       style="color:inherit;text-decoration:none;">
                      {{ $lbl }}{{ $arrow }}
                    </a>
                  </th>
                  @endforeach
                  <th style="text-align:right;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($rankings as $r)
                <tr>
                  {{-- Posición con medalla --}}
                  <td>
                    @if($r->posicion === 1)      <span style="font-size:18px;">🥇</span> <strong style="color:var(--gold);">1º</strong>
                    @elseif($r->posicion === 2)  <span style="font-size:18px;">🥈</span> <strong style="color:#b0bec5;">2º</strong>
                    @elseif($r->posicion === 3)  <span style="font-size:18px;">🥉</span> <strong style="color:#cd7f32;">3º</strong>
                    @else                        <strong style="color:var(--text-muted);">#{{ $r->posicion }}</strong>
                    @endif
                  </td>
                  {{-- Jugador --}}
                  <td>
                    <strong>{{ $r->user->name ?? '—' }}</strong>
                    <div class="muted">{{ $r->user->email ?? 'ID #'.$r->user_id }}</div>
                  </td>
                  {{-- Puntos --}}
                  <td>
                    <span style="font-size:16px;font-weight:800;color:var(--gold);">{{ number_format($r->puntos) }}</span>
                    <span class="muted"> pts</span>
                  </td>
                  {{-- Total ganado --}}
                  <td>
                    <span style="font-weight:700;color:var(--success);">
                      {{ number_format($r->total_ganado, 2, ',', '.') }} EUR
                    </span>
                  </td>
                  {{-- Acciones --}}
                  <td class="actions">
                    <div class="action-buttons">
                      {{-- Botón editar: abre modal inline --}}
                      <button class="btn btn-sm"
                              onclick="abrirEditarRanking({{ $r->id }}, {{ $r->user_id }}, {{ $r->posicion }}, {{ $r->puntos }}, {{ $r->total_ganado }})">
                        ✏️ Editar
                      </button>
                      {{-- Borrar --}}
                      <form method="POST"
                            action="{{ route('admin.rankings.destroy', $r->id) }}"
                            onsubmit="return confirm('¿Eliminar ranking de {{ addslashes($r->user->name ?? 'este usuario') }}?')"
                            style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">🗑 Eliminar</button>
                      </form>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="muted" style="text-align:center;padding:36px;">
                    No hay entradas en el ranking
                    @if(request('search')) para "<strong>{{ request('search') }}</strong>" @endif.
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          {{-- Paginación --}}
          <div class="pagination-wrap">
            @include('partials.pagination', ['paginator' => $rankings])
          </div>
        </section>

        {{-- ════════════════════════════════════
             MODAL CREAR RANKING
             ════════════════════════════════════ --}}
        <div id="modal-ranking-crear"
             style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.74);z-index:1000;align-items:center;justify-content:center;padding:20px;">
          <div style="width:min(500px,95vw);background:var(--bg-card);border:1px solid var(--border);border-radius:18px;padding:28px;box-shadow:0 30px 80px rgba(0,0,0,.4);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
              <h2 class="panel-title" style="margin:0;">+ Nueva entrada de ranking</h2>
              <button class="btn" onclick="document.getElementById('modal-ranking-crear').style.display='none'">✕ Cerrar</button>
            </div>
            <form method="POST" action="{{ route('admin.rankings.store') }}">
              @csrf
              <div style="display:grid;gap:14px;">
                <div>
                  <label class="stat-label">Usuario *</label>
                  <select name="user_id" class="input-sm" style="width:100%;margin-top:6px;" required>
                    <option value="">Selecciona un usuario</option>
                    @foreach($usuariosAdmin as $u)
                      <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                  </select>
                  @error('user_id') <div style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                  <div>
                    <label class="stat-label">Posición *</label>
                    <input type="number" name="posicion" class="input-sm" style="width:100%;margin-top:6px;"
                           value="{{ old('posicion', 1) }}" min="1" required placeholder="Ej: 1">
                    @error('posicion') <div style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                  </div>
                  <div>
                    <label class="stat-label">Puntos *</label>
                    <input type="number" name="puntos" class="input-sm" style="width:100%;margin-top:6px;"
                           value="{{ old('puntos', 0) }}" min="0" required placeholder="Ej: 1500">
                    @error('puntos') <div style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                  </div>
                </div>
                <div>
                  <label class="stat-label">Total ganado (EUR) *</label>
                  <input type="number" name="total_ganado" class="input-sm" style="width:100%;margin-top:6px;"
                         value="{{ old('total_ganado', 0) }}" min="0" step="0.01" required placeholder="Ej: 250.00">
                  @error('total_ganado') <div style="color:var(--danger);font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
                </div>
              </div>
              <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;border-top:1px solid var(--border);padding-top:16px;">
                <button type="button" class="btn" onclick="document.getElementById('modal-ranking-crear').style.display='none'">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar ranking</button>
              </div>
            </form>
          </div>
        </div>

        {{-- ════════════════════════════════════
             MODAL EDITAR RANKING
             ════════════════════════════════════ --}}
        <div id="modal-ranking-editar"
             style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.74);z-index:1000;align-items:center;justify-content:center;padding:20px;">
          <div style="width:min(500px,95vw);background:var(--bg-card);border:1px solid var(--border);border-radius:18px;padding:28px;box-shadow:0 30px 80px rgba(0,0,0,.4);">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
              <h2 class="panel-title" style="margin:0;">✏️ Editar ranking</h2>
              <button class="btn" onclick="document.getElementById('modal-ranking-editar').style.display='none'">✕ Cerrar</button>
            </div>
            <form method="POST" id="form-editar-ranking" action="">
              @csrf @method('PUT')
              <div style="display:grid;gap:14px;">
                <div>
                  <label class="stat-label">Usuario</label>
                  <select name="user_id" id="edit-rk-user" class="input-sm" style="width:100%;margin-top:6px;" required>
                    @foreach($usuariosAdmin as $u)
                      <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                  </select>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                  <div>
                    <label class="stat-label">Posición *</label>
                    <input type="number" name="posicion" id="edit-rk-posicion" class="input-sm"
                           style="width:100%;margin-top:6px;" min="1" required>
                  </div>
                  <div>
                    <label class="stat-label">Puntos *</label>
                    <input type="number" name="puntos" id="edit-rk-puntos" class="input-sm"
                           style="width:100%;margin-top:6px;" min="0" required>
                  </div>
                </div>
                <div>
                  <label class="stat-label">Total ganado (EUR) *</label>
                  <input type="number" name="total_ganado" id="edit-rk-total" class="input-sm"
                         style="width:100%;margin-top:6px;" min="0" step="0.01" required>
                </div>
              </div>
              <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px;border-top:1px solid var(--border);padding-top:16px;">
                <button type="button" class="btn" onclick="document.getElementById('modal-ranking-editar').style.display='none'">Cancelar</button>
                <button type="submit" class="btn btn-primary">Actualizar</button>
              </div>
            </form>
          </div>
        </div>

        <script>
        function abrirEditarRanking(id, userId, posicion, puntos, total) {
          document.getElementById('form-editar-ranking').action = '/admin/rankings/' + id;
          document.getElementById('edit-rk-user').value    = userId;
          document.getElementById('edit-rk-posicion').value = posicion;
          document.getElementById('edit-rk-puntos').value  = puntos;
          document.getElementById('edit-rk-total').value   = total;
          document.getElementById('modal-ranking-editar').style.display = 'flex';
        }
        {{-- Reabrir modal crear si hay errores de validación --}}
        @if($errors->any() && $section === 'rankings')
          document.getElementById('modal-ranking-crear').style.display = 'flex';
        @endif
        </script>

      @endif
      {{-- FIN SECCIÓN RANKINGS --}}

    </div>
  </main>
</div>

<div class="summary-modal" id="summaryModal">
  <div class="modal-box">
    <div class="modal-head">
      <div><h2 class="panel-title" id="summaryTitle">Resumen usuario</h2><div class="muted" id="summaryEmail"></div></div>
      <button class="btn" onclick="closeSummary()">Cerrar</button>
    </div>
    <div id="summaryBody" class="muted">Cargando...</div>
  </div>
</div>

<script>
async function showUserSummary(userId) {
  const modal = document.getElementById('summaryModal');
  const body = document.getElementById('summaryBody');
  modal.classList.add('open');
  body.textContent = 'Cargando...';

  try {
    const response = await fetch(`/admin/usuarios/${userId}/resumen`, { headers: { 'Accept': 'application/json' } });
    if (!response.ok) throw new Error('No se pudo cargar el resumen');
    const data = await response.json();
    document.getElementById('summaryTitle').textContent = data.user.name;
    document.getElementById('summaryEmail').textContent = data.user.email + ' · Saldo ' + formatMoney(data.user.saldo);

    const resumen = data.resumen;
    let html = `<div class="mini-grid">
      <div class="mini-card"><span>Total apuestas</span><b>${resumen.total_apuestas}</b></div>
      <div class="mini-card"><span>Total apostado</span><b>${formatMoney(resumen.total_apostado)}</b></div>
      <div class="mini-card"><span>Ganancia neta</span><b>${formatMoney(resumen.ganancia_neta)}</b></div>
      <div class="mini-card"><span>Pérdida neta</span><b>${formatMoney(resumen.perdida_neta)}</b></div>
      <div class="mini-card"><span>Balance neto</span><b>${formatMoney(resumen.balance_neto)}</b></div>
      <div class="mini-card"><span>Activas</span><b>${resumen.pendientes}</b></div>
    </div>`;

    html += '<h3 class="panel-title">Por juego</h3>';
    if (!data.por_juego.length) {
      html += '<p class="muted">Este usuario todavía no tiene apuestas.</p>';
    } else {
      html += '<div class="table-scroll"><table><thead><tr><th>Juego</th><th>Total</th><th>Apostado</th><th>Ganadas</th><th>Perdidas</th><th>Activas</th><th>Balance</th></tr></thead><tbody>';
      data.por_juego.forEach((row) => {
        html += `<tr><td><strong>${escapeHtml(row.nombre || 'Juego')}</strong></td><td>${row.total}</td><td>${formatMoney(row.apostado)}</td><td>${row.ganadas}</td><td>${row.perdidas}</td><td>${row.pendientes}</td><td>${formatMoney(row.ganancia_neta - row.perdida_neta)}</td></tr>`;
      });
      html += '</tbody></table></div>';
    }
    body.innerHTML = html;
  } catch (error) {
    body.textContent = error.message;
  }
}
function closeSummary() { document.getElementById('summaryModal').classList.remove('open'); }
function formatMoney(value) { return Number(value || 0).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' EUR'; }
function escapeHtml(value) { return String(value ?? '').replace(/[&<>'"]/g, char => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[char])); }
</script>
</body>
</html>
