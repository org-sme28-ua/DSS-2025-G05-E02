<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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

  /* ====== PAGE NAV ====== */
  .page { display:none; }
  .page.active { display:flex; min-height:100vh; }

  /* ====== SIDEBAR ====== */
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

  /* ====== MAIN ====== */
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

  /* ====== STAT CARDS ====== */
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

  /* ====== TABLE WRAP ====== */
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

  /* ====== BADGES ====== */
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

  /* ====== BUTTONS ====== */
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
  .btn-success { background:rgba(46,204,113,.18); border-color:rgba(46,204,113,.35); color:#2ecc71; }
  .btn-gold { background:rgba(240,192,64,.18); border-color:rgba(240,192,64,.35); color:#f0c040; }
  .btn-sm { padding:5px 10px; font-size:11px; }
  .btn-icon { padding:5px 8px; font-size:13px; }

  /* ====== PAGINATION ====== */
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

  /* ====== MODALS ====== */
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

  /* ====== FORMS ====== */
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
  select.form-control option { background:#2a0a0a; }
  .error-msg { font-size:11px; color:var(--danger); min-height:14px; }
  .form-actions { display:flex; gap:10px; justify-content:flex-end; padding-top:16px; border-top:1px solid var(--border); margin-top:4px; }
  .form-hint { font-size:11px; color:var(--text-muted); }

  /* ====== TOAST ====== */
  #toast {
    position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(20px);
    background:#2a0a0a; border:1px solid var(--border); border-radius:10px;
    padding:10px 20px; font-size:13px; color:var(--text);
    opacity:0; transition:all .3s; z-index:9999; pointer-events:none; white-space:nowrap;
  }
  #toast.show { opacity:1; transform:translateX(-50%) translateY(0); }
  #toast.success { border-color:rgba(46,204,113,.5); color:#2ecc71; }
  #toast.danger  { border-color:rgba(231,76,60,.5);  color:#e74c3c; }
  #toast.warning { border-color:rgba(243,156,18,.5); color:#f39c12; }

  /* ====== DASHBOARD SPECIFIC ====== */
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

  /* ====== DETAIL CARD ====== */
  .detail-row { display:flex; justify-content:space-between; padding:9px 0; border-bottom:1px solid rgba(255,255,255,0.05); font-size:13px; }
  .detail-row:last-child { border-bottom:none; }
  .detail-key { color:var(--text-muted); }
  .detail-val { font-weight:500; text-align:right; }

  /* ====== TABS ====== */
  .tab-bar { display:flex; gap:4px; margin-bottom:18px; border-bottom:1px solid var(--border); padding-bottom:-1px; }
  .tab-btn {
    padding:8px 16px; border:none; background:none;
    color:var(--text-muted); font-size:13px; font-family:inherit;
    cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1px;
  }
  .tab-btn.active { color:var(--white); border-bottom-color:var(--gold); }
  .tab-btn:hover { color:var(--text); }

  /* ====== RESPONSIVE ====== */
  @media (max-width:768px) {
    .sidebar { display:none; }
    .form-row { grid-template-columns:1fr; }
    .two-col { grid-template-columns:1fr; }
  }
</style>
</head>
<body>

<!-- ===================== HELPER: SIDEBAR ADMIN ===================== -->
<!-- Used as template — each page has its own copy with active state -->

<!-- ======================== PAGE: ADMIN DASHBOARD ======================== -->
<div class="page active" id="page-admin-dashboard">
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-icon">🎰</div>
      <span class="logo-text">Bookie 2.0</span>
      <span class="logo-badge">ADMIN</span>
    </div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item active" onclick="goTo('admin-dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('admin-usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('admin-juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('admin-apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('admin-billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('admin-notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('admin-chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('admin-rankings')"><span class="nav-icon">🏆</span> Rankings</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar">
      <span class="topbar-title">Dashboard</span>
      <input class="topbar-search" placeholder="🔍  Buscar en el panel...">
      <div class="user-chip">
        <div class="user-avatar">AD</div>
        <span style="font-size:13px;font-weight:500;">Admin</span>
      </div>
    </div>
    <div class="content">
      <div class="page-header">
        <div>
          <div class="page-title">Panel de Administración</div>
          <div class="page-subtitle">Visión general del sistema · Bookie 2.0</div>
        </div>
      </div>

      <div class="stats-row">
        <div class="stat-card">
          <div class="stat-label">Usuarios totales</div>
          <div class="stat-value">1,247</div>
          <div class="stat-delta delta-up">▲ +23 esta semana</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Apuestas activas</div>
          <div class="stat-value">384</div>
          <div class="stat-delta delta-up">▲ +12 hoy</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Juegos disponibles</div>
          <div class="stat-value">14</div>
          <div class="stat-delta" style="color:var(--text-muted)">─ Sin cambios</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Volumen total (€)</div>
          <div class="stat-value">98.4K</div>
          <div class="stat-delta delta-up">▲ +8.2% este mes</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Chats activos</div>
          <div class="stat-value">37</div>
          <div class="stat-delta delta-down">▼ -3 vs ayer</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Notif. pendientes</div>
          <div class="stat-value">129</div>
          <div class="stat-delta delta-up">▲ +41 no leídas</div>
        </div>
      </div>

      <div class="two-col">
        <div class="table-wrap">
          <div class="toolbar" style="padding:12px 16px;">
            <span style="font-size:13px;font-weight:600;color:var(--white);">Actividad reciente</span>
          </div>
          <div style="padding:4px 16px 4px;">
            <div class="activity-list">
              <div class="activity-item"><div class="activity-dot" style="background:#2ecc71"></div><div class="activity-text"><strong>Usuario123</strong> ganó €2,250 en Ruleta</div><div class="activity-time">hace 3 min</div></div>
              <div class="activity-item"><div class="activity-dot" style="background:#3498db"></div><div class="activity-text"><strong>Cute45</strong> realizó un ingreso de €500</div><div class="activity-time">hace 8 min</div></div>
              <div class="activity-item"><div class="activity-dot" style="background:#e74c3c"></div><div class="activity-text"><strong>Fg3453</strong> fue eliminado por admin</div><div class="activity-time">hace 21 min</div></div>
              <div class="activity-item"><div class="activity-dot" style="background:#f39c12"></div><div class="activity-text">Juego <strong>Blackjack</strong> cerrado manualmente</div><div class="activity-time">hace 35 min</div></div>
              <div class="activity-item"><div class="activity-dot" style="background:#2ecc71"></div><div class="activity-text"><strong>Guitierrez33556</strong> nueva apuesta €300</div><div class="activity-time">hace 1h</div></div>
              <div class="activity-item"><div class="activity-dot" style="background:#9b59b6"></div><div class="activity-text">Nuevo usuario <strong>BettingKing99</strong> registrado</div><div class="activity-time">hace 1h</div></div>
              <div class="activity-item"><div class="activity-dot" style="background:#3498db"></div><div class="activity-text"><strong>Apuestaslover434</strong> retiró €150</div><div class="activity-time">hace 2h</div></div>
            </div>
          </div>
        </div>
        <div class="table-wrap">
          <div class="toolbar" style="padding:12px 16px;">
            <span style="font-size:13px;font-weight:600;color:var(--white);">Acceso rápido</span>
          </div>
          <div style="padding:16px;display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <button class="btn" style="flex-direction:column;padding:16px;height:auto;gap:6px;" onclick="goTo('admin-usuarios')">
              <span style="font-size:22px;">👥</span><span>Usuarios</span>
            </button>
            <button class="btn" style="flex-direction:column;padding:16px;height:auto;gap:6px;" onclick="goTo('admin-juegos')">
              <span style="font-size:22px;">🎮</span><span>Juegos</span>
            </button>
            <button class="btn" style="flex-direction:column;padding:16px;height:auto;gap:6px;" onclick="goTo('admin-apuestas')">
              <span style="font-size:22px;">📋</span><span>Apuestas</span>
            </button>
            <button class="btn" style="flex-direction:column;padding:16px;height:auto;gap:6px;" onclick="goTo('admin-billeteras')">
              <span style="font-size:22px;">💳</span><span>Billeteras</span>
            </button>
            <button class="btn" style="flex-direction:column;padding:16px;height:auto;gap:6px;" onclick="goTo('admin-notificaciones')">
              <span style="font-size:22px;">🔔</span><span>Notificaciones</span>
            </button>
            <button class="btn" style="flex-direction:column;padding:16px;height:auto;gap:6px;" onclick="goTo('admin-rankings')">
              <span style="font-size:22px;">🏆</span><span>Rankings</span>
            </button>
          </div>
        </div>
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
      <button class="nav-item" onclick="goTo('admin-dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item active" onclick="goTo('admin-usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('admin-juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('admin-apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('admin-billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('admin-notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('admin-chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('admin-rankings')"><span class="nav-icon">🏆</span> Rankings</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar">
      <span class="topbar-title">Gestión de Usuarios</span>
      <input class="topbar-search" placeholder="🔍  Buscar...">
      <div class="user-chip"><div class="user-avatar">AD</div><span style="font-size:13px;font-weight:500;">Admin</span></div>
    </div>
    <div class="content">
      <div class="page-header">
        <div><div class="page-title">Usuarios</div><div class="page-subtitle">Gestión completa de jugadores y operadores</div></div>
        <button class="btn btn-primary" onclick="openModal('modal-usuario')">＋ Nuevo usuario</button>
      </div>
      <div class="table-wrap">
        <div class="toolbar">
          <input class="input-sm" placeholder="🔍 Buscar por nombre, email..." style="width:220px;" id="search-usuarios" oninput="filterRender('usuarios')">
          <select class="input-sm" id="filter-vip-u" onchange="filterRender('usuarios')">
            <option value="">Todos los niveles VIP</option>
            <option value="0">Sin VIP</option>
            <option value="1">VIP 1</option>
            <option value="2">VIP 2</option>
            <option value="3">VIP 3</option>
          </select>
          <div class="toolbar-right">
            <span id="count-usuarios" style="font-size:11px;color:var(--text-muted);"></span>
          </div>
        </div>
        <table>
          <thead>
            <tr>
              <th onclick="sortRender('usuarios','id',this)">ID <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('usuarios','nombre',this)">Nombre <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('usuarios','email',this)">Email <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('usuarios','puntosFidelidad',this)">Puntos fidelidad <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('usuarios','nivelVIP',this)">Nivel VIP <span class="sort-arrow">⇅</span></th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="tbody-usuarios"></tbody>
        </table>
        <div class="pagination" id="pag-usuarios"></div>
      </div>
    </div>
  </div>
</div>

<!-- ======================== PAGE: ADMIN JUEGOS ======================== -->
<div class="page" id="page-admin-juegos">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item" onclick="goTo('admin-dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('admin-usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item active" onclick="goTo('admin-juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('admin-apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('admin-billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('admin-notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('admin-chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('admin-rankings')"><span class="nav-icon">🏆</span> Rankings</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar">
      <span class="topbar-title">Gestión de Juegos</span>
      <input class="topbar-search" placeholder="🔍  Buscar...">
      <div class="user-chip"><div class="user-avatar">AD</div><span style="font-size:13px;font-weight:500;">Admin</span></div>
    </div>
    <div class="content">
      <div class="page-header">
        <div><div class="page-title">Juegos</div><div class="page-subtitle">Catálogo de juegos y su estado</div></div>
        <button class="btn btn-primary" onclick="openModal('modal-juego')">＋ Nuevo juego</button>
      </div>
      <div class="table-wrap">
        <div class="toolbar">
          <input class="input-sm" placeholder="🔍 Buscar por nombre..." style="width:200px;" id="search-juegos" oninput="filterRender('juegos')">
          <select class="input-sm" id="filter-cat-j" onchange="filterRender('juegos')">
            <option value="">Todas las categorías</option>
            <option value="casino">Casino</option>
            <option value="deportes">Deportes</option>
            <option value="virtual">Virtual</option>
          </select>
          <select class="input-sm" id="filter-estado-j" onchange="filterRender('juegos')">
            <option value="">Todos los estados</option>
            <option value="abierta">Abierta</option>
            <option value="cerrada">Cerrada</option>
            <option value="en_juego">En juego</option>
          </select>
          <div class="toolbar-right">
            <span id="count-juegos" style="font-size:11px;color:var(--text-muted);"></span>
          </div>
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

<!-- ======================== PAGE: ADMIN APUESTAS ======================== -->
<div class="page" id="page-admin-apuestas">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item" onclick="goTo('admin-dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('admin-usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('admin-juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item active" onclick="goTo('admin-apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('admin-billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('admin-notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('admin-chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('admin-rankings')"><span class="nav-icon">🏆</span> Rankings</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar">
      <span class="topbar-title">Gestión de Apuestas</span>
      <input class="topbar-search" placeholder="🔍  Buscar...">
      <div class="user-chip"><div class="user-avatar">AD</div><span style="font-size:13px;font-weight:500;">Admin</span></div>
    </div>
    <div class="content">
      <div class="page-header">
        <div><div class="page-title">Apuestas</div><div class="page-subtitle">Historial y gestión de todas las apuestas</div></div>
        <button class="btn btn-primary" onclick="openModal('modal-apuesta')">＋ Nueva apuesta</button>
      </div>
      <div class="table-wrap">
        <div class="toolbar">
          <input class="input-sm" placeholder="🔍 Buscar usuario, juego..." style="width:210px;" id="search-apuestas" oninput="filterRender('apuestas')">
          <select class="input-sm" id="filter-estado-a" onchange="filterRender('apuestas')">
            <option value="">Todos los estados</option>
            <option value="pendiente">Pendiente</option>
            <option value="ganada">Ganada</option>
            <option value="perdida">Perdida</option>
          </select>
          <input type="date" class="input-sm" id="filter-fecha-a" onchange="filterRender('apuestas')" title="Filtrar por fecha">
          <div class="toolbar-right">
            <span id="count-apuestas" style="font-size:11px;color:var(--text-muted);"></span>
          </div>
        </div>
        <table>
          <thead>
            <tr>
              <th onclick="sortRender('apuestas','id',this)">ID <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('apuestas','usuario',this)">Usuario <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('apuestas','juego',this)">Juego <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('apuestas','monto',this)">Monto (€) <span class="sort-arrow">⇅</span></th>
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

<!-- ======================== PAGE: ADMIN BILLETERAS ======================== -->
<div class="page" id="page-admin-billeteras">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item" onclick="goTo('admin-dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('admin-usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('admin-juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('admin-apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item active" onclick="goTo('admin-billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('admin-notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('admin-chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('admin-rankings')"><span class="nav-icon">🏆</span> Rankings</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar">
      <span class="topbar-title">Billeteras</span>
      <input class="topbar-search" placeholder="🔍  Buscar...">
      <div class="user-chip"><div class="user-avatar">AD</div><span style="font-size:13px;font-weight:500;">Admin</span></div>
    </div>
    <div class="content">
      <div class="page-header">
        <div><div class="page-title">Billeteras</div><div class="page-subtitle">Control de saldos y monedas de usuarios</div></div>
        <button class="btn btn-primary" onclick="openModal('modal-billetera')">＋ Nueva billetera</button>
      </div>
      <div class="table-wrap">
        <div class="toolbar">
          <input class="input-sm" placeholder="🔍 Buscar usuario o moneda..." style="width:220px;" id="search-billeteras" oninput="filterRender('billeteras')">
          <select class="input-sm" id="filter-moneda" onchange="filterRender('billeteras')">
            <option value="">Todas las monedas</option>
            <option value="EUR">EUR</option>
            <option value="USD">USD</option>
            <option value="GBP">GBP</option>
          </select>
          <div class="toolbar-right">
            <span id="count-billeteras" style="font-size:11px;color:var(--text-muted);"></span>
          </div>
        </div>
        <table>
          <thead>
            <tr>
              <th onclick="sortRender('billeteras','id',this)">ID <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('billeteras','usuario',this)">Usuario <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('billeteras','saldo',this)">Saldo disponible <span class="sort-arrow">⇅</span></th>
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

<!-- ======================== PAGE: ADMIN NOTIFICACIONES ======================== -->
<div class="page" id="page-admin-notificaciones">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item" onclick="goTo('admin-dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('admin-usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('admin-juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('admin-apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('admin-billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item active" onclick="goTo('admin-notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('admin-chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('admin-rankings')"><span class="nav-icon">🏆</span> Rankings</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar">
      <span class="topbar-title">Notificaciones</span>
      <input class="topbar-search" placeholder="🔍  Buscar...">
      <div class="user-chip"><div class="user-avatar">AD</div><span style="font-size:13px;font-weight:500;">Admin</span></div>
    </div>
    <div class="content">
      <div class="page-header">
        <div><div class="page-title">Notificaciones</div><div class="page-subtitle">Gestión de notificaciones del sistema</div></div>
        <button class="btn btn-primary" onclick="openModal('modal-notificacion')">＋ Nueva notificación</button>
      </div>
      <div class="table-wrap">
        <div class="toolbar">
          <input class="input-sm" placeholder="🔍 Buscar título, usuario..." style="width:210px;" id="search-notificaciones" oninput="filterRender('notificaciones')">
          <select class="input-sm" id="filter-tipo-n" onchange="filterRender('notificaciones')">
            <option value="">Todos los tipos</option>
            <option value="apuesta">Apuesta</option>
            <option value="promo">Promo</option>
            <option value="alerta">Alerta</option>
          </select>
          <select class="input-sm" id="filter-leido-n" onchange="filterRender('notificaciones')">
            <option value="">Leídas y no leídas</option>
            <option value="false">No leídas</option>
            <option value="true">Leídas</option>
          </select>
          <div class="toolbar-right">
            <span id="count-notificaciones" style="font-size:11px;color:var(--text-muted);"></span>
          </div>
        </div>
        <table>
          <thead>
            <tr>
              <th onclick="sortRender('notificaciones','id',this)">ID <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('notificaciones','usuario',this)">Usuario <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('notificaciones','tipo',this)">Tipo <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('notificaciones','titulo',this)">Título <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('notificaciones','fechaHora',this)">Fecha/Hora <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('notificaciones','leido',this)">Leída <span class="sort-arrow">⇅</span></th>
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

<!-- ======================== PAGE: ADMIN CHATS ======================== -->
<div class="page" id="page-admin-chats">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-icon">🎰</div><span class="logo-text">Bookie 2.0</span><span class="logo-badge">ADMIN</span></div>
    <nav class="sidebar-nav">
      <div class="sidebar-section">Panel</div>
      <button class="nav-item" onclick="goTo('admin-dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('admin-usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('admin-juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('admin-apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('admin-billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('admin-notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item active" onclick="goTo('admin-chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item" onclick="goTo('admin-rankings')"><span class="nav-icon">🏆</span> Rankings</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar">
      <span class="topbar-title">Chats y Mensajes</span>
      <input class="topbar-search" placeholder="🔍  Buscar...">
      <div class="user-chip"><div class="user-avatar">AD</div><span style="font-size:13px;font-weight:500;">Admin</span></div>
    </div>
    <div class="content">
      <div class="page-header">
        <div><div class="page-title">Chats y Mensajes</div><div class="page-subtitle">Supervisión de conversaciones y moderación</div></div>
        <button class="btn btn-primary" onclick="openModal('modal-chat')">＋ Nuevo chat</button>
      </div>

      <div class="tab-bar">
        <button class="tab-btn active" onclick="switchAdminTab('chats',this)">Chats</button>
        <button class="tab-btn" onclick="switchAdminTab('mensajes',this)">Mensajes</button>
      </div>

      <div id="tab-chats">
        <div class="table-wrap">
          <div class="toolbar">
            <input class="input-sm" placeholder="🔍 Buscar por nombre..." style="width:200px;" id="search-chats" oninput="filterRender('chats')">
            <select class="input-sm" id="filter-activo-c" onchange="filterRender('chats')">
              <option value="">Todos</option>
              <option value="true">Activos</option>
              <option value="false">Inactivos</option>
            </select>
            <div class="toolbar-right"><span id="count-chats" style="font-size:11px;color:var(--text-muted);"></span></div>
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

      <div id="tab-mensajes" style="display:none;">
        <div class="table-wrap">
          <div class="toolbar">
            <input class="input-sm" placeholder="🔍 Buscar contenido, emisor..." style="width:220px;" id="search-mensajes" oninput="filterRender('mensajes')">
            <select class="input-sm" id="filter-editado-m" onchange="filterRender('mensajes')">
              <option value="">Todos</option>
              <option value="true">Editados</option>
              <option value="false">Sin editar</option>
            </select>
            <div class="toolbar-right"><span id="count-mensajes" style="font-size:11px;color:var(--text-muted);"></span></div>
          </div>
          <table>
            <thead>
              <tr>
                <th onclick="sortRender('mensajes','id',this)">ID <span class="sort-arrow">⇅</span></th>
                <th onclick="sortRender('mensajes','chat',this)">Chat <span class="sort-arrow">⇅</span></th>
                <th onclick="sortRender('mensajes','emisor',this)">Emisor <span class="sort-arrow">⇅</span></th>
                <th onclick="sortRender('mensajes','receptor',this)">Receptor <span class="sort-arrow">⇅</span></th>
                <th onclick="sortRender('mensajes','contenido',this)">Contenido <span class="sort-arrow">⇅</span></th>
                <th onclick="sortRender('mensajes','editado',this)">Editado <span class="sort-arrow">⇅</span></th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="tbody-mensajes"></tbody>
          </table>
          <div class="pagination" id="pag-mensajes"></div>
        </div>
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
      <button class="nav-item" onclick="goTo('admin-dashboard')"><span class="nav-icon">📊</span> Dashboard</button>
      <div class="nav-divider"></div>
      <div class="sidebar-section">Gestión</div>
      <button class="nav-item" onclick="goTo('admin-usuarios')"><span class="nav-icon">👥</span> Usuarios</button>
      <button class="nav-item" onclick="goTo('admin-juegos')"><span class="nav-icon">🎮</span> Juegos</button>
      <button class="nav-item" onclick="goTo('admin-apuestas')"><span class="nav-icon">📋</span> Apuestas</button>
      <button class="nav-item" onclick="goTo('admin-billeteras')"><span class="nav-icon">💳</span> Billeteras</button>
      <button class="nav-item" onclick="goTo('admin-notificaciones')"><span class="nav-icon">🔔</span> Notificaciones</button>
      <button class="nav-item" onclick="goTo('admin-chats')"><span class="nav-icon">💬</span> Chats / Mensajes</button>
      <button class="nav-item active" onclick="goTo('admin-rankings')"><span class="nav-icon">🏆</span> Rankings</button>
    </nav>
    <div class="sidebar-bottom">
      <button class="nav-item"><span class="nav-icon">⚙</span> Configuración</button>
      <button class="nav-item"><span class="nav-icon">🚪</span> Cerrar sesión</button>
    </div>
  </aside>
  <div class="main">
    <div class="topbar">
      <span class="topbar-title">Rankings</span>
      <input class="topbar-search" placeholder="🔍  Buscar...">
      <div class="user-chip"><div class="user-avatar">AD</div><span style="font-size:13px;font-weight:500;">Admin</span></div>
    </div>
    <div class="content">
      <div class="page-header">
        <div><div class="page-title">Rankings</div><div class="page-subtitle">Tabla de clasificación de jugadores</div></div>
        <button class="btn btn-primary" onclick="openModal('modal-ranking')">＋ Nueva entrada</button>
      </div>
      <div class="table-wrap">
        <div class="toolbar">
          <input class="input-sm" placeholder="🔍 Buscar usuario..." style="width:200px;" id="search-rankings" oninput="filterRender('rankings')">
          <select class="input-sm" id="filter-activo-r" onchange="filterRender('rankings')">
            <option value="">Todos</option>
            <option value="true">Activos</option>
            <option value="false">Inactivos</option>
          </select>
          <div class="toolbar-right"><span id="count-rankings" style="font-size:11px;color:var(--text-muted);"></span></div>
        </div>
        <table>
          <thead>
            <tr>
              <th onclick="sortRender('rankings','posicion',this)">Posición <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('rankings','usuario',this)">Usuario <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('rankings','puntos',this)">Puntos <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('rankings','totalGanado',this)">Total ganado (€) <span class="sort-arrow">⇅</span></th>
              <th onclick="sortRender('rankings','activo',this)">Activo <span class="sort-arrow">⇅</span></th>
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


<!-- ====================================================
     MODALES
===================================================== -->

<!-- Modal Usuario -->
<div class="modal-overlay" id="modal-usuario">
  <div class="modal">
    <h2 id="title-usuario">Nuevo Usuario</h2>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Nombre *</label>
        <input type="text" class="form-control" id="u-nombre" placeholder="Nombre de usuario">
        <div class="error-msg" id="err-u-nombre"></div>
      </div>
      <div class="form-group">
        <label class="form-label">Email *</label>
        <input type="email" class="form-control" id="u-email" placeholder="usuario@email.com">
        <div class="error-msg" id="err-u-email"></div>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Contraseña *</label>
        <input type="password" class="form-control" id="u-password" placeholder="Mínimo 8 caracteres">
        <div class="error-msg" id="err-u-password"></div>
      </div>
      <div class="form-group">
        <label class="form-label">Puntos fidelidad</label>
        <input type="number" class="form-control" id="u-puntos" placeholder="0" min="0">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Nivel VIP</label>
      <select class="form-control" id="u-vip">
        <option value="0">Sin VIP</option>
        <option value="1">VIP 1</option>
        <option value="2">VIP 2</option>
        <option value="3">VIP 3</option>
      </select>
    </div>
    <div class="form-actions">
      <button class="btn" onclick="closeModal('modal-usuario')">Cancelar</button>
      <button class="btn btn-primary" onclick="submitForm('usuarios')">Guardar</button>
    </div>
  </div>
</div>

<!-- Modal Juego -->
<div class="modal-overlay" id="modal-juego">
  <div class="modal">
    <h2 id="title-juego">Nuevo Juego</h2>
    <div class="form-group">
      <label class="form-label">Nombre *</label>
      <input type="text" class="form-control" id="j-nombre" placeholder="Nombre del juego">
      <div class="error-msg" id="err-j-nombre"></div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Categoría *</label>
        <select class="form-control" id="j-categoria">
          <option value="">Selecciona...</option>
          <option value="casino">Casino</option>
          <option value="deportes">Deportes</option>
          <option value="virtual">Virtual</option>
        </select>
        <div class="error-msg" id="err-j-categoria"></div>
      </div>
      <div class="form-group">
        <label class="form-label">Estado</label>
        <select class="form-control" id="j-estado">
          <option value="abierta">Abierta</option>
          <option value="cerrada">Cerrada</option>
          <option value="en_juego">En juego</option>
        </select>
      </div>
    </div>
    <div class="form-actions">
      <button class="btn" onclick="closeModal('modal-juego')">Cancelar</button>
      <button class="btn btn-primary" onclick="submitForm('juegos')">Guardar</button>
    </div>
  </div>
</div>

<!-- Modal Apuesta -->
<div class="modal-overlay" id="modal-apuesta">
  <div class="modal">
    <h2 id="title-apuesta">Nueva Apuesta</h2>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Usuario *</label>
        <select class="form-control" id="a-usuario">
          <option value="">Selecciona usuario...</option>
          <option>Usuario123</option><option>Apuestaslover434</option><option>Cute45</option><option>Guitierrez33556</option>
        </select>
        <div class="error-msg" id="err-a-usuario"></div>
      </div>
      <div class="form-group">
        <label class="form-label">Juego *</label>
        <select class="form-control" id="a-juego">
          <option value="">Selecciona juego...</option>
          <option>Ruleta</option><option>Apuestas Deportivas</option><option>Bingo</option><option>Slot Machine</option>
        </select>
        <div class="error-msg" id="err-a-juego"></div>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Monto (€) *</label>
        <input type="number" class="form-control" id="a-monto" placeholder="0.00" min="1" step="0.01">
        <div class="error-msg" id="err-a-monto"></div>
      </div>
      <div class="form-group">
        <label class="form-label">Cuota *</label>
        <input type="number" class="form-control" id="a-cuota" placeholder="1.50" min="1" step="0.01">
        <div class="error-msg" id="err-a-cuota"></div>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Estado</label>
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

<!-- Modal Billetera -->
<div class="modal-overlay" id="modal-billetera">
  <div class="modal">
    <h2 id="title-billetera">Nueva Billetera</h2>
    <div class="form-group">
      <label class="form-label">Usuario *</label>
      <select class="form-control" id="b-usuario">
        <option value="">Selecciona usuario...</option>
        <option>Usuario123</option><option>Apuestaslover434</option><option>Cute45</option>
      </select>
      <div class="error-msg" id="err-b-usuario"></div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Saldo disponible (€) *</label>
        <input type="number" class="form-control" id="b-saldo" placeholder="0.00" min="0" step="0.01">
        <div class="error-msg" id="err-b-saldo"></div>
      </div>
      <div class="form-group">
        <label class="form-label">Moneda</label>
        <select class="form-control" id="b-moneda">
          <option value="EUR">EUR</option>
          <option value="USD">USD</option>
          <option value="GBP">GBP</option>
        </select>
      </div>
    </div>
    <div class="form-actions">
      <button class="btn" onclick="closeModal('modal-billetera')">Cancelar</button>
      <button class="btn btn-primary" onclick="submitForm('billeteras')">Guardar</button>
    </div>
  </div>
</div>

<!-- Modal Notificación -->
<div class="modal-overlay" id="modal-notificacion">
  <div class="modal">
    <h2 id="title-notificacion">Nueva Notificación</h2>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Usuario *</label>
        <select class="form-control" id="n-usuario">
          <option value="">Selecciona usuario...</option>
          <option>Usuario123</option><option>Apuestaslover434</option><option>Cute45</option>
        </select>
        <div class="error-msg" id="err-n-usuario"></div>
      </div>
      <div class="form-group">
        <label class="form-label">Tipo *</label>
        <select class="form-control" id="n-tipo">
          <option value="">Selecciona...</option>
          <option value="apuesta">Apuesta</option>
          <option value="promo">Promo</option>
          <option value="alerta">Alerta</option>
        </select>
        <div class="error-msg" id="err-n-tipo"></div>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Título *</label>
      <input type="text" class="form-control" id="n-titulo" placeholder="Título de la notificación">
      <div class="error-msg" id="err-n-titulo"></div>
    </div>
    <div class="form-group">
      <label class="form-label">Descripción</label>
      <input type="text" class="form-control" id="n-desc" placeholder="Descripción breve">
    </div>
    <div class="form-group">
      <label class="form-label">Leída</label>
      <select class="form-control" id="n-leido">
        <option value="false">No</option>
        <option value="true">Sí</option>
      </select>
    </div>
    <div class="form-actions">
      <button class="btn" onclick="closeModal('modal-notificacion')">Cancelar</button>
      <button class="btn btn-primary" onclick="submitForm('notificaciones')">Guardar</button>
    </div>
  </div>
</div>

<!-- Modal Chat -->
<div class="modal-overlay" id="modal-chat">
  <div class="modal">
    <h2 id="title-chat">Nuevo Chat</h2>
    <div class="form-group">
      <label class="form-label">Nombre *</label>
      <input type="text" class="form-control" id="c-nombre" placeholder="Nombre del chat">
      <div class="error-msg" id="err-c-nombre"></div>
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

<!-- Modal Ranking -->
<div class="modal-overlay" id="modal-ranking">
  <div class="modal">
    <h2 id="title-ranking">Nueva Entrada de Ranking</h2>
    <div class="form-group">
      <label class="form-label">Usuario *</label>
      <select class="form-control" id="r-usuario">
        <option value="">Selecciona usuario...</option>
        <option>Usuario123</option><option>Apuestaslover434</option><option>Cute45</option><option>Guitierrez33556</option>
      </select>
      <div class="error-msg" id="err-r-usuario"></div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Posición *</label>
        <input type="number" class="form-control" id="r-posicion" placeholder="1" min="1">
        <div class="error-msg" id="err-r-posicion"></div>
      </div>
      <div class="form-group">
        <label class="form-label">Puntos</label>
        <input type="number" class="form-control" id="r-puntos" placeholder="0" min="0">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label">Total ganado (€)</label>
      <input type="number" class="form-control" id="r-total" placeholder="0.00" min="0" step="0.01">
    </div>
    <div class="form-group">
      <label class="form-label">Activo</label>
      <select class="form-control" id="r-activo">
        <option value="true">Sí</option>
        <option value="false">No</option>
      </select>
    </div>
    <div class="form-actions">
      <button class="btn" onclick="closeModal('modal-ranking')">Cancelar</button>
      <button class="btn btn-primary" onclick="submitForm('rankings')">Guardar</button>
    </div>
  </div>
</div>

<!-- Confirm delete -->
<div class="modal-overlay" id="modal-confirm">
  <div class="confirm-modal">
    <div class="confirm-icon">🗑️</div>
    <h3>¿Eliminar registro?</h3>
    <p id="confirm-text">Esta acción no se puede deshacer.</p>
    <div style="display:flex;gap:12px;justify-content:center;">
      <button class="btn" onclick="closeModal('modal-confirm')">Cancelar</button>
      <button class="btn btn-danger" id="confirm-ok-btn">Sí, eliminar</button>
    </div>
  </div>
</div>

<div id="toast"></div>

<script>
// ===========================
// DATA
// ===========================
let db = {
  usuarios: [
    {id:1, nombre:'Usuario123', email:'user123@bookie.com', puntosFidelidad:4500, nivelVIP:2, password:'••••••••'},
    {id:2, nombre:'Apuestaslover434', email:'love434@bookie.com', puntosFidelidad:1200, nivelVIP:1, password:'••••••••'},
    {id:3, nombre:'Fg3453', email:'fg3453@bookie.com', puntosFidelidad:350, nivelVIP:0, password:'••••••••'},
    {id:4, nombre:'Cute45', email:'cute45@bookie.com', puntosFidelidad:8900, nivelVIP:3, password:'••••••••'},
    {id:5, nombre:'Guitierrez33556', email:'g33556@bookie.com', puntosFidelidad:670, nivelVIP:1, password:'••••••••'},
    {id:6, nombre:'BettingKing99', email:'bk99@bookie.com', puntosFidelidad:220, nivelVIP:0, password:'••••••••'},
    {id:7, nombre:'LuckyStrike7', email:'ls7@bookie.com', puntosFidelidad:5100, nivelVIP:2, password:'••••••••'},
    {id:8, nombre:'ProGambler88', email:'pg88@bookie.com', puntosFidelidad:3400, nivelVIP:1, password:'••••••••'},
    {id:9, nombre:'HighRoller21', email:'hr21@bookie.com', puntosFidelidad:12000, nivelVIP:3, password:'••••••••'},
    {id:10, nombre:'CasualBet22', email:'cb22@bookie.com', puntosFidelidad:90, nivelVIP:0, password:'••••••••'},
  ],
  juegos: [
    {id:1, nombre:'Ruleta', categoria:'casino', estado:'abierta'},
    {id:2, nombre:'Apuestas Deportivas', categoria:'deportes', estado:'en_juego'},
    {id:3, nombre:'Bingo', categoria:'casino', estado:'abierta'},
    {id:4, nombre:'Slot Machine', categoria:'casino', estado:'abierta'},
    {id:5, nombre:'Blackjack', categoria:'casino', estado:'cerrada'},
    {id:6, nombre:'Poker', categoria:'casino', estado:'cerrada'},
    {id:7, nombre:'Fútbol Virtual', categoria:'virtual', estado:'abierta'},
    {id:8, nombre:'Carreras F1', categoria:'deportes', estado:'abierta'},
  ],
  apuestas: [
    {id:1, usuario:'Usuario123', juego:'Apuestas Deportivas', monto:1250, cuota:1.8, estado:'ganada', fecha:'2026-03-25'},
    {id:2, usuario:'Usuario123', juego:'Slot Machine', monto:800, cuota:2.1, estado:'perdida', fecha:'2026-03-24'},
    {id:3, usuario:'Cute45', juego:'Ruleta', monto:500, cuota:1.5, estado:'pendiente', fecha:'2026-03-28'},
    {id:4, usuario:'Apuestaslover434', juego:'Apuestas Deportivas', monto:300, cuota:2.4, estado:'ganada', fecha:'2026-03-22'},
    {id:5, usuario:'Guitierrez33556', juego:'Bingo', monto:150, cuota:3.0, estado:'perdida', fecha:'2026-03-20'},
    {id:6, usuario:'HighRoller21', juego:'Blackjack', monto:5000, cuota:1.9, estado:'ganada', fecha:'2026-03-18'},
    {id:7, usuario:'BettingKing99', juego:'Ruleta', monto:200, cuota:1.2, estado:'pendiente', fecha:'2026-03-29'},
    {id:8, usuario:'LuckyStrike7', juego:'Poker', monto:750, cuota:2.5, estado:'ganada', fecha:'2026-03-15'},
    {id:9, usuario:'ProGambler88', juego:'Fútbol Virtual', monto:400, cuota:1.7, estado:'perdida', fecha:'2026-03-10'},
    {id:10, usuario:'CasualBet22', juego:'Slot Machine', monto:50, cuota:4.0, estado:'pendiente', fecha:'2026-03-29'},
    {id:11, usuario:'Cute45', juego:'Carreras F1', monto:3000, cuota:2.2, estado:'ganada', fecha:'2026-03-08'},
    {id:12, usuario:'Fg3453', juego:'Ruleta', monto:100, cuota:1.3, estado:'perdida', fecha:'2026-03-05'},
  ],
  billeteras: [
    {id:1, usuario:'Usuario123', saldo:789.03, moneda:'EUR'},
    {id:2, usuario:'Cute45', saldo:12540.50, moneda:'EUR'},
    {id:3, usuario:'Apuestaslover434', saldo:234.10, moneda:'EUR'},
    {id:4, usuario:'HighRoller21', saldo:45200.00, moneda:'USD'},
    {id:5, usuario:'Fg3453', saldo:50.00, moneda:'EUR'},
    {id:6, usuario:'BettingKing99', saldo:320.75, moneda:'GBP'},
    {id:7, usuario:'Guitierrez33556', saldo:890.00, moneda:'EUR'},
    {id:8, usuario:'LuckyStrike7', saldo:6700.20, moneda:'EUR'},
    {id:9, usuario:'ProGambler88', saldo:1100.00, moneda:'USD'},
    {id:10, usuario:'CasualBet22', saldo:15.50, moneda:'EUR'},
  ],
  notificaciones: [
    {id:1, usuario:'Usuario123', tipo:'apuesta', titulo:'¡Ganador!', desc:'Ruleta - Rojos ganadores', leido:false, fechaHora:'2026-03-30 10:14'},
    {id:2, usuario:'Cute45', tipo:'alerta', titulo:'Streaming disponible', desc:'Real Madrid vs. Barcelona', leido:false, fechaHora:'2026-03-30 09:22'},
    {id:3, usuario:'Apuestaslover434', tipo:'promo', titulo:'¡Nuevo bono!', desc:'50% extra en tu próximo ingreso', leido:true, fechaHora:'2026-03-29 18:00'},
    {id:4, usuario:'HighRoller21', tipo:'apuesta', titulo:'Irá mejor a la próxima...', desc:'Slot machine', leido:true, fechaHora:'2026-03-28 15:30'},
    {id:5, usuario:'BettingKing99', tipo:'apuesta', titulo:'¡Bingo!', desc:'Bingo - ¡Felicidades!', leido:false, fechaHora:'2026-03-30 08:45'},
    {id:6, usuario:'Fg3453', tipo:'alerta', titulo:'Mensaje nuevo', desc:'Tienes un mensaje de admin', leido:true, fechaHora:'2026-03-27 12:00'},
    {id:7, usuario:'LuckyStrike7', tipo:'promo', titulo:'Bono VIP activo', desc:'Tu bono VIP 2 está activo', leido:false, fechaHora:'2026-03-26 11:00'},
  ],
  chats: [
    {id:1, nombre:'Chat General', fechaCreacion:'2026-01-10', activo:true},
    {id:2, nombre:'Sala VIP', fechaCreacion:'2026-01-15', activo:true},
    {id:3, nombre:'Soporte Técnico', fechaCreacion:'2026-02-01', activo:true},
    {id:4, nombre:'Torneos', fechaCreacion:'2026-02-20', activo:false},
    {id:5, nombre:'Deportes', fechaCreacion:'2026-03-01', activo:true},
  ],
  mensajes: [
    {id:1, chat:'Chat General', emisor:'Usuario123', receptor:'Apuestaslover434', contenido:'Que sí, que sí', editado:false},
    {id:2, chat:'Chat General', emisor:'Apuestaslover434', receptor:'Usuario123', contenido:'Creo que deberías apostar a los blancos', editado:false},
    {id:3, chat:'Sala VIP', emisor:'Cute45', receptor:'HighRoller21', contenido:'¿Jugamos una mano de poker?', editado:true},
    {id:4, chat:'Soporte Técnico', emisor:'BettingKing99', receptor:'Admin', contenido:'No puedo hacer retiros', editado:false},
    {id:5, chat:'Deportes', emisor:'Guitierrez33556', receptor:'LuckyStrike7', contenido:'¿Apostaste al Madrid?', editado:false},
    {id:6, chat:'Chat General', emisor:'ProGambler88', receptor:'CasualBet22', contenido:'Hola, ¿cómo vas?', editado:true},
  ],
  rankings: [
    {id:1, usuario:'HighRoller21', posicion:1, puntos:12000, totalGanado:45200, activo:true},
    {id:2, usuario:'Cute45', posicion:2, puntos:8900, totalGanado:12540, activo:true},
    {id:3, usuario:'LuckyStrike7', posicion:3, puntos:5100, totalGanado:6700, activo:true},
    {id:4, usuario:'Usuario123', posicion:4, puntos:4500, totalGanado:4200, activo:true},
    {id:5, usuario:'ProGambler88', posicion:5, puntos:3400, totalGanado:1100, activo:true},
    {id:6, usuario:'Apuestaslover434', posicion:6, puntos:1200, totalGanado:234, activo:true},
    {id:7, usuario:'Guitierrez33556', posicion:7, puntos:670, totalGanado:890, activo:false},
    {id:8, usuario:'Fg3453', posicion:8, puntos:350, totalGanado:50, activo:true},
  ],
};

// ===========================
// STATE
// ===========================
let state = {};
['usuarios','juegos','apuestas','billeteras','notificaciones','chats','mensajes','rankings'].forEach(k=>{
  state[k] = {page:1, per:6, sort:'id', dir:1};
});
let editing = {};
let deleteFn = null;

// ===========================
// NAV
// ===========================
function goTo(page) {
  document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
  const el = document.getElementById('page-admin-'+page) || document.getElementById('page-'+page);
  if(el) el.classList.add('active');
  const renders = {
    'admin-usuarios':()=>renderTable('usuarios'),
    'admin-juegos':()=>renderTable('juegos'),
    'admin-apuestas':()=>renderTable('apuestas'),
    'admin-billeteras':()=>renderTable('billeteras'),
    'admin-notificaciones':()=>renderTable('notificaciones'),
    'admin-chats':()=>{renderTable('chats');renderTable('mensajes');},
    'admin-rankings':()=>renderTable('rankings'),
  };
  const key = 'admin-'+page;
  if(renders[key]) renders[key]();
}

// ===========================
// MODAL
// ===========================
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) {
  document.getElementById(id).classList.remove('open');
  clearErrors();
}
function clearErrors() {
  document.querySelectorAll('.error-msg').forEach(e=>e.textContent='');
  document.querySelectorAll('.form-control').forEach(e=>e.classList.remove('error'));
}
function setErr(fId, eId, msg) {
  const f = document.getElementById(fId);
  const e = document.getElementById(eId);
  if(f) f.classList.add('error');
  if(e) e.textContent = msg;
}

// ===========================
// TOAST
// ===========================
function toast(msg, type='success') {
  const t = document.getElementById('toast');
  t.textContent = (type==='success'?'✓ ':type==='danger'?'✗ ':'⚠ ') + msg;
  t.className = 'show ' + type;
  setTimeout(()=>{ t.className=''; }, 2800);
}

// ===========================
// TABLE ENGINE
// ===========================
function getFiltered(key) {
  let arr = [...(db[key]||[])];
  const s = state[key];
  // Search
  const searchEl = document.getElementById('search-'+key);
  const q = (searchEl?.value||'').toLowerCase();
  if(q) arr = arr.filter(r=>Object.values(r).some(v=>String(v).toLowerCase().includes(q)));
  // Extra filters
  if(key==='usuarios'){
    const fv = document.getElementById('filter-vip-u')?.value;
    if(fv!==undefined && fv!=='') arr = arr.filter(r=>String(r.nivelVIP)===fv);
  }
  if(key==='juegos'){
    const fc = document.getElementById('filter-cat-j')?.value;
    const fe = document.getElementById('filter-estado-j')?.value;
    if(fc) arr = arr.filter(r=>r.categoria===fc);
    if(fe) arr = arr.filter(r=>r.estado===fe);
  }
  if(key==='apuestas'){
    const fe = document.getElementById('filter-estado-a')?.value;
    const ff = document.getElementById('filter-fecha-a')?.value;
    if(fe) arr = arr.filter(r=>r.estado===fe);
    if(ff) arr = arr.filter(r=>r.fecha===ff);
  }
  if(key==='billeteras'){
    const fm = document.getElementById('filter-moneda')?.value;
    if(fm) arr = arr.filter(r=>r.moneda===fm);
  }
  if(key==='notificaciones'){
    const ft = document.getElementById('filter-tipo-n')?.value;
    const fl = document.getElementById('filter-leido-n')?.value;
    if(ft) arr = arr.filter(r=>r.tipo===ft);
    if(fl!==undefined && fl!=='') arr = arr.filter(r=>String(r.leido)===fl);
  }
  if(key==='chats'){
    const fa = document.getElementById('filter-activo-c')?.value;
    if(fa!==undefined && fa!=='') arr = arr.filter(r=>String(r.activo)===fa);
  }
  if(key==='mensajes'){
    const fe = document.getElementById('filter-editado-m')?.value;
    if(fe!==undefined && fe!=='') arr = arr.filter(r=>String(r.editado)===fe);
  }
  if(key==='rankings'){
    const fa = document.getElementById('filter-activo-r')?.value;
    if(fa!==undefined && fa!=='') arr = arr.filter(r=>String(r.activo)===fa);
  }
  // Sort
  arr.sort((a,b)=>{
    let av = a[s.sort], bv = b[s.sort];
    if(typeof av==='boolean') return s.dir*(Number(av)-Number(bv));
    if(typeof av==='string') return s.dir*av.localeCompare(String(bv));
    return s.dir*(av-bv);
  });
  return arr;
}

function renderTable(key) {
  const s = state[key];
  const arr = getFiltered(key);
  const total = arr.length;
  const pages = Math.max(1, Math.ceil(total/s.per));
  if(s.page > pages) s.page = pages;
  const slice = arr.slice((s.page-1)*s.per, s.page*s.per);
  const tbody = document.getElementById('tbody-'+key);
  const pagEl = document.getElementById('pag-'+key);
  const countEl = document.getElementById('count-'+key);
  if(countEl) countEl.textContent = total + ' registro' + (total!==1?'s':'');
  if(!tbody) return;
  tbody.innerHTML = rowsFor(key, slice);
  if(pagEl) pagEl.innerHTML = buildPagination(key, s.page, pages, total, slice.length);
}

function rowsFor(key, slice) {
  return slice.map(r=>{
    const actions = `
      <td style="display:flex;gap:5px;">
        <button class="btn btn-sm" onclick="editRecord('${key}',${r.id})">✏️</button>
        <button class="btn btn-sm btn-danger" onclick="confirmDelete('${key}',${r.id})">🗑</button>
      </td>`;
    if(key==='usuarios') return `<tr>
      <td><span style="color:var(--text-muted);font-size:11px;">#${r.id}</span></td>
      <td><strong>${r.nombre}</strong></td>
      <td style="color:var(--text-muted);">${r.email}</td>
      <td>${r.puntosFidelidad.toLocaleString()}</td>
      <td>${badgeVip(r.nivelVIP)}</td>
      ${actions}</tr>`;
    if(key==='juegos') return `<tr>
      <td><span style="color:var(--text-muted);font-size:11px;">#${r.id}</span></td>
      <td><strong>${r.nombre}</strong></td>
      <td><span class="badge badge-info">${r.categoria}</span></td>
      <td>${badgeJuego(r.estado)}</td>
      ${actions}</tr>`;
    if(key==='apuestas') return `<tr>
      <td><span style="color:var(--text-muted);font-size:11px;">#${r.id}</span></td>
      <td>${r.usuario}</td>
      <td>${r.juego}</td>
      <td><strong>€${r.monto.toLocaleString()}</strong></td>
      <td>${r.cuota}x</td>
      <td>${badgeApuesta(r.estado)}</td>
      <td style="color:var(--text-muted);font-size:12px;">${r.fecha}</td>
      ${actions}</tr>`;
    if(key==='billeteras') return `<tr>
      <td><span style="color:var(--text-muted);font-size:11px;">#${r.id}</span></td>
      <td>${r.usuario}</td>
      <td><strong style="color:var(--gold);">${r.moneda} ${r.saldo.toLocaleString('es-ES',{minimumFractionDigits:2})}</strong></td>
      <td><span class="badge badge-info">${r.moneda}</span></td>
      ${actions}</tr>`;
    if(key==='notificaciones') return `<tr>
      <td><span style="color:var(--text-muted);font-size:11px;">#${r.id}</span></td>
      <td>${r.usuario}</td>
      <td>${badgeNotif(r.tipo)}</td>
      <td>${r.titulo}</td>
      <td style="color:var(--text-muted);font-size:12px;">${r.fechaHora}</td>
      <td>${r.leido?'<span class="badge badge-muted">Sí</span>':'<span class="badge badge-warning">No</span>'}</td>
      ${actions}</tr>`;
    if(key==='chats') return `<tr>
      <td><span style="color:var(--text-muted);font-size:11px;">#${r.id}</span></td>
      <td><strong>${r.nombre}</strong></td>
      <td style="color:var(--text-muted);font-size:12px;">${r.fechaCreacion}</td>
      <td>${r.activo?'<span class="badge badge-success">Activo</span>':'<span class="badge badge-danger">Inactivo</span>'}</td>
      ${actions}</tr>`;
    if(key==='mensajes') return `<tr>
      <td><span style="color:var(--text-muted);font-size:11px;">#${r.id}</span></td>
      <td><span class="badge badge-muted">${r.chat}</span></td>
      <td>${r.emisor}</td>
      <td>${r.receptor}</td>
      <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${r.contenido}</td>
      <td>${r.editado?'<span class="badge badge-warning">Sí</span>':'<span class="badge badge-muted">No</span>'}</td>
      ${actions}</tr>`;
    if(key==='rankings') return `<tr>
      <td>${r.posicion===1?'🥇':r.posicion===2?'🥈':r.posicion===3?'🥉':'#'+r.posicion}</td>
      <td><strong>${r.usuario}</strong></td>
      <td style="color:var(--gold);">${r.puntos.toLocaleString()}</td>
      <td>€${r.totalGanado.toLocaleString()}</td>
      <td>${r.activo?'<span class="badge badge-success">Sí</span>':'<span class="badge badge-danger">No</span>'}</td>
      ${actions}</tr>`;
    return '';
  }).join('');
}

function buildPagination(key, cur, pages, total, showing) {
  let html = `<button class="page-btn" onclick="changePage('${key}',${cur-1})" ${cur===1?'disabled':''}>‹</button>`;
  for(let i=1;i<=pages;i++) {
    if(pages>7 && i>2 && i<pages-1 && Math.abs(i-cur)>1) {
      if(i===3||i===pages-2) html+='<span style="padding:0 4px;color:var(--text-muted)">…</span>';
      continue;
    }
    html += `<button class="page-btn ${i===cur?'active':''}" onclick="changePage('${key}',${i})">${i}</button>`;
  }
  html += `<button class="page-btn" onclick="changePage('${key}',${cur+1})" ${cur===pages?'disabled':''}>›</button>`;
  html += `<span class="page-info">Mostrando ${showing} de ${total}</span>`;
  return html;
}

function changePage(key, p) {
  const arr = getFiltered(key);
  const pages = Math.max(1,Math.ceil(arr.length/state[key].per));
  if(p<1||p>pages) return;
  state[key].page = p;
  renderTable(key);
}

function sortRender(key, col, th) {
  const s = state[key];
  if(s.sort===col) s.dir*=-1; else { s.sort=col; s.dir=1; }
  s.page = 1;
  document.querySelectorAll('thead th').forEach(t=>{
    t.classList.remove('sorted');
    const a = t.querySelector('.sort-arrow');
    if(a) a.textContent='⇅';
  });
  if(th) {
    th.classList.add('sorted');
    const a = th.querySelector('.sort-arrow');
    if(a) a.textContent = s.dir===1?'↑':'↓';
  }
  renderTable(key);
}

function filterRender(key) { state[key].page=1; renderTable(key); }

// ===========================
// BADGES
// ===========================
function badgeVip(v) {
  const map = {0:'<span class="badge badge-muted">Sin VIP</span>',1:'<span class="badge badge-warning">VIP 1</span>',2:'<span class="badge badge-info">VIP 2</span>',3:'<span class="badge badge-gold">VIP 3</span>'};
  return map[v]||'<span class="badge badge-muted">?</span>';
}
function badgeApuesta(e) {
  return {ganada:'<span class="badge badge-success">ganada</span>',perdida:'<span class="badge badge-danger">perdida</span>',pendiente:'<span class="badge badge-warning">pendiente</span>'}[e]||'<span class="badge badge-muted">'+e+'</span>';
}
function badgeJuego(e) {
  return {abierta:'<span class="badge badge-success">abierta</span>',cerrada:'<span class="badge badge-danger">cerrada</span>',en_juego:'<span class="badge badge-info">en juego</span>'}[e]||'<span class="badge badge-muted">'+e+'</span>';
}
function badgeNotif(t) {
  return {apuesta:'<span class="badge badge-danger">apuesta</span>',promo:'<span class="badge badge-warning">promo</span>',alerta:'<span class="badge badge-info">alerta</span>'}[t]||'<span class="badge badge-muted">'+t+'</span>';
}

// ===========================
// CRUD
// ===========================
function editRecord(key, id) {
  const r = db[key].find(x=>x.id===id);
  if(!r) return;
  editing[key] = id;
  const modalMap = {usuarios:'modal-usuario',juegos:'modal-juego',apuestas:'modal-apuesta',billeteras:'modal-billetera',notificaciones:'modal-notificacion',chats:'modal-chat',rankings:'modal-ranking'};
  const titleMap = {usuarios:'Editar Usuario',juegos:'Editar Juego',apuestas:'Editar Apuesta',billeteras:'Editar Billetera',notificaciones:'Editar Notificación',chats:'Editar Chat',rankings:'Editar Ranking'};
  const modalId = modalMap[key];
  if(!modalId) return;
  const titleEl = document.getElementById('title-'+key.replace(/s$/,''));
  if(titleEl) titleEl.textContent = titleMap[key]||'Editar';
  // Fill fields
  if(key==='usuarios'){
    document.getElementById('u-nombre').value=r.nombre;
    document.getElementById('u-email').value=r.email;
    document.getElementById('u-puntos').value=r.puntosFidelidad;
    document.getElementById('u-vip').value=r.nivelVIP;
    document.getElementById('u-password').value='';
  }
  if(key==='juegos'){
    document.getElementById('j-nombre').value=r.nombre;
    document.getElementById('j-categoria').value=r.categoria;
    document.getElementById('j-estado').value=r.estado;
  }
  if(key==='apuestas'){
    document.getElementById('a-usuario').value=r.usuario;
    document.getElementById('a-juego').value=r.juego;
    document.getElementById('a-monto').value=r.monto;
    document.getElementById('a-cuota').value=r.cuota;
    document.getElementById('a-estado').value=r.estado;
    document.getElementById('a-fecha').value=r.fecha;
  }
  if(key==='billeteras'){
    document.getElementById('b-usuario').value=r.usuario;
    document.getElementById('b-saldo').value=r.saldo;
    document.getElementById('b-moneda').value=r.moneda;
  }
  if(key==='notificaciones'){
    document.getElementById('n-usuario').value=r.usuario;
    document.getElementById('n-tipo').value=r.tipo;
    document.getElementById('n-titulo').value=r.titulo;
    document.getElementById('n-desc').value=r.desc||'';
    document.getElementById('n-leido').value=String(r.leido);
  }
  if(key==='chats'){
    document.getElementById('c-nombre').value=r.nombre;
    document.getElementById('c-activo').value=String(r.activo);
  }
  if(key==='rankings'){
    document.getElementById('r-usuario').value=r.usuario;
    document.getElementById('r-posicion').value=r.posicion;
    document.getElementById('r-puntos').value=r.puntos;
    document.getElementById('r-total').value=r.totalGanado;
    document.getElementById('r-activo').value=String(r.activo);
  }
  openModal(modalId);
}

function submitForm(key) {
  clearErrors();
  let valid = true;
  let data = {};

  if(key==='usuarios'){
    const nombre=document.getElementById('u-nombre').value.trim();
    const email=document.getElementById('u-email').value.trim();
    const pw=document.getElementById('u-password').value;
    if(!nombre){setErr('u-nombre','err-u-nombre','El nombre es obligatorio');valid=false;}
    if(!email||!/^[^@]+@[^@]+\.[^@]+$/.test(email)){setErr('u-email','err-u-email','Email inválido');valid=false;}
    if(!editing[key]&&!pw){setErr('u-password','err-u-password','La contraseña es obligatoria');valid=false;}
    if(!valid) return;
    data={nombre, email, puntosFidelidad:parseInt(document.getElementById('u-puntos').value)||0, nivelVIP:parseInt(document.getElementById('u-vip').value), password:'••••••••'};
  }
  if(key==='juegos'){
    const nombre=document.getElementById('j-nombre').value.trim();
    const cat=document.getElementById('j-categoria').value;
    if(!nombre){setErr('j-nombre','err-j-nombre','El nombre es obligatorio');valid=false;}
    if(!cat){setErr('j-categoria','err-j-categoria','Selecciona una categoría');valid=false;}
    if(!valid) return;
    data={nombre, categoria:cat, estado:document.getElementById('j-estado').value};
  }
  if(key==='apuestas'){
    const usuario=document.getElementById('a-usuario').value;
    const juego=document.getElementById('a-juego').value;
    const monto=parseFloat(document.getElementById('a-monto').value);
    const cuota=parseFloat(document.getElementById('a-cuota').value);
    const fecha=document.getElementById('a-fecha').value;
    if(!usuario){setErr('a-usuario','err-a-usuario','Selecciona un usuario');valid=false;}
    if(!juego){setErr('a-juego','err-a-juego','Selecciona un juego');valid=false;}
    if(!monto||monto<=0){setErr('a-monto','err-a-monto','Monto debe ser mayor que 0');valid=false;}
    if(!cuota||cuota<1){setErr('a-cuota','err-a-cuota','Cuota mínima: 1.00');valid=false;}
    if(!fecha){setErr('a-fecha','err-a-fecha','Selecciona una fecha');valid=false;}
    if(!valid) return;
    data={usuario, juego, monto, cuota, estado:document.getElementById('a-estado').value, fecha};
  }
  if(key==='billeteras'){
    const usuario=document.getElementById('b-usuario').value;
    const saldo=parseFloat(document.getElementById('b-saldo').value);
    if(!usuario){setErr('b-usuario','err-b-usuario','Selecciona un usuario');valid=false;}
    if(isNaN(saldo)||saldo<0){setErr('b-saldo','err-b-saldo','Saldo inválido');valid=false;}
    if(!valid) return;
    data={usuario, saldo, moneda:document.getElementById('b-moneda').value};
  }
  if(key==='notificaciones'){
    const usuario=document.getElementById('n-usuario').value;
    const tipo=document.getElementById('n-tipo').value;
    const titulo=document.getElementById('n-titulo').value.trim();
    if(!usuario){setErr('n-usuario','err-n-usuario','Selecciona un usuario');valid=false;}
    if(!tipo){setErr('n-tipo','err-n-tipo','Selecciona un tipo');valid=false;}
    if(!titulo){setErr('n-titulo','err-n-titulo','El título es obligatorio');valid=false;}
    if(!valid) return;
    data={usuario, tipo, titulo, desc:document.getElementById('n-desc').value, leido:document.getElementById('n-leido').value==='true', fechaHora:new Date().toISOString().slice(0,16).replace('T',' ')};
  }
  if(key==='chats'){
    const nombre=document.getElementById('c-nombre').value.trim();
    if(!nombre){setErr('c-nombre','err-c-nombre','El nombre es obligatorio');valid=false;}
    if(!valid) return;
    data={nombre, activo:document.getElementById('c-activo').value==='true', fechaCreacion:new Date().toISOString().slice(0,10)};
  }
  if(key==='rankings'){
    const usuario=document.getElementById('r-usuario').value;
    const posicion=parseInt(document.getElementById('r-posicion').value);
    if(!usuario){setErr('r-usuario','err-r-usuario','Selecciona un usuario');valid=false;}
    if(!posicion||posicion<1){setErr('r-posicion','err-r-posicion','Posición inválida');valid=false;}
    if(!valid) return;
    data={usuario, posicion, puntos:parseInt(document.getElementById('r-puntos').value)||0, totalGanado:parseFloat(document.getElementById('r-total').value)||0, activo:document.getElementById('r-activo').value==='true'};
  }

  if(editing[key]) {
    const rec = db[key].find(x=>x.id===editing[key]);
    if(rec) Object.assign(rec, data);
    toast(key.charAt(0).toUpperCase()+key.slice(1,-1)+' actualizado correctamente');
  } else {
    const newId = db[key].length ? Math.max(...db[key].map(x=>x.id))+1 : 1;
    db[key].push({id:newId, ...data});
    toast(key.charAt(0).toUpperCase()+key.slice(1,-1)+' creado correctamente');
  }

  const modalMap={usuarios:'modal-usuario',juegos:'modal-juego',apuestas:'modal-apuesta',billeteras:'modal-billetera',notificaciones:'modal-notificacion',chats:'modal-chat',rankings:'modal-ranking'};
  closeModal(modalMap[key]);
  editing[key] = null;
  renderTable(key);
  if(key==='mensajes') renderTable('chats');
}

function confirmDelete(key, id) {
  document.getElementById('confirm-text').textContent = `¿Eliminar ${key.replace(/s$/,'')} #${id}? Esta acción no se puede deshacer.`;
  deleteFn = ()=>{
    db[key] = db[key].filter(x=>x.id!==id);
    closeModal('modal-confirm');
    toast('Eliminado correctamente', 'danger');
    renderTable(key);
    if(key==='chats'||key==='mensajes'){ renderTable('chats'); renderTable('mensajes'); }
  };
  document.getElementById('confirm-ok-btn').onclick = deleteFn;
  openModal('modal-confirm');
}

// ===========================
// TABS (Chats page)
// ===========================
function switchAdminTab(tab, btn) {
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('tab-chats').style.display = tab==='chats'?'block':'none';
  document.getElementById('tab-mensajes').style.display = tab==='mensajes'?'block':'none';
}

// ===========================
// INIT
// ===========================
['usuarios','juegos','apuestas','billeteras','notificaciones','chats','mensajes','rankings'].forEach(k=>renderTable(k));

// Reset modal titles on open
document.querySelectorAll('.modal-overlay').forEach(m=>{
  m.addEventListener('click', e=>{ if(e.target===m) closeModal(m.id); });
});

// Reset editing state when opening new item modals
function openNewModal(key) {
  editing[key] = null;
  const titleMap={usuarios:'Nuevo Usuario',juegos:'Nuevo Juego',apuestas:'Nueva Apuesta',billeteras:'Nueva Billetera',notificaciones:'Nueva Notificación',chats:'Nuevo Chat',rankings:'Nueva Entrada de Ranking'};
  const titleEl = document.getElementById('title-'+key.replace(/s$/,''));
  if(titleEl) titleEl.textContent = titleMap[key]||'Nuevo';
  // Clear fields
  const modalMap={usuarios:'modal-usuario',juegos:'modal-juego',apuestas:'modal-apuesta',billeteras:'modal-billetera',notificaciones:'modal-notificacion',chats:'modal-chat',rankings:'modal-ranking'};
  const modal = document.getElementById(modalMap[key]);
  if(modal) { modal.querySelectorAll('input,select,textarea').forEach(el=>{ if(el.tagName==='SELECT') el.selectedIndex=0; else el.value=''; }); }
  openModal(modalMap[key]);
}
// Patch buttons that open creation modals
document.querySelectorAll('[onclick^="openModal(\'modal-"]').forEach(btn=>{
  const m = btn.getAttribute('onclick').match(/openModal\('modal-(\w+)'\)/);
  if(m) {
    const modalKey = m[1];
    const keyMap = {'usuario':'usuarios','juego':'juegos','apuesta':'apuestas','billetera':'billeteras','notificacion':'notificaciones','chat':'chats','ranking':'rankings'};
    const key = keyMap[modalKey];
    if(key) btn.setAttribute('onclick', `openNewModal('${key}')`);
  }
});
</script>
</body>
</html>
