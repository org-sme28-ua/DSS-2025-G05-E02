<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Bookie 2.0 — Panel de Administración</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #7B1C1C;
    --bg-dark: #5a1212;
    --bg-card: #2a0a0a;
    --bg-card2: #3d1212;
    --sidebar: #4a1010;
    --sidebar-active: #2a0606;
    --accent: #c0392b;
    --gold: #f0c040;
    --text: #f5e6e6;
    --text-muted: #c4a0a0;
    --border: rgba(255,255,255,0.12);
    --white: #ffffff;
    --success: #2ecc71;
    --danger: #e74c3c;
    --warning: #f39c12;
    --info: #3498db;
    --btn-bg: rgba(255,255,255,0.10);
    --btn-hover: rgba(255,255,255,0.18);
    --input-bg: rgba(255,255,255,0.08);
    --radius: 12px;
    --radius-sm: 8px;
  }
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:'DM Sans',sans-serif; background:#1a0505; color:var(--text); min-height:100vh; }

  .page { display:none; }
  .page.active { display:flex; min-height:100vh; }

  .sidebar {
    width:252px; min-width:252px;
    background:var(--sidebar);
    display:flex; flex-direction:column;
    position:sticky; top:0; height:100vh; overflow-y:auto;
  }
  .sidebar-logo {
    display:flex; align-items:center; gap:10px;
    padding:20px 20px 16px;
    border-bottom:1px solid var(--border);
  }
  .logo-icon {
    width:40px; height:40px;
    background:var(--gold); border-radius:10px;
    display:flex; align-items:center; justify-content:center;
    font-size:20px;
  }
  .logo-text { font-family:'Playfair Display',serif; font-size:18px; color:var(--white); }
  .logo-badge {
    margin-left:auto; background:var(--accent);
    color:white; font-size:10px; font-weight:600;
    padding:2px 7px; border-radius:20px; letter-spacing:.5px;
  }
  .sidebar-section {
    padding:10px 12px 4px;
    font-size:10px; font-weight:600; letter-spacing:1.2px;
    color:var(--text-muted); text-transform:uppercase;
  }
  .sidebar-nav { flex:1; padding:8px 12px; display:flex; flex-direction:column; gap:2px; }
  .nav-item {
    display:flex; align-items:center; gap:10px;
    padding:9px 12px; border-radius:var(--radius-sm);
    cursor:pointer; color:var(--text-muted);
    font-size:13px; font-weight:500; transition:all .15s;
    border:none; background:none; text-align:left; width:100%;
  }
  .nav-item:hover { background:var(--btn-bg); color:var(--text); }
  .nav-item.active { background:var(--sidebar-active); color:var(--white); border-left:3px solid var(--gold); }
  .nav-icon { font-size:15px; width:18px; text-align:center; }
  .nav-divider { height:1px; background:var(--border); margin:8px 0; }
  .sidebar-bottom { padding:10px 12px; border-top:1px solid var(--border); display:flex; flex-direction:column; gap:2px; }

  .main { flex:1; display:flex; flex-direction:column; overflow:auto; background:#1a0505; }
  .topbar {
    background:#2a0808; padding:10px 24px;
    display:flex; align-items:center; gap:14px;
    border-bottom:1px solid var(--border);
    position:sticky; top:0; z-index:10;
  }
  .topbar-title { font-family:'Playfair Display',serif; font-size:15px; color:var(--gold); white-space:nowrap; }
  .topbar-search {
    flex:1; background:var(--input-bg);
    border:1px solid var(--border); border-radius:50px;
    padding:7px 16px; color:var(--text); font-size:13px; font-family:inherit;
  }
  .topbar-search::placeholder { color:var(--text-muted); }
  .topbar-search:focus { outline:none; border-color:rgba(255,255,255,0.28); }
  .user-chip { display:flex; align-items:center; gap:8px; cursor:pointer; }
  .user-avatar {
    width:34px; height:34px; border-radius:50%;
    background:linear-gradient(135deg,#c0392b,#e74c3c);
    display:flex; align-items:center; justify-content:center;
    font-weight:700; font-size:12px; color:white; border:2px solid var(--gold);
  }
  .content { padding:24px 28px; flex:1; }
  .page-header { display:flex; align-items:center; gap:14px; margin-bottom:22px; }
  .page-title { font-family:'Playfair Display',serif; font-size:26px; color:var(--white); flex:1; }
  .page-subtitle { font-size:12px; color:var(--text-muted); margin-top:2px; }

  .stats-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:14px; margin-bottom:22px; }
  .stat-card {
    background:var(--bg-card2); border-radius:var(--radius);
    padding:16px 18px; border:1px solid var(--border);
  }
  .stat-label { font-size:11px; color:var(--text-muted); font-weight:600; letter-spacing:.5px; text-transform:uppercase; margin-bottom:6px; }
  .stat-value { font-size:24px; font-weight:700; color:var(--white); }
  .stat-delta { font-size:11px; margin-top:4px; }
  .delta-up { color:var(--success); }
  .delta-down { color:var(--danger); }

  .table-wrap {
    background:var(--bg-card); border-radius:var(--radius);
    border:1px solid var(--border); overflow:hidden;
  }
  .toolbar {
    display:flex; align-items:center; gap:10px; flex-wrap:wrap;
    padding:14px 16px; border-bottom:1px solid var(--border);
    background:var(--bg-card2);
  }
  .toolbar-right { margin-left:auto; display:flex; gap:8px; align-items:center; }
  .input-sm {
    background:var(--input-bg); border:1px solid var(--border);
    border-radius:var(--radius-sm); padding:7px 12px;
    color:var(--text); font-size:12px; font-family:inherit;
  }
  .input-sm:focus { outline:none; border-color:rgba(255,255,255,0.3); }
  .input-sm::placeholder { color:var(--text-muted); }
  select.input-sm option { background:#2a0a0a; }
  table { width:100%; border-collapse:collapse; font-size:13px; }
  thead { background:var(--bg-card2); }
  th {
    padding:11px 14px; text-align:left; color:var(--text-muted);
    font-weight:600; font-size:11px; letter-spacing:.6px; text-transform:uppercase;
    border-bottom:1px solid var(--border); cursor:pointer; user-select:none;
    white-space:nowrap;
  }
  th:hover { color:var(--text); }
  th:last-child { cursor:default; }
  td { padding:11px 14px; border-bottom:1px solid rgba(255,255,255,0.05); vertical-align:middle; }
  tr:last-child td { border-bottom:none; }
  tr:hover td { background:rgba(255,255,255,0.03); }
  .sort-arrow { font-size:10px; margin-left:4px; opacity:.5; }
  th.sorted .sort-arrow { opacity:1; color:var(--gold); }

  .badge {
    display:inline-block; padding:3px 9px; border-radius:20px;
    font-size:11px; font-weight:600; letter-spacing:.3px;
  }
  .badge-success { background:rgba(46,204,113,.18); color:#2ecc71; border:1px solid rgba(46,204,113,.3); }
  .badge-danger  { background:rgba(231,76,60,.18);  color:#e74c3c; border:1px solid rgba(231,76,60,.3); }
  .badge-warning { background:rgba(243,156,18,.18); color:#f39c12; border:1px solid rgba(243,156,18,.3); }
  .badge-info    { background:rgba(52,152,219,.18);  color:#3498db; border:1px solid rgba(52,152,219,.3); }
  .badge-gold    { background:rgba(240,192,64,.18); color:#f0c040; border:1px solid rgba(240,192,64,.3); }
  .badge-muted   { background:rgba(255,255,255,.08); color:#aaa; border:1px solid rgba(255,255,255,.1); }

  .btn {
    padding:7px 14px; border-radius:var(--radius-sm);
    border:1px solid var(--border); background:var(--btn-bg);
    color:var(--text); font-family:inherit; font-size:12px; font-weight:500;
    cursor:pointer; transition:all .15s;
    display:inline-flex; align-items:center; gap:5px;
  }
  .btn:hover { background:var(--btn-hover); }
  .btn-primary { background:var(--accent); border-color:var(--accent); color:white; }
  .btn-primary:hover { background:#a93226; }
  .btn-danger { background:rgba(231,76,60,.18); border-color:rgba(231,76,60,.35); color:#e74c3c; }
  .btn-danger:hover { background:rgba(231,76,60,.32); }
  .btn-sm { padding:5px 10px; font-size:11px; }

  .pagination { display:flex; align-items:center; gap:4px; padding:12px 16px; background:var(--bg-card2); border-top:1px solid var(--border); }
  .page-btn {
    width:30px; height:30px; border-radius:var(--radius-sm);
    border:1px solid var(--border); background:var(--btn-bg);
    color:var(--text); font-size:12px; cursor:pointer; transition:all .15s;
    display:flex; align-items:center; justify-content:center;
  }
  .page-btn:hover:not([disabled]) { background:var(--btn-hover); }
  .page-btn.active { background:var(--accent); border-color:var(--accent); color:white; font-weight:700; }
  .page-btn[disabled] { opacity:.35; cursor:not-allowed; }
  .page-info { margin-left:auto; font-size:11px; color:var(--text-muted); }

  .modal-overlay {
    display:none; position:fixed; inset:0;
    background:rgba(0,0,0,.75); z-index:1000;
    align-items:center; justify-content:center;
    backdrop-filter:blur(3px);
  }
  .modal-overlay.open { display:flex; }
  .modal {
    background:var(--bg-card); border:1px solid var(--border);
    border-radius:16px; padding:28px; width:min(520px,92vw);
    max-height:90vh; overflow-y:auto;
  }
  .modal h2 {
    font-family:'Playfair Display',serif; font-size:22px;
    color:var(--white); margin-bottom:20px;
    padding-bottom:14px; border-bottom:1px solid var(--border);
  }
  .confirm-modal {
    background:var(--bg-card); border:1px solid rgba(231,76,60,.4);
    border-radius:16px; padding:32px; width:360px; text-align:center;
  }
  .confirm-icon { font-size:40px; margin-bottom:12px; }
  .confirm-modal h3 { font-size:18px; margin-bottom:8px; color:var(--white); }
  .confirm-modal p { color:var(--text-muted); font-size:13px; margin-bottom:20px; }

  .form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
  .form-group { display:flex; flex-direction:column; gap:5px; margin-bottom:14px; }
  .form-label { font-size:12px; font-weight:600; color:var(--text-muted); letter-spacing:.3px; }
  .form-control {
    background:var(--input-bg); border:1px solid var(--border);
    border-radius:var(--radius-sm); padding:9px 12px;
    color:var(--text); font-size:13px; font-family:inherit; width:100%;
  }
  .form-control:focus { outline:none; border-color:rgba(255,255,255,0.3); }
  .form-control.error { border-color:var(--danger); }
  .form-control::placeholder { color:var(--text-muted); }
  .error-msg { font-size:11px; color:var(--danger); min-height:14px; }
  .form-actions { display:flex; gap:10px; justify-content:flex-end; padding-top:16px; border-top:1px solid var(--border); margin-top:4px; }
  .form-hint { font-size:11px; color:var(--text-muted); }

  #toast {
    position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(20px);
    background:#2a0a0a; border:1px solid var(--border); border-radius:10px;
    padding:10px 20px; font-size:13px; color:var(--text);
    opacity:0; transition:all .3s; z-index:9999; pointer-events:none; white-space:nowrap;
  }
  #toast.show { opacity:1; transform:translateX(-50%) translateY(0); }
  #toast.success { border-color:rgba(46,204,113,.5); color:#2ecc71; }
  #toast.danger  { border-color:rgba(231,76,60,.5);  color:#e74c3c; }

  .activity-list { display:flex; flex-direction:column; gap:0; }
  .activity-item {
    display:flex; align-items:center; gap:12px;
    padding:11px 0; border-bottom:1px solid rgba(255,255,255,0.05);
  }
  .activity-item:last-child { border-bottom:none; }
  .activity-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
  .activity-text { font-size:12px; flex:1; }
  .activity-time { font-size:11px; color:var(--text-muted); white-space:nowrap; }
  .two-col { display:grid; grid-template-columns:1fr 1fr; gap:18px; }

  .tab-bar { display:flex; gap:4px; margin-bottom:18px; border-bottom:1px solid var(--border); padding-bottom:-1px; }
  .tab-btn {
    padding:8px 16px; border:none; background:none;
    color:var(--text-muted); font-size:13px; font-family:inherit;
    cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1px;
  }
  .tab-btn.active { color:var(--white); border-bottom-color:var(--gold); }

  @media (max-width:768px) {
    .sidebar { display:none; }
    .form-row { grid-template-columns:1fr; }
    .two-col { grid-template-columns:1fr; }
  }
</style>
</head>
<body>

<!-- ======================== PAGE: ADMIN DASHBOARD ======================== -->
<div class="page active" id="page-admin-dashboard">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item active" onclick="goTo('dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('rankings')"><span class="nav-icon">🏆</span> Rankings</button>
      <button class="nav-item" onclick="goTo('settings')"><span class="nav-icon">⚙️</span> Settings</button>
      <button class="nav-item" onclick="goTo('parametros-ganancia')"><span class="nav-icon">💰</span> ParámetrosGanancia</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar">
      <span class="topbar-title">Dashboard</span>
      <input class="topbar-search" placeholder="🔍 Buscar en el panel...">
      <div class="user-chip"><div class="user-avatar">AD</div><span style="font-size:13px;font-weight:500;">Admin</span></div>
    </div>
    <div class="content">
      <div class="page-header"><div><div class="page-title">Panel de Administración</div><div class="page-subtitle">Visión general del sistema · Bookie 2.0</div></div></div>
      <div class="stats-row">
        <div class="stat-card"><div class="stat-label">Usuarios totales</div><div class="stat-value" id="stat-usuarios">0</div></div>
        <div class="stat-card"><div class="stat-label">Apuestas activas</div><div class="stat-value" id="stat-apuestas">0</div></div>
        <div class="stat-card"><div class="stat-label">Juegos disponibles</div><div class="stat-value" id="stat-juegos">0</div></div>
      </div>
      <div class="two-col">
        <div class="table-wrap"><div class="toolbar"><span>Actividad reciente</span></div><div style="padding:16px;" id="actividad-reciente">Cargando...</div></div>
        <div class="table-wrap"><div class="toolbar"><span>Acceso rápido</span></div><div style="padding:16px;display:grid;grid-template-columns:1fr 1fr;gap:10px;">
          <button class="btn" style="flex-direction:column;padding:16px;" onclick="goTo('usuarios')"><span style="font-size:22px;">👥</span><span>Usuarios</span></button>
          <button class="btn" style="flex-direction:column;padding:16px;" onclick="goTo('juegos')"><span style="font-size:22px;">🎮</span><span>Juegos</span></button>
          <button class="btn" style="flex-direction:column;padding:16px;" onclick="goTo('apuestas')"><span style="font-size:22px;">📋</span><span>Apuestas</span></button>
          <button class="btn" style="flex-direction:column;padding:16px;" onclick="goTo('settings')"><span style="font-size:22px;">⚙️</span><span>Settings</span></button>
        </div></div>
      </div>
    </div>
  </div>
</div>

<!-- ======================== PAGE: ADMIN USUARIOS ======================== -->
<div class="page" id="page-admin-usuarios">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item" onclick="goTo('dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item active" onclick="goTo('usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('rankings')"><span class="nav-icon">🏆</span> Rankings</button>
      <button class="nav-item" onclick="goTo('settings')"><span class="nav-icon">⚙️</span> Settings</button>
      <button class="nav-item" onclick="goTo('parametros-ganancia')"><span class="nav-icon">💰</span> Parámetros Ganancia</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar"><span class="topbar-title">Gestión de Usuarios</span><input class="topbar-search" placeholder="🔍 Buscar..."><div class="user-chip"><div class="user-avatar">AD</div><span>Admin</span></div></div>
    <div class="content">
      <div class="page-header"><div><div class="page-title">Usuarios</div><div class="page-subtitle">Gestión completa de jugadores y operadores</div></div><button class="btn btn-primary" onclick="openNewModal('usuarios')">＋ Nuevo usuario</button></div>
      <div class="table-wrap">
        <div class="toolbar">
          <input class="input-sm" placeholder="🔍 Buscar por nombre, email..." style="width:220px;" id="search-usuarios" oninput="filterRender('usuarios')">
          <select class="input-sm" id="filter-vip-u" onchange="filterRender('usuarios')"><option value="">Todos los niveles VIP</option><option value="0">Sin VIP</option><option value="1">VIP 1</option><option value="2">VIP 2</option><option value="3">VIP 3</option></select>
          <div class="toolbar-right"><span id="count-usuarios"></span></div>
        </div>
        <table><thead><tr><th onclick="sortRender('usuarios','id',this)">ID <span class="sort-arrow">⇅</span></th><th onclick="sortRender('usuarios','name',this)">Nombre <span class="sort-arrow">⇅</span></th><th onclick="sortRender('usuarios','email',this)">Email <span class="sort-arrow">⇅</span></th><th onclick="sortRender('usuarios','puntos_fidelidad',this)">Puntos fidelidad <span class="sort-arrow">⇅</span></th><th onclick="sortRender('usuarios','nivel_vip',this)">Nivel VIP <span class="sort-arrow">⇅</span></th><th>Acciones</th></tr></thead><tbody id="tbody-usuarios"></tbody></table>
        <div class="pagination" id="pag-usuarios"></div>
      </div>
    </div>
  </div>
</div>

<!-- ======================== PAGE: ADMIN NOTIFICACION ======================== -->

<div class="page" id="page-admin-notificaciones">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item" onclick="goTo('dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item active" onclick="goTo('notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('rankings')"><span class="nav-icon">🏆</span> Rankings</button>
      <button class="nav-item" onclick="goTo('settings')"><span class="nav-icon">⚙️</span> Settings</button>
      <button class="nav-item" onclick="goTo('parametros-ganancia')"><span class="nav-icon">💰</span> Parámetros Ganancia</button>
    </nav>
  </aside>
  <div class="main">
    <div class="topbar"><span class="topbar-title">Gestión de Notificaciones</span><input class="topbar-search" placeholder="🔍 Buscar..."><div class="user-chip"><div class="user-avatar">AD</div><span>Admin</span></div></div>
    <div class="content">
      <div class="page-header">
        <div><div class="page-title">Notificaciones</div><div class="page-subtitle">Alertas y mensajes del sistema</div></div>
        <button class="btn btn-primary" onclick="openNewModal('notificaciones')">＋ Nueva Notificación</button>
      </div>
      <div class="table-wrap">
        <div class="toolbar">
          <input class="input-sm" placeholder="🔍 Buscar título o usuario..." style="width:250px;" id="search-notificaciones" oninput="filterRender('notificaciones')">
          <select class="input-sm" id="filter-tipo-n" onchange="filterRender('notificaciones')">
            <option value="">Todos los tipos</option>
            <option value="apuesta">Apuesta</option>
            <option value="promo">Promo</option>
            <option value="alerta">Alerta</option>
            <option value="info">Info</option>
          </select>
          <select class="input-sm" id="filter-leido-n" onchange="filterRender('notificaciones')">
            <option value="">Estado lectura</option>
            <option value="true">Leídas</option>
            <option value="false">No leídas</option>
          </select>
          <div class="toolbar-right"><span id="count-notificaciones"></span></div>
        </div>
        <table>
          <thead>
            <tr>
              <th onclick="sortRender('notificaciones','id',this)">ID ⇅</th>
              <th>Usuario</th>
              <th onclick="sortRender('notificaciones','tipo',this)">Tipo ⇅</th>
              <th onclick="sortRender('notificaciones','titulo',this)">Título ⇅</th>
              <th onclick="sortRender('notificaciones','leido',this)">Leído ⇅</th>
              <th onclick="sortRender('notificaciones','fecha',this)">Fecha ⇅</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody-notificaciones"></tbody>
        </table>
        <div class="pagination" id="pag-notificaciones"></div>
      </div>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-notificacion">
  <div class="modal">
    <h2 id="title-notificacion">Nueva Notificación</h2>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">ID Usuario *</label>
        <input type="number" class="form-control" id="n-user_id">
        <div class="error-msg" id="err-n-user_id"></div>
      </div>
      <div class="form-group">
        <label class="form-label">Tipo *</label>
        <select class="form-control" id="n-tipo">
          <option value="alerta">Alerta</option>
          <option value="promo">Promo</option>
          <option value="apuesta">Apuesta</option>
          <option value="info">Info</option>
          <option value="mensaje">Mensaje</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Título *</label>
      <input type="text" class="form-control" id="n-titulo">
      <div class="error-msg" id="err-n-titulo"></div>
    </div>
    <div class="form-group">
      <label class="form-label">Mensaje</label>
      <textarea class="form-control" id="n-mensaje" rows="3"></textarea>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Leído</label>
        <select class="form-control" id="n-leido">
          <option value="false">No</option>
          <option value="true">Sí</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Fecha *</label>
        <input type="date" class="form-control" id="n-fecha">
        <div class="error-msg" id="err-n-fecha"></div>
      </div>
    </div>
    <div class="form-actions">
      <button class="btn" onclick="closeModal('modal-notificacion')">Cancelar</button>
      <button class="btn btn-primary" onclick="submitForm('notificaciones')">Guardar</button>
    </div>
  </div>
</div>

<!-- ======================== PAGE: ADMIN JUEGOS ======================== -->

<div class="page" id="page-admin-juegos">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item" onclick="goTo('dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('rankings')"><span class="nav-icon">🏆</span> Rankings</button>
      <button class="nav-item" onclick="goTo('settings')"><span class="nav-icon">⚙️</span> Settings</button>
      <button class="nav-item" onclick="goTo('parametros-ganancia')"><span class="nav-icon">💰</span> Parámetros Ganancia</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar"><span class="topbar-title">Gestión de Juegos</span><input class="topbar-search" placeholder="🔍 Buscar..."><div class="user-chip"><div class="user-avatar">AD</div><span>Admin</span></div></div>
    <div class="content">
      <div class="page-header"><div><div class="page-title">Juegos</div><div class="page-subtitle">Gestión de juegos disponibles</div></div><button class="btn btn-primary" onclick="openNewModal('juegos')">＋ Nuevo juego</button></div>
      <div class="table-wrap">
        <div class="toolbar">
          <input class="input-sm" placeholder="🔍 Buscar por nombre o categoría..." style="width:250px;" id="search-juegos" oninput="filterRender('juegos')">
          <select class="input-sm" id="filter-estado-j" onchange="filterRender('juegos')">
            <option value="">Todos los estados</option>
            <option value="abierta">Abierta</option>
            <option value="cerrada">Cerrada</option>
            <option value="en_juego">En juego</option>
          </select>
          <div class="toolbar-right"><span id="count-juegos"></span></div>
        </div>
        <table>
          <thead>
            <tr>
              <th onclick="sortRender('juegos','id',this)">ID <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('juegos','nombre',this)">Nombre <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('juegos','categoria',this)">Categoría <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('juegos','estado',this)">Estado <span class="sort-arrow">⇅</span></th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody-juegos"></tbody>
        </table>
        <div class="pagination" id="pag-juegos"></div>
      </div>
    </div>
  </div>
</div>

<!-- ======================== PAGE: ADMIN BILLETERAS ======================== -->
<div class="page" id="page-admin-billeteras">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item" onclick="goTo('dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item active" onclick="goTo('billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('rankings')"><span class="nav-icon">🏆</span> Rankings</button>
      <button class="nav-item" onclick="goTo('settings')"><span class="nav-icon">⚙️</span> Settings</button>
      <button class="nav-item" onclick="goTo('parametros-ganancia')"><span class="nav-icon">💰</span> Parámetros Ganancia</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar">
      <span class="topbar-title">Gestión de Billeteras</span>
      <input class="topbar-search" placeholder="🔍 Buscar...">
      <div class="user-chip"><div class="user-avatar">AD</div><span>Admin</span></div>
    </div>
    <div class="content">
      <div class="page-header">
        <div>
          <div class="page-title">Billeteras</div>
          <div class="page-subtitle">Gestión de saldos y monedas</div>
        </div>
        <button class="btn btn-primary" onclick="openNewModal('billeteras')">＋ Nueva billetera</button>
      </div>
      <div class="table-wrap">
        <div class="toolbar">
          <input class="input-sm" placeholder="🔍 Buscar por ID o moneda..." style="width:250px;" id="search-billeteras" oninput="filterRender('billeteras')">
          <select class="input-sm" id="filter-moneda-b" onchange="filterRender('billeteras')">
            <option value="">Todas las monedas</option>
            <option value="EUR">EUR</option>
            <option value="USD">USD</option>
            <option value="GBP">GBP</option>
          </select>
          <div class="toolbar-right"><span id="count-billeteras"></span></div>
        </div>
        <table>
          <thead>
            <tr>
              <th onclick="sortRender('billeteras','id',this)">ID <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('billeteras','saldoDisponible',this)">Saldo disponible <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('billeteras','moneda',this)">Moneda <span class="sort-arrow">⇅</span></th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody-billeteras"></tbody>
        </table>
        <div class="pagination" id="pag-billeteras"></div>
      </div>
    </div>
  </div>
</div>

<!-- ======================== PAGE: ADMIN CHAT ======================== -->

<div class="page" id="page-admin-chats">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item" onclick="goTo('dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('rankings')"><span class="nav-icon">🏆</span> Rankings</button>
      <button class="nav-item" onclick="goTo('settings')"><span class="nav-icon">⚙️</span> Settings</button>
      <button class="nav-item" onclick="goTo('parametros-ganancia')"><span class="nav-icon">💰</span> Parámetros Ganancia</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar"><span class="topbar-title">Gestión de Chats</span><input class="topbar-search" placeholder="🔍 Buscar..."><div class="user-chip"><div class="user-avatar">AD</div><span>Admin</span></div></div>
    <div class="content">
      <div class="page-header"><div><div class="page-title">Chats</div><div class="page-subtitle">Gestión de salas y chats</div></div><button class="btn btn-primary" onclick="openNewModal('chats')">＋ Nuevo chat</button></div>
      <div class="table-wrap">
        <div class="toolbar">
          <input class="input-sm" placeholder="🔍 Buscar por nombre..." style="width:250px;" id="search-chats" oninput="filterRender('chats')">
          <select class="input-sm" id="filter-activo-c" onchange="filterRender('chats')">
            <option value="">Todos</option>
            <option value="true">Activos</option>
            <option value="false">Inactivos</option>
          </select>
          <div class="toolbar-right"><span id="count-chats"></span></div>
        </div>
        <table>
          <thead>
            <tr>
              <th onclick="sortRender('chats','id',this)">ID <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('chats','nombre',this)">Nombre <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('chats','fechaCreacion',this)">Fecha creación <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('chats','activo',this)">Activo <span class="sort-arrow">⇅</span></th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody-chats"></tbody>
        </table>
        <div class="pagination" id="pag-chats"></div>
      </div>
    </div>
  </div>
</div>

<!-- ======================== PAGE: ADMIN RANKINGS ======================== -->

<div class="page" id="page-admin-rankings">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item" onclick="goTo('dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('rankings')"><span class="nav-icon">🏆</span> Rankings</button>
      <button class="nav-item" onclick="goTo('settings')"><span class="nav-icon">⚙️</span> Settings</button>
      <button class="nav-item" onclick="goTo('parametros-ganancia')"><span class="nav-icon">💰</span> Parámetros Ganancia</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar"><span class="topbar-title">Gestión de Rankings</span><input class="topbar-search" placeholder="🔍 Buscar..."><div class="user-chip"><div class="user-avatar">AD</div><span>Admin</span></div></div>
    <div class="content">
      <div class="page-header"><div><div class="page-title">Rankings</div><div class="page-subtitle">Clasificación de usuarios</div></div><button class="btn btn-primary" onclick="openNewModal('rankings')">＋ Nuevo ranking</button></div>
      <div class="table-wrap">
        <div class="toolbar">
          <input class="input-sm" placeholder="🔍 Buscar por user_id..." style="width:250px;" id="search-rankings" oninput="filterRender('rankings')">
          <div class="toolbar-right"><span id="count-rankings"></span></div>
        </div>
        <table>
           <thead>
            <tr>
              <th onclick="sortRender('rankings','id',this)">ID <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('rankings','user_id',this)">User ID <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('rankings','posicion',this)">Posición <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('rankings','puntos',this)">Puntos <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('rankings','total_ganado',this)">Total ganado <span class="sort-arrow">⇅</span></th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody-rankings"></tbody>
        </table>
        <div class="pagination" id="pag-rankings"></div>
      </div>
    </div>
  </div>
</div>

<!-- ======================== PAGE: ADMIN GANANCIAS ======================== -->
<div class="page" id="page-admin-parametros-ganancia">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item" onclick="goTo('dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('rankings')"><span class="nav-icon">🏆</span> Rankings</button>
      <button class="nav-item" onclick="goTo('settings')"><span class="nav-icon">⚙️</span> Settings</button>
      <button class="nav-item active" onclick="goTo('parametros-ganancia')"><span class="nav-icon">💰</span> Parámetros Ganancia</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar">
      <span class="topbar-title">Parámetros de Ganancia</span>
      <input class="topbar-search" placeholder="🔍 Buscar...">
      <div class="user-chip"><div class="user-avatar">AD</div><span>Admin</span></div>
    </div>
    <div class="content">
      <div class="page-header">
        <div>
          <div class="page-title">Parámetros de Ganancia</div>
          <div class="page-subtitle">Configuración de multiplicadores y bonus</div>
        </div>
        <button class="btn btn-primary" onclick="openNewModal('parametros-ganancia')">＋ Nuevo parámetro</button>
      </div>
      <div class="table-wrap">
        <div class="toolbar">
          <div class="toolbar-right"><span id="count-parametros-ganancia"></span></div>
        </div>
        <table>
          <thead>
            <tr>
              <th onclick="sortRender('parametros-ganancia','id',this)">ID <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('parametros-ganancia','juego_id',this)">ID Juego <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('parametros-ganancia','multiplicacion_por_juego',this)">Multiplicación por juego <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('parametros-ganancia','bonus_por_racha',this)">Bonus por racha <span class="sort-arrow">⇅</span></th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody-parametros-ganancia"></tbody>
        </table>
        <div class="pagination" id="pag-parametros-ganancia"></div>
      </div>
    </div>
  </div>
</div>
<!-- ======================== PAGE: ADMIN APUESTAS ======================== -->
<div class="page" id="page-admin-apuestas">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item" onclick="goTo('dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item active" onclick="goTo('apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('rankings')"><span class="nav-icon">🏆</span> Rankings</button>
      <button class="nav-item" onclick="goTo('settings')"><span class="nav-icon">⚙️</span> Settings</button>
      <button class="nav-item" onclick="goTo('parametros-ganancia')"><span class="nav-icon">💰</span> Parámetros Ganancia</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar">
      <span class="topbar-title">Gestión de Apuestas</span>
      <input class="topbar-search" placeholder="🔍 Buscar...">
      <div class="user-chip"><div class="user-avatar">AD</div><span>Admin</span></div>
    </div>
    <div class="content">
      <div class="page-header">
        <div>
          <div class="page-title">Apuestas</div>
          <div class="page-subtitle">Historial y gestión de tickets</div>
        </div>
        <button class="btn btn-primary" onclick="openNewModal('apuestas')">＋ Nueva Apuesta</button>
      </div>
      <div class="table-wrap">
        <div class="toolbar">
          <input class="input-sm" placeholder="🔍 Buscar usuario o juego..." style="width:250px;" id="search-apuestas" oninput="filterRender('apuestas')">
          <select class="input-sm" id="filter-estado-a" onchange="filterRender('apuestas')">
            <option value="">Todos los estados</option>
            <option value="pendiente">Pendientes</option>
            <option value="ganada">Ganadas</option>
            <option value="perdida">Perdidas</option>
          </select>
          <div class="toolbar-right"><span id="count-apuestas"></span></div>
        </div>
        <table>
          <thead>
            <tr>
              <th onclick="sortRender('apuestas','id',this)">ID <span class="sort-arrow">⇅</span></th>
              <th>Usuario</th>
              <th>Juego</th>
              <th onclick="sortRender('apuestas','monto',this)">Monto <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('apuestas','cuota',this)">Cuota <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('apuestas','estado',this)">Estado <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('apuestas','fecha',this)">Fecha <span class="sort-arrow">⇅</span></th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody-apuestas"></tbody>
        </table>
        <div class="pagination" id="pag-apuestas"></div>
      </div>
    </div>
  </div>
</div>

<!-- ======================== PAGE: ADMIN SETTINGS ======================== -->
<div class="page" id="page-admin-settings">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item" onclick="goTo('dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('rankings')"><span class="nav-icon">🏆</span> Rankings</button>
      <button class="nav-item active" onclick="goTo('settings')"><span class="nav-icon">⚙️</span> Settings</button>
      <button class="nav-item" onclick="goTo('parametros-ganancia')"><span class="nav-icon">💰</span> Parámetros Ganancia</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar"><span class="topbar-title">Configuración del Sistema</span><input class="topbar-search" placeholder="🔍 Buscar..."><div class="user-chip"><div class="user-avatar">AD</div><span>Admin</span></div></div>
    <div class="content">
      <div class="page-header"><div><div class="page-title">Settings</div><div class="page-subtitle">Configuración general del sistema</div></div><button class="btn btn-primary" onclick="openNewModal('settings')">＋ Nueva configuración</button></div>
      <div class="table-wrap">
        <div class="toolbar">
          <input class="input-sm" placeholder="🔍 Buscar por clave, descripción..." style="width:250px;" id="search-settings" oninput="filterRender('settings')">
          <select class="input-sm" id="filter-activo-s" onchange="filterRender('settings')"><option value="">Todos</option><option value="true">Activos</option><option value="false">Inactivos</option></select>
          <div class="toolbar-right"><span id="count-settings"></span></div>
        </div>
        <table><thead><tr><th onclick="sortRender('settings','clave',this)">Clave <span class="sort-arrow">⇅</span></th><th onclick="sortRender('settings','valor',this)">Valor <span class="sort-arrow">⇅</span></th><th onclick="sortRender('settings','descripcion',this)">Descripción <span class="sort-arrow">⇅</span></th><th onclick="sortRender('settings','activo',this)">Activo <span class="sort-arrow">⇅</span></th><th>Acciones</th></tr></thead><tbody id="tbody-settings"></tbody></table>
        <div class="pagination" id="pag-settings"></div>
      </div>
    </div>
  </div>
</div>

<!-- MODALES -->
<div class="modal-overlay" id="modal-usuario"><div class="modal"><h2 id="title-usuario">Nuevo Usuario</h2>
  <div class="form-row"><div class="form-group"><label class="form-label">Nombre *</label><input type="text" class="form-control" id="u-nombre"><div class="error-msg" id="err-u-nombre"></div></div><div class="form-group"><label class="form-label">Email *</label><input type="email" class="form-control" id="u-email"><div class="error-msg" id="err-u-email"></div></div></div>
  <div class="form-row"><div class="form-group"><label class="form-label">Contraseña *</label><input type="password" class="form-control" id="u-password"><div class="error-msg" id="err-u-password"></div></div><div class="form-group"><label class="form-label">Puntos fidelidad</label><input type="number" class="form-control" id="u-puntos" value="0"></div></div>
  <div class="form-group"><label class="form-label">Nivel VIP</label><select class="form-control" id="u-vip"><option value="0">Sin VIP</option><option value="1">VIP 1</option><option value="2">VIP 2</option><option value="3">VIP 3</option></select></div>
  <div class="form-actions"><button class="btn" onclick="closeModal('modal-usuario')">Cancelar</button><button class="btn btn-primary" onclick="submitForm('usuarios')">Guardar</button></div>
</div></div>

<div class="modal-overlay" id="modal-setting"><div class="modal"><h2 id="title-setting">Nueva Configuración</h2>
  <div class="form-group"><label class="form-label">Clave *</label><input type="text" class="form-control" id="s-clave"><div class="error-msg" id="err-s-clave"></div></div>
  <div class="form-group"><label class="form-label">Valor *</label><input type="number" class="form-control" id="s-valor"><div class="error-msg" id="err-s-valor"></div></div>
  <div class="form-group"><label class="form-label">Descripción</label><textarea class="form-control" id="s-descripcion" rows="3"></textarea></div>
  <div class="form-group"><label class="form-label">Activo</label><select class="form-control" id="s-activo"><option value="true">Sí</option><option value="false">No</option></select></div>
  <div class="form-actions"><button class="btn" onclick="closeModal('modal-setting')">Cancelar</button><button class="btn btn-primary" onclick="submitForm('settings')">Guardar</button></div>
</div></div>

<div class="modal-overlay" id="modal-apuesta">
  <div class="modal">
    <h2 id="title-apuesta">Nueva Apuesta</h2>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">ID Usuario *</label>
        <input type="number" class="form-control" id="a-user_id">
        <div class="error-msg" id="err-a-user_id"></div>
      </div>
      <div class="form-group">
        <label class="form-label">ID Juego *</label>
        <input type="number" class="form-control" id="a-juego_id">
        <div class="error-msg" id="err-a-juego_id"></div>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Monto ($) *</label>
        <input type="number" step="0.01" class="form-control" id="a-monto">
        <div class="error-msg" id="err-a-monto"></div>
      </div>
      <div class="form-group">
        <label class="form-label">Cuota *</label>
        <input type="number" step="0.01" class="form-control" id="a-cuota">
        <div class="error-msg" id="err-a-cuota"></div>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Estado *</label>
        <select class="form-control" id="a-estado">
          <option value="pendiente">Pendiente</option>
          <option value="ganada">Ganada</option>
          <option value="perdida">Perdida</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Fecha *</label>
        <input type="date" class="form-control" id="a-fecha">
        <div class="error-msg" id="err-a-fecha"></div>
      </div>
    </div>
    <div class="form-actions">
      <button class="btn" onclick="closeModal('modal-apuesta')">Cancelar</button>
      <button class="btn btn-primary" onclick="submitForm('apuestas')">Guardar</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-juego">
  <div class="modal">
    <h2 id="title-juego">Nuevo Juego</h2>
    <div class="form-group">
      <label class="form-label">Nombre *</label>
      <input type="text" class="form-control" id="j-nombre">
      <div class="error-msg" id="err-j-nombre"></div>
    </div>
    <div class="form-group">
      <label class="form-label">Categoría *</label>
      <input type="text" class="form-control" id="j-categoria">
      <div class="error-msg" id="err-j-categoria"></div>
    </div>
    <div class="form-group">
      <label class="form-label">Estado *</label>
      <select class="form-control" id="j-estado">
        <option value="abierta">Abierta</option>
        <option value="cerrada">Cerrada</option>
        <option value="en_juego">En juego</option>
      </select>
      <div class="error-msg" id="err-j-estado"></div>
    </div>
    <div class="form-actions">
      <button class="btn" onclick="closeModal('modal-juego')">Cancelar</button>
      <button class="btn btn-primary" onclick="submitForm('juegos')">Guardar</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-billetera">
  <div class="modal">
    <h2 id="title-billetera">Nueva Billetera</h2>
    <div class="form-group">
      <label class="form-label">Saldo disponible *</label>
      <input type="number" step="0.01" class="form-control" id="b-saldo">
      <div class="error-msg" id="err-b-saldo"></div>
    </div>
    <div class="form-group">
      <label class="form-label">Moneda *</label>
      <select class="form-control" id="b-moneda">
        <option value="EUR">EUR</option>
        <option value="USD">USD</option>
        <option value="GBP">GBP</option>
      </select>
      <div class="error-msg" id="err-b-moneda"></div>
    </div>
    <div class="form-actions">
      <button class="btn" onclick="closeModal('modal-billetera')">Cancelar</button>
      <button class="btn btn-primary" onclick="submitForm('billeteras')">Guardar</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-chat">
  <div class="modal">
    <h2 id="title-chat">Nuevo Chat</h2>
    <div class="form-group">
      <label class="form-label">Nombre *</label>
      <input type="text" class="form-control" id="c-nombre">
      <div class="error-msg" id="err-c-nombre"></div>
    </div>
    <div class="form-group">
      <label class="form-label">Fecha creación *</label>
      <input type="date" class="form-control" id="c-fecha">
      <div class="error-msg" id="err-c-fecha"></div>
    </div>
    <div class="form-group">
      <label class="form-label">Activo</label>
      <select class="form-control" id="c-activo">
        <option value="true">Sí</option>
        <option value="false">No</option>
      </select>
    </div>
    <div class="form-actions">
      <button class="btn" onclick="closeModal('modal-chat')">Cancelar</button>
      <button class="btn btn-primary" onclick="submitForm('chats')">Guardar</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-ranking">
  <div class="modal">
    <h2 id="title-ranking">Nuevo Ranking</h2>
    <div class="form-group">
      <label class="form-label">User ID *</label>
      <input type="number" class="form-control" id="r-user_id">
      <div class="error-msg" id="err-r-user_id"></div>
    </div>
    <div class="form-group">
      <label class="form-label">Posición *</label>
      <input type="number" class="form-control" id="r-posicion">
      <div class="error-msg" id="err-r-posicion"></div>
    </div>
    <div class="form-group">
      <label class="form-label">Puntos *</label>
      <input type="number" class="form-control" id="r-puntos">
      <div class="error-msg" id="err-r-puntos"></div>
    </div>
    <div class="form-group">
      <label class="form-label">Total ganado *</label>
      <input type="number" step="0.01" class="form-control" id="r-total_ganado">
      <div class="error-msg" id="err-r-total_ganado"></div>
    </div>
    <div class="form-actions">
      <button class="btn" onclick="closeModal('modal-ranking')">Cancelar</button>
      <button class="btn btn-primary" onclick="submitForm('rankings')">Guardar</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-parametro-ganancia">
  <div class="modal">
    <h2 id="title-parametro-ganancia">Nuevo Parámetro</h2>

    <div class="form-group">
      <label class="form-label">ID Juego *</label>
      <input type="number" class="form-control" id="pg-juego_id">
      <div class="error-msg" id="err-pg-juego_id"></div>
    </div>

    <div class="form-group">
      <label class="form-label">Multiplicación por juego *</label>
      <input type="number" step="0.01" class="form-control" id="pg-multiplicacion">
      <div class="error-msg" id="err-pg-multiplicacion"></div>
    </div>

    <div class="form-group">
      <label class="form-label">Bonus por racha *</label>
      <input type="number" step="0.01" class="form-control" id="pg-bonus">
      <div class="error-msg" id="err-pg-bonus"></div>
    </div>

    <div class="form-actions">
      <button class="btn" onclick="closeModal('modal-parametro-ganancia')">Cancelar</button>
      <button class="btn btn-primary" onclick="submitForm('parametros-ganancia')">Guardar</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modal-confirm"><div class="confirm-modal"><div class="confirm-icon">🗑️</div><h3>¿Eliminar registro?</h3><p id="confirm-text"></p><div><button class="btn" onclick="closeModal('modal-confirm')">Cancelar</button><button class="btn btn-danger" id="confirm-ok-btn">Sí, eliminar</button></div></div></div>
<div id="toast"></div>

<script>
// =========================== STATE ===========================
let state = {};
['usuarios', 'settings', 'apuestas', 'notificaciones', 'juegos', 'billeteras', 'chats', 'rankings', 'parametros-ganancia'].forEach(k => {
  state[k] = { page: 1, per: 6, sort: 'id', dir: 1, total: 0, lastPage: 1 };
});
state['settings'].sort = 'clave';
state['rankings'].sort = 'id';
let editing = {};
let deleteFn = null;

// =========================== NAV ===========================
function goTo(page) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  const el = document.getElementById('page-admin-' + page);
  if (el) el.classList.add('active');

  if (page === 'usuarios') renderTable('usuarios');
  if (page === 'settings') renderTable('settings');
  if (page === 'notificaciones') renderTable('notificaciones');
  if (page === 'apuestas') renderTable('apuestas');
  if (page === 'juegos') renderTable('juegos');
  if (page === 'billeteras') renderTable('billeteras');
  if (page === 'chats') renderTable('chats');
  if (page === 'rankings') renderTable('rankings');
  if (page === 'parametros-ganancia') renderTable('parametros-ganancia');
  if (page === 'dashboard') loadDashboardStats();
}

// =========================== MODAL ===========================
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); clearErrors(); }
function clearErrors() { document.querySelectorAll('.error-msg').forEach(e => e.textContent = ''); document.querySelectorAll('.form-control').forEach(e => e.classList.remove('error')); }
function setErr(fId, eId, msg) { const f = document.getElementById(fId); const e = document.getElementById(eId); if (f) f.classList.add('error'); if (e) e.textContent = msg; }
function toast(msg, type = 'success') { const t = document.getElementById('toast'); t.textContent = (type === 'success' ? '✓ ' : type === 'danger' ? '✗ ' : '⚠ ') + msg; t.className = 'show ' + type; setTimeout(() => { t.className = ''; }, 2800); }

// =========================== API ===========================
async function fetchData(key) {
  try {
    const s = state[key];
    let url = `/admin/${key}/data?per=${s.per}&page=${s.page}&sort=${s.sort}&dir=${s.dir === 1 ? 'asc' : 'desc'}`;
    const searchEl = document.getElementById('search-' + key);
    if (searchEl && searchEl.value) url += `&search=${encodeURIComponent(searchEl.value)}`;
    if (key === 'usuarios') { const vip = document.getElementById('filter-vip-u')?.value; if (vip && vip !== '') url += `&nivel_vip=${vip}`; }
    if (key === 'settings') { const act = document.getElementById('filter-activo-s')?.value; if (act && act !== '') url += `&activo=${act}`; }
    
    if (key === 'notificaciones') {
        const tipo = document.getElementById('filter-tipo-n')?.value;
        const leido = document.getElementById('filter-leido-n')?.value;
        if (tipo && tipo !== '') url += `&tipo=${tipo}`;
        if (leido && leido !== '') url += `&leido=${leido}`;
    }

    if (key === 'juegos') {
      const estado = document.getElementById('filter-estado-j')?.value;
      if (estado && estado !== '') url += `&estado=${estado}`;
    }
    if (key === 'billeteras') {
      const moneda = document.getElementById('filter-moneda-b')?.value;
      if (moneda && moneda !== '') url += `&moneda=${moneda}`;
    }
    if (key === 'chats') {
      const activo = document.getElementById('filter-activo-c')?.value;
      if (activo && activo !== '') url += `&activo=${activo}`;
    }
    
    const response = await fetch(url);
    const data = await response.json();
    return data;
  } catch (error) { toast('Error al cargar datos', 'danger'); return { data: [], total: 0, current_page: 1, last_page: 1 }; }
}

async function saveToAPI(key, data, id = null) {
  try {
    const url = id ? `/admin/${key}/${id}` : `/admin/${key}`;
    const method = id ? 'PUT' : 'POST';
    const response = await fetch(url, {
      method: method,
      headers: { 
        'Content-Type': 'application/json',
        'Accept': 'application/json', // Esto obliga a Laravel a no devolver HTML
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
      },
      body: JSON.stringify(data)
    });
    
    const result = await response.json();
    
    if (response.ok) { 
      toast(result.message || 'Guardado correctamente', 'success'); 
      return result; 
    } else { 
      if (result.errors) { 
        Object.keys(result.errors).forEach(f => { 
          if (key === 'usuarios') { 
            if (f === 'name') setErr('u-nombre', 'err-u-nombre', result.errors[f][0]); 
            if (f === 'email') setErr('u-email', 'err-u-email', result.errors[f][0]); 
            if (f === 'password') setErr('u-password', 'err-u-password', result.errors[f][0]); 
          } 
          if (key === 'settings') { 
            if (f === 'clave') setErr('s-clave', 'err-s-clave', result.errors[f][0]); 
            if (f === 'valor') setErr('s-valor', 'err-s-valor', result.errors[f][0]); 
          }
          
          if (key === 'apuestas') {
            if (f === 'user_id') setErr('a-user_id', 'err-a-user_id', result.errors[f][0]);
            if (f === 'juego_id') setErr('a-juego_id', 'err-a-juego_id', result.errors[f][0]);
            if (f === 'monto') setErr('a-monto', 'err-a-monto', result.errors[f][0]);
            if (f === 'cuota') setErr('a-cuota', 'err-a-cuota', result.errors[f][0]);
            if (f === 'fecha') setErr('a-fecha', 'err-a-fecha', result.errors[f][0]);
          }

          if (key === 'juegos') {
            if (f === 'nombre') setErr('j-nombre', 'err-j-nombre', result.errors[f][0]);
            if (f === 'categoria') setErr('j-categoria', 'err-j-categoria', result.errors[f][0]);
            if (f === 'estado') setErr('j-estado', 'err-j-estado', result.errors[f][0]);
          }
          if (key === 'billeteras') {
            if (f === 'saldoDisponible') setErr('b-saldo', 'err-b-saldo', result.errors[f][0]);
            if (f === 'moneda') setErr('b-moneda', 'err-b-moneda', result.errors[f][0]);
          }
          if (key === 'chats') {
            if (f === 'nombre') setErr('c-nombre', 'err-c-nombre', result.errors[f][0]);
            if (f === 'fechaCreacion') setErr('c-fecha', 'err-c-fecha', result.errors[f][0]);
          }
          if (key === 'rankings') {
            if (f === 'user_id') setErr('r-user_id', 'err-r-user_id', result.errors[f][0]);
            if (f === 'posicion') setErr('r-posicion', 'err-r-posicion', result.errors[f][0]);
            if (f === 'puntos') setErr('r-puntos', 'err-r-puntos', result.errors[f][0]);
            if (f === 'total_ganado') setErr('r-total_ganado', 'err-r-total_ganado', result.errors[f][0]);
          }
          if (key === 'parametros-ganancia') {
            if (f === 'juego_id') setErr('pg-juego_id', 'err-pg-juego_id', result.errors[f][0]);
            if (f === 'multiplicacion_por_juego') setErr('pg-multiplicacion', 'err-pg-multiplicacion', result.errors[f][0]);
            if (f === 'bonus_por_racha') setErr('pg-bonus', 'err-pg-bonus', result.errors[f][0]);
          }
        }); 
      } 
      toast(result.message || 'Error de validación', 'danger'); 
      return null; 
    }
  } catch (error) { 
    toast('Error de conexión. Revisa la consola.', 'danger'); 
    console.error('Error capturado en saveToAPI:', error);
    return null; 
  }
}
async function deleteFromAPI(key, id) {
  try {
    const response = await fetch(`/admin/${key}/${id}`, { 
      method: 'DELETE', 
      headers: { 
        'Content-Type': 'application/json',
        'Accept': 'application/json', // <-- EL ESCUDO ANTI-ERRORES
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
      } 
    });
    
    const result = await response.json();
    
    if (response.ok) { 
      toast(result.message || 'Eliminado correctamente', 'success'); 
      return true; 
    } else { 
      toast(result.message || 'Error al eliminar', 'danger'); 
      return false; 
    }
  } catch (error) { 
    toast('Error de conexión al eliminar. Mira la consola.', 'danger'); 
    console.error('Error en deleteFromAPI:', error);
    return false; 
  }
}

function confirmDelete(key, id) {
  // Arreglamos el texto para que suene bien según lo que borres
  let nombreItem = key;
  if (key === 'usuarios') nombreItem = 'usuario';
  if (key === 'settings') nombreItem = 'configuración';
  if (key === 'apuestas') nombreItem = 'apuesta';
  if (key === 'juegos') nombreItem = 'juego';
  if (key === 'billeteras') nombreItem = 'billetera';
  if (key === 'chats') nombreItem = 'chat';
  if (key === 'rankings') nombreItem = 'ranking';
  if (key === 'parametros-ganancia') nombreItem = 'parámetro';
  if (key === 'notificaciones') nombreItem = 'notificación';

  document.getElementById('confirm-text').textContent = `¿Eliminar ${nombreItem} #${id}?`;
  
  deleteFn = async () => { 
    const success = await deleteFromAPI(key, id); 
    if (success) { 
      closeModal('modal-confirm'); 
      renderTable(key); 
    } 
  };
  
  document.getElementById('confirm-ok-btn').onclick = deleteFn;
  openModal('modal-confirm');
}
// =========================== RENDER ===========================
async function renderTable(key) {
  const result = await fetchData(key);
  const data = result.data || [];
  const total = result.total || 0;
  const currentPage = result.current_page || 1;
  const lastPage = result.last_page || 1;
  state[key].total = total; state[key].lastPage = lastPage; state[key].page = currentPage;
  const countEl = document.getElementById('count-' + key);
  if (countEl) countEl.textContent = total + ' registro' + (total !== 1 ? 's' : '');
  const tbody = document.getElementById('tbody-' + key);
  if (tbody) tbody.innerHTML = rowsFor(key, data);
  const pagEl = document.getElementById('pag-' + key);
  if (pagEl) pagEl.innerHTML = buildPagination(key, currentPage, lastPage, total, data.length);
}

function rowsFor(key, slice) {
  return slice.map(r => {
    const actions = `<td style="display:flex;gap:5px;"><button class="btn btn-sm" onclick="editRecord('${key}',${r.id})">✏️</button><button class="btn btn-sm btn-danger" onclick="confirmDelete('${key}',${r.id})">🗑</button></td>`;
    if (key === 'usuarios') return `<tr><td>#${r.id}</td><td><strong>${r.name}</strong></td><td>${r.email}</td><td>${(r.puntos_fidelidad || 0).toLocaleString()}</td><td>${badgeVip(r.nivel_vip || 0)}</td>${actions}</tr>`;

    if (key === 'juegos') {
      let estadoBadge = '';
      if (r.estado === 'abierta') estadoBadge = '<span class="badge badge-success">Abierta</span>';
      else if (r.estado === 'cerrada') estadoBadge = '<span class="badge badge-danger">Cerrada</span>';
      else estadoBadge = '<span class="badge badge-warning">En juego</span>';

      return `<tr>
        <td>#${r.id}</td>
        <td><strong>${r.nombre}</strong></td>
        <td>${r.categoria}</td>
        <td>${estadoBadge}</td>
        ${actions}
      </tr>`;
    }

    if (key === 'billeteras') {
      return `<tr>
        <td>#${r.id}</td>
        <td>${parseFloat(r.saldoDisponible).toFixed(2)}</td>
        <td><span class="badge badge-info">${r.moneda}</span></td>
        ${actions}
      </tr>`;
    }

    if (key === 'chats') {
      let fecha = r.fechaCreacion ? new Date(r.fechaCreacion).toLocaleDateString() : '';
      return `<tr>
        <td>#${r.id}</td>
        <td><strong>${r.nombre}</strong></td>
        <td>${fecha}</td>
        <td>${r.activo ? '<span class="badge badge-success">Sí</span>' : '<span class="badge badge-danger">No</span>'}</td>
        ${actions}
      </tr>`;
    }

  if (key === 'rankings') {
    return `<tr>
      <td>#${r.id}</td>
      <td>${r.user_id}</td>
      <td>${r.posicion}</td>
      <td>${parseFloat(r.puntos).toFixed(2)}</td>
      <td>${parseFloat(r.total_ganado).toFixed(2)}</td>
      <td style="display:flex;gap:5px;">
        <button class="btn btn-sm" onclick="editRecord('rankings',${r.id})">✏️</button>
        <button class="btn btn-sm btn-danger" onclick="confirmDelete('rankings',${r.id})">🗑</button>
      </td>
    </tr>`;
  }

  if (key === 'parametros-ganancia') {
    return `<tr>
      <td>#${r.id}</td>
      <td>${r.juego_id}</td>
      <td>${parseFloat(r.multiplicacion_por_juego).toFixed(2)}</td>
      <td>${parseFloat(r.bonus_por_racha).toFixed(2)}</td>
      ${actions}
    </tr>`;
  }

    if (key === 'settings') return `<tr><td><strong>${r.clave}</strong></td><td><span class="badge badge-gold">${r.valor}</span></td><td>${r.descripcion || '—'}</td><td>${r.activo ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>'}</td>${actions}</tr>`;
    if (key === 'apuestas') {
      let estadoBadge = '';
      if (r.estado === 'ganada') estadoBadge = '<span class="badge badge-success">Ganada</span>';
      else if (r.estado === 'perdida') estadoBadge = '<span class="badge badge-danger">Perdida</span>';
      else estadoBadge = '<span class="badge badge-warning">Pendiente</span>';

      // Accedemos a las relaciones si existen, sino mostramos el ID
      let userName = r.user ? r.user.name : 'User #' + r.user_id;
      let juegoName = r.juego ? r.juego.nombre : 'Juego #' + r.juego_id;
      // Formatear fecha
      let fecha = new Date(r.fecha).toLocaleDateString();

      return `<tr>
        <td>#${r.id}</td>
        <td><strong>${userName}</strong></td>
        <td>${juegoName}</td>
        <td>$${parseFloat(r.monto).toFixed(2)}</td>
        <td>${parseFloat(r.cuota).toFixed(2)}</td>
        <td>${estadoBadge}</td>
        <td>${fecha}</td>
        ${actions}
      </tr>`;
    }

    if (key === 'notificaciones') {
      let tipoBadge = r.tipo === 'promo' ? '<span class="badge badge-gold">Promo</span>' : 
                      (r.tipo === 'apuesta' ? '<span class="badge badge-success">Apuesta</span>' : '<span class="badge badge-info">' + r.tipo + '</span>');
      let leidoBadge = r.leido ? '<span class="badge badge-success">Sí</span>' : '<span class="badge badge-danger">No</span>';
      let userName = r.user ? r.user.name : 'User #' + r.user_id;
      let fecha = r.fecha ? r.fecha.split(' ')[0] : '';

      return `<tr>
        <td>#${r.id}</td>
        <td><strong>${userName}</strong></td>
        <td>${tipoBadge}</td>
        <td>${r.titulo}</td>
        <td>${leidoBadge}</td>
        <td>${fecha}</td>
        ${actions}
      </tr>`;
    }

    return '';
  }).join('');
}

function buildPagination(key, cur, pages, total, showing) {
  if (pages <= 1) return '';
  let html = `<button class="page-btn" onclick="changePage('${key}',${cur - 1})" ${cur <= 1 ? 'disabled' : ''}>‹</button>`;
  for (let i = 1; i <= pages; i++) { if (pages > 7 && i > 2 && i < pages - 1 && Math.abs(i - cur) > 1) { if (i === 3 || i === pages - 2) html += '<span style="padding:0 4px;">…</span>'; continue; } html += `<button class="page-btn ${i === cur ? 'active' : ''}" onclick="changePage('${key}',${i})">${i}</button>`; }
  html += `<button class="page-btn" onclick="changePage('${key}',${cur + 1})" ${cur >= pages ? 'disabled' : ''}>›</button><span class="page-info">Mostrando ${showing} de ${total}</span>`;
  return html;
}

function changePage(key, p) { if (p < 1 || p > state[key].lastPage) return; state[key].page = p; renderTable(key); }
function filterRender(key) { state[key].page = 1; renderTable(key); }
function sortRender(key, col, th) { const s = state[key]; if (s.sort === col) s.dir *= -1; else { s.sort = col; s.dir = 1; } s.page = 1; document.querySelectorAll(`#page-admin-${key} thead th`).forEach(t => { t.classList.remove('sorted'); const a = t.querySelector('.sort-arrow'); if (a) a.textContent = '⇅'; }); if (th) { th.classList.add('sorted'); const a = th.querySelector('.sort-arrow'); if (a) a.textContent = s.dir === 1 ? '↑' : '↓'; } renderTable(key); }

// =========================== BADGES ===========================
function badgeVip(v) { const map = { 0: '<span class="badge badge-muted">Sin VIP</span>', 1: '<span class="badge badge-warning">VIP 1</span>', 2: '<span class="badge badge-info">VIP 2</span>', 3: '<span class="badge badge-gold">VIP 3</span>' }; return map[v] || '<span class="badge badge-muted">?</span>'; }

// =========================== CRUD ===========================
async function editRecord(key, id) {
  try {
    const response = await fetch(`/admin/${key}/${id}`);
    const record = await response.json();
    editing[key] = id;
    
    if (key === 'notificaciones') {
      document.getElementById('title-notificacion').textContent = 'Editar Notificación';
      document.getElementById('n-user_id').value = record.user_id;
      document.getElementById('n-tipo').value = record.tipo;
      document.getElementById('n-titulo').value = record.titulo;
      document.getElementById('n-mensaje').value = record.mensaje || '';
      document.getElementById('n-leido').value = record.leido ? 'true' : 'false';
      document.getElementById('n-fecha').value = record.fecha ? record.fecha.split(' ')[0] : '';
      openModal('modal-notificacion');
    }

    if (key === 'usuarios') {
      document.getElementById('title-usuario').textContent = 'Editar Usuario';
      document.getElementById('u-nombre').value = record.name;
      document.getElementById('u-email').value = record.email;
      document.getElementById('u-puntos').value = record.puntos_fidelidad || 0;
      document.getElementById('u-vip').value = record.nivel_vip || 0;
      document.getElementById('u-password').value = '';
      openModal('modal-usuario');
    }
    if (key === 'settings') {
      document.getElementById('title-setting').textContent = 'Editar Configuración';
      document.getElementById('s-clave').value = record.clave;
      document.getElementById('s-valor').value = record.valor;
      document.getElementById('s-descripcion').value = record.descripcion || '';
      document.getElementById('s-activo').value = record.activo ? 'true' : 'false';
      openModal('modal-setting');
    }
    if (key === 'apuestas') {
      document.getElementById('title-apuesta').textContent = 'Editar Apuesta';
      document.getElementById('a-user_id').value = record.user_id;
      document.getElementById('a-juego_id').value = record.juego_id;
      document.getElementById('a-monto').value = record.monto;
      document.getElementById('a-cuota').value = record.cuota;
      document.getElementById('a-estado').value = record.estado;
      // Extraemos solo la fecha si viene con hora
      document.getElementById('a-fecha').value = record.fecha ? record.fecha.split(' ')[0] : '';
      openModal('modal-apuesta');
    }
    if (key === 'rankings') {
      document.getElementById('title-ranking').textContent = 'Editar Ranking';
      document.getElementById('r-user_id').value = record.user_id;
      document.getElementById('r-posicion').value = record.posicion;
      document.getElementById('r-puntos').value = record.puntos;
      document.getElementById('r-total_ganado').value = record.total_ganado;
      openModal('modal-ranking');
    }

    if (key === 'parametros-ganancia') {
      document.getElementById('title-parametro-ganancia').textContent = 'Editar Parámetro';
      document.getElementById('pg-juego_id').value = record.juego_id;
      document.getElementById('pg-multiplicacion').value = record.multiplicacion_por_juego;
      document.getElementById('pg-bonus').value = record.bonus_por_racha;
      openModal('modal-parametro-ganancia');
    }

  } catch (error) { toast('Error al cargar', 'danger'); }
}

async function submitForm(key) {
  clearErrors();
  let valid = true;
  let data = {};
  
  if (key === 'usuarios') {
    const nombre = document.getElementById('u-nombre').value.trim();
    const email = document.getElementById('u-email').value.trim();
    const pw = document.getElementById('u-password').value;
    if (!nombre) { setErr('u-nombre', 'err-u-nombre', 'El nombre es obligatorio'); valid = false; }
    if (!email || !/^[^@]+@[^@]+\.[^@]+$/.test(email)) { setErr('u-email', 'err-u-email', 'Email inválido'); valid = false; }
    if (!editing[key] && !pw) { setErr('u-password', 'err-u-password', 'La contraseña es obligatoria'); valid = false; }
    if (!valid) return;
    data = { name: nombre, email: email, puntos_fidelidad: parseInt(document.getElementById('u-puntos').value) || 0, nivel_vip: parseInt(document.getElementById('u-vip').value) || 0 };
    if (pw) data.password = pw;
  }

  if (key === 'juegos') {
  const nombre = document.getElementById('j-nombre').value.trim();
  const categoria = document.getElementById('j-categoria').value.trim();
  const estado = document.getElementById('j-estado').value;

  if (!nombre) { setErr('j-nombre', 'err-j-nombre', 'Obligatorio'); valid = false; }
  if (!categoria) { setErr('j-categoria', 'err-j-categoria', 'Obligatorio'); valid = false; }
  if (!estado) { setErr('j-estado', 'err-j-estado', 'Obligatorio'); valid = false; }
  if (!valid) return;

  data = { nombre, categoria, estado };
  }

  if (key === 'billeteras') {
    const saldoDisponible = parseFloat(document.getElementById('b-saldo').value);
    const moneda = document.getElementById('b-moneda').value;

    if (isNaN(saldoDisponible) || saldoDisponible < 0) { setErr('b-saldo', 'err-b-saldo', 'Saldo inválido'); valid = false; }
    if (!moneda) { setErr('b-moneda', 'err-b-moneda', 'Obligatorio'); valid = false; }
    if (!valid) return;

    data = { saldoDisponible, moneda };
  }

  if (key === 'chats') {
    const nombre = document.getElementById('c-nombre').value.trim();
    const fechaCreacion = document.getElementById('c-fecha').value;
    const activo = document.getElementById('c-activo').value === 'true';

    if (!nombre) { setErr('c-nombre', 'err-c-nombre', 'Obligatorio'); valid = false; }
    if (!fechaCreacion) { setErr('c-fecha', 'err-c-fecha', 'Obligatorio'); valid = false; }
    if (!valid) return;

    data = { nombre, fechaCreacion, activo };
  }

  if (key === 'rankings') {
    const user_id = parseInt(document.getElementById('r-user_id').value);
    const posicion = parseInt(document.getElementById('r-posicion').value);
    const puntos = parseInt(document.getElementById('r-puntos').value);
    const total_ganado = parseFloat(document.getElementById('r-total_ganado').value);

    if (!user_id) { setErr('r-user_id', 'err-r-user_id', 'Obligatorio'); valid = false; }
    if (isNaN(posicion) || posicion < 1) { setErr('r-posicion', 'err-r-posicion', 'Posición inválida'); valid = false; }
    if (isNaN(puntos) || puntos < 0) { setErr('r-puntos', 'err-r-puntos', 'Puntos inválidos'); valid = false; }
    if (isNaN(total_ganado) || total_ganado < 0) { setErr('r-total_ganado', 'err-r-total_ganado', 'Valor inválido'); valid = false; }
    if (!valid) return;

    data = { user_id, posicion, puntos, total_ganado };
  }

  if (key === 'parametros-ganancia') {
    const juego_id = parseInt(document.getElementById('pg-juego_id').value);
    const multiplicacion_por_juego = parseFloat(document.getElementById('pg-multiplicacion').value);
    const bonus_por_racha = parseFloat(document.getElementById('pg-bonus').value);

    if (!juego_id) {
      setErr('pg-juego_id', 'err-pg-juego_id', 'Juego obligatorio');
      valid = false;
    }
    if (isNaN(multiplicacion_por_juego) || multiplicacion_por_juego < 0) {
      setErr('pg-multiplicacion', 'err-pg-multiplicacion', 'Valor inválido');
      valid = false;
    }
    if (isNaN(bonus_por_racha) || bonus_por_racha < 0) {
      setErr('pg-bonus', 'err-pg-bonus', 'Valor inválido');
      valid = false;
    }
    if (!valid) return;

    data = { juego_id, multiplicacion_por_juego, bonus_por_racha };
  }

  if (key === 'notificaciones') {
    const userId = parseInt(document.getElementById('n-user_id').value);
    const tipo = document.getElementById('n-tipo').value;
    const titulo = document.getElementById('n-titulo').value.trim();
    const mensaje = document.getElementById('n-mensaje').value;
    const leido = document.getElementById('n-leido').value === 'true';
    const fecha = document.getElementById('n-fecha').value;

    if (!userId) { setErr('n-user_id', 'err-n-user_id', 'Obligatorio'); valid = false; }
    if (!titulo) { setErr('n-titulo', 'err-n-titulo', 'Obligatorio'); valid = false; }
    if (!fecha) { setErr('n-fecha', 'err-n-fecha', 'Obligatorio'); valid = false; }

    if (!valid) return;
    data = { user_id: userId, tipo: tipo, titulo: titulo, mensaje: mensaje, leido: leido, fecha: fecha };
  }

  if (key === 'settings') {
    const clave = document.getElementById('s-clave').value.trim();
    const valor = parseInt(document.getElementById('s-valor').value);
    if (!clave) { setErr('s-clave', 'err-s-clave', 'La clave es obligatoria'); valid = false; }
    if (isNaN(valor)) { setErr('s-valor', 'err-s-valor', 'El valor debe ser un número'); valid = false; }
    if (!valid) return;
    data = { clave: clave, valor: valor, descripcion: document.getElementById('s-descripcion').value, activo: document.getElementById('s-activo').value === 'true' };
  }

  if (key === 'apuestas') {
    const userId = parseInt(document.getElementById('a-user_id').value);
    const juegoId = parseInt(document.getElementById('a-juego_id').value);
    const monto = parseFloat(document.getElementById('a-monto').value);
    const cuota = parseFloat(document.getElementById('a-cuota').value);
    const estado = document.getElementById('a-estado').value;
    const fecha = document.getElementById('a-fecha').value;

    if (!userId) { setErr('a-user_id', 'err-a-user_id', 'Obligatorio'); valid = false; }
    if (!juegoId) { setErr('a-juego_id', 'err-a-juego_id', 'Obligatorio'); valid = false; }
    if (isNaN(monto) || monto <= 0) { setErr('a-monto', 'err-a-monto', 'Monto inválido'); valid = false; }
    if (isNaN(cuota) || cuota < 1) { setErr('a-cuota', 'err-a-cuota', 'Cuota inválida'); valid = false; }
    if (!fecha) { setErr('a-fecha', 'err-a-fecha', 'Obligatorio'); valid = false; }

    if (!valid) return;
    data = { user_id: userId, juego_id: juegoId, monto: monto, cuota: cuota, estado: estado, fecha: fecha };
  }
  
  const result = await saveToAPI(key, data, editing[key]);
  if (result) { 
    if (key === 'usuarios') closeModal('modal-usuario');
    if (key === 'settings') closeModal('modal-setting');
    if (key === 'apuestas') closeModal('modal-apuesta');
    if (key === 'notificaciones') closeModal('modal-notificacion');
    if (key === 'juegos') closeModal('modal-juego');
    if (key === 'billeteras') closeModal('modal-billetera');
    if (key === 'chats') closeModal('modal-chat');
    if (key === 'rankings') closeModal('modal-ranking');
    if (key === 'parametros-ganancia') closeModal('modal-parametro-ganancia');
    
    editing[key] = null; 
    renderTable(key); 
  }
}

function openNewModal(key) {
  editing[key] = null;
  
  if (key === 'usuarios') {
    document.getElementById('title-usuario').textContent = 'Nuevo Usuario';
    document.getElementById('u-nombre').value = '';
    document.getElementById('u-email').value = '';
    document.getElementById('u-password').value = '';
    document.getElementById('u-puntos').value = '0';
    document.getElementById('u-vip').value = '0';
    openModal('modal-usuario');
  }
  if (key === 'settings') {
    document.getElementById('title-setting').textContent = 'Nueva Configuración';
    document.getElementById('s-clave').value = '';
    document.getElementById('s-valor').value = '';
    document.getElementById('s-descripcion').value = '';
    document.getElementById('s-activo').value = 'true';
    openModal('modal-setting');
  }
  if (key === 'apuestas') {
    document.getElementById('title-apuesta').textContent = 'Nueva Apuesta';
    document.getElementById('a-user_id').value = '';
    document.getElementById('a-juego_id').value = '';
    document.getElementById('a-monto').value = '';
    document.getElementById('a-cuota').value = '';
    document.getElementById('a-estado').value = 'pendiente';
    // Por defecto ponemos la fecha de hoy
    document.getElementById('a-fecha').value = new Date().toISOString().split('T')[0];
    openModal('modal-apuesta');
  }

  if (key === 'notificaciones') {
    document.getElementById('title-notificacion').textContent = 'Nueva Notificación';
    document.getElementById('n-user_id').value = '';
    document.getElementById('n-tipo').value = 'alerta';
    document.getElementById('n-titulo').value = '';
    document.getElementById('n-mensaje').value = '';
    document.getElementById('n-leido').value = 'false';
    document.getElementById('n-fecha').value = new Date().toISOString().split('T')[0];
    openModal('modal-notificacion');
  }

  if (key === 'juegos') {
    document.getElementById('title-juego').textContent = 'Nuevo Juego';
    document.getElementById('j-nombre').value = '';
    document.getElementById('j-categoria').value = '';
    document.getElementById('j-estado').value = 'abierta';
    openModal('modal-juego');
  }

  if (key === 'billeteras') {
    document.getElementById('title-billetera').textContent = 'Nueva Billetera';
    document.getElementById('b-saldo').value = '';
    document.getElementById('b-moneda').value = 'EUR';
    openModal('modal-billetera');
  }

  if (key === 'chats') {
    document.getElementById('title-chat').textContent = 'Nuevo Chat';
    document.getElementById('c-nombre').value = '';
    document.getElementById('c-fecha').value = new Date().toISOString().split('T')[0];
    document.getElementById('c-activo').value = 'true';
    openModal('modal-chat');
  }

  if (key === 'rankings') {
    document.getElementById('title-ranking').textContent = 'Nuevo Ranking';
    document.getElementById('r-user_id').value = '';
    document.getElementById('r-posicion').value = '';
    document.getElementById('r-puntos').value = '';
    document.getElementById('r-total_ganado').value = '';
    openModal('modal-ranking');
  }

  if (key === 'parametros-ganancia') {
    document.getElementById('title-parametro-ganancia').textContent = 'Nuevo Parámetro';
    document.getElementById('pg-juego_id').value = '';
    document.getElementById('pg-multiplicacion').value = '';
    document.getElementById('pg-bonus').value = '';
    openModal('modal-parametro-ganancia');
  }

}

// =========================== DASHBOARD ===========================
  async function loadDashboardStats() {
    try {
      const usersRes = await fetch('/admin/usuarios/data?per=100');
      const usersData = await usersRes.json();
      document.getElementById('stat-usuarios').textContent = usersData.total || usersData.data?.length || 0;

      const apuestasRes = await fetch('/admin/apuestas/data?per=100');
      const apuestasData = await apuestasRes.json();
      document.getElementById('stat-apuestas').textContent = apuestasData.total || apuestasData.data?.length || 0;

      const juegosRes = await fetch('/admin/juegos/data?per=100');
      const juegosData = await juegosRes.json();
      document.getElementById('stat-juegos').textContent = juegosData.total || juegosData.data?.length || 0;

      document.getElementById('actividad-reciente').innerHTML =
        '<div class="activity-list">' +
        '<div class="activity-item">✅ Panel cargado correctamente</div>' +
        '<div class="activity-item">📊 Conectado a la base de datos</div>' +
        '</div>';
    } catch (error) {
      document.getElementById('actividad-reciente').innerHTML = '<div>Error al cargar estadísticas</div>';
    }
  }

// =========================== INIT ===========================
['usuarios', 'settings', 'apuestas', 'notificaciones', 'juegos', 'billeteras', 'chats', 'rankings', 'parametros-ganancia'].forEach(k => renderTable(k));
document.querySelectorAll('.modal-overlay').forEach(m => { m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); }); });
</script>
</body>
</html>