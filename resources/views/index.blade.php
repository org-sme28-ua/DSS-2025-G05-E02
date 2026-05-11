@extends('layouts.app')

@section('title', 'Rankings')

@section('content')

{{-- ══════════════════════════════════════════
     ESTILOS PROPIOS DE RANKINGS
     ══════════════════════════════════════════ --}}
<style>
    :root {
        --bk-bg:      #1a0505;
        --bk-card:    #2a0a0a;
        --bk-card2:   #3d1212;
        --bk-accent:  #c0392b;
        --bk-gold:    #f0c040;
        --bk-text:    #f5e6e6;
        --bk-muted:   #c4a0a0;
        --bk-border:  rgba(255,255,255,0.10);
        --bk-success: #2ecc71;
        --bk-danger:  #e74c3c;
        --bk-warning: #f39c12;
        --bk-info:    #3498db;
    }

    /* ── Contenedor principal ── */
    .rk-wrap { color: var(--bk-text); font-family: 'DM Sans', 'Segoe UI', sans-serif; }

    /* ── Cabecera de página ── */
    .rk-header {
        display: flex; align-items: flex-start; justify-content: space-between;
        margin-bottom: 28px; flex-wrap: wrap; gap: 14px;
    }
    .rk-title {
        font-size: 30px; font-weight: 700; color: #fff; letter-spacing: -.5px;
        display: flex; align-items: center; gap: 10px;
    }
    .rk-subtitle { font-size: 13px; color: var(--bk-muted); margin-top: 4px; }

    /* ── Tarjetas de estadísticas ── */
    .rk-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px,1fr)); gap: 14px; margin-bottom: 26px; }
    .rk-stat {
        background: var(--bk-card); border: 1px solid var(--bk-border);
        border-radius: 12px; padding: 18px 20px;
    }
    .rk-stat-label { font-size: 11px; text-transform: uppercase; letter-spacing: .06em; color: var(--bk-muted); margin-bottom: 6px; }
    .rk-stat-value { font-size: 26px; font-weight: 700; color: #fff; }
    .rk-stat-value.gold  { color: var(--bk-gold); }
    .rk-stat-value.green { color: var(--bk-success); }

    /* ── Podio top-3 ── */
    .rk-podium {
        display: flex; align-items: flex-end; justify-content: center;
        gap: 16px; margin-bottom: 30px; flex-wrap: wrap;
    }
    .rk-podium-card {
        background: var(--bk-card); border: 1px solid var(--bk-border);
        border-radius: 16px; padding: 22px 28px; text-align: center;
        position: relative; min-width: 160px; transition: transform .2s;
    }
    .rk-podium-card:hover { transform: translateY(-4px); }
    .rk-podium-card.first {
        background: linear-gradient(160deg, #3d1212, #5a2010);
        border-color: var(--bk-gold); order: 2;
        padding-bottom: 28px; padding-top: 28px;
    }
    .rk-podium-card.second { order: 1; }
    .rk-podium-card.third  { order: 3; }
    .rk-medal { font-size: 32px; display: block; margin-bottom: 8px; }
    .rk-pos-badge {
        position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
        background: var(--bk-gold); color: #1a0505;
        font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 50px;
    }
    .rk-podium-name { font-size: 15px; font-weight: 600; color: #fff; margin-bottom: 4px; }
    .rk-podium-pts  { font-size: 22px; font-weight: 700; color: var(--bk-gold); }
    .rk-podium-sub  { font-size: 11px; color: var(--bk-muted); margin-top: 2px; }

    /* ── Filtros y búsqueda ── */
    .rk-toolbar {
        display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
        background: var(--bk-card); border: 1px solid var(--bk-border);
        border-radius: 12px 12px 0 0; padding: 14px 18px;
        border-bottom: 1px solid var(--bk-border);
    }
    .rk-search {
        background: rgba(255,255,255,.07); border: 1px solid var(--bk-border);
        border-radius: 8px; padding: 8px 13px; color: var(--bk-text);
        font-size: 13.5px; outline: none; min-width: 200px;
        transition: border-color .15s;
    }
    .rk-search:focus { border-color: rgba(255,255,255,.3); }
    .rk-search::placeholder { color: var(--bk-muted); }
    .rk-select {
        background: rgba(255,255,255,.07); border: 1px solid var(--bk-border);
        border-radius: 8px; padding: 8px 12px; color: var(--bk-text);
        font-size: 13px; outline: none; cursor: pointer;
    }
    .rk-select option { background: #2a0a0a; }
    .rk-btn {
        padding: 8px 18px; border-radius: 8px; font-size: 13px; font-weight: 500;
        border: 1px solid var(--bk-border); cursor: pointer;
        background: rgba(255,255,255,.08); color: var(--bk-text);
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        transition: background .15s;
    }
    .rk-btn:hover { background: rgba(255,255,255,.16); color: #fff; }
    .rk-btn-primary { background: var(--bk-accent); border-color: var(--bk-accent); color: #fff; }
    .rk-btn-primary:hover { background: #a93226; color: #fff; }
    .rk-btn-success { background: rgba(46,204,113,.15); border-color: rgba(46,204,113,.4); color: var(--bk-success); }
    .rk-btn-danger  { background: rgba(231,76,60,.15);  border-color: rgba(231,76,60,.4);  color: var(--bk-danger); }
    .rk-btn-sm { padding: 5px 11px; font-size: 12px; }

    /* ── Tabla ── */
    .rk-table-wrap {
        background: var(--bk-card); border: 1px solid var(--bk-border);
        border-radius: 0 0 12px 12px; overflow: hidden;
    }
    .rk-table { width: 100%; border-collapse: collapse; }
    .rk-table thead th {
        padding: 11px 16px; text-align: left; font-size: 11px;
        font-weight: 600; text-transform: uppercase; letter-spacing: .06em;
        color: var(--bk-muted); background: rgba(0,0,0,.25);
        border-bottom: 1px solid var(--bk-border); white-space: nowrap;
    }
    .rk-table thead th a {
        color: inherit; text-decoration: none;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .rk-table thead th a:hover { color: var(--bk-text); }
    .rk-table thead th.sort-asc  a::after { content: ' ↑'; color: var(--bk-gold); }
    .rk-table thead th.sort-desc a::after { content: ' ↓'; color: var(--bk-gold); }
    .rk-table tbody tr { border-bottom: 1px solid rgba(255,255,255,.05); transition: background .1s; }
    .rk-table tbody tr:last-child { border-bottom: none; }
    .rk-table tbody tr:hover { background: rgba(255,255,255,.03); }
    .rk-table tbody td { padding: 13px 16px; font-size: 13.5px; }

    /* ── Posición con medalla ── */
    .rk-pos { display: flex; align-items: center; gap: 8px; font-weight: 600; }
    .rk-pos-num { font-size: 13px; color: var(--bk-muted); min-width: 20px; }
    .rk-pos-num.top1 { color: var(--bk-gold); font-size: 16px; }
    .rk-pos-num.top2 { color: #b0bec5; font-size: 15px; }
    .rk-pos-num.top3 { color: #cd7f32; font-size: 15px; }

    /* ── Badges ── */
    .badge-rk {
        display: inline-block; padding: 3px 10px; border-radius: 50px;
        font-size: 11px; font-weight: 600;
    }
    .badge-gold    { background: rgba(240,192,64,.2);  color: var(--bk-gold); }
    .badge-silver  { background: rgba(176,190,197,.2); color: #b0bec5; }
    .badge-bronze  { background: rgba(205,127,50,.2);  color: #cd7f32; }
    .badge-default { background: rgba(255,255,255,.08); color: var(--bk-muted); }

    /* ── Paginación ── */
    .rk-pagination { padding: 14px 18px; border-top: 1px solid var(--bk-border); }
    .rk-pagination .pagination { gap: 4px; margin: 0; }
    .rk-pagination .page-link {
        background: rgba(255,255,255,.06); border-color: var(--bk-border);
        color: var(--bk-muted); border-radius: 8px !important; font-size: 13px;
    }
    .rk-pagination .page-link:hover { background: rgba(255,255,255,.14); color: #fff; }
    .rk-pagination .page-item.active .page-link { background: var(--bk-accent); border-color: var(--bk-accent); color: #fff; }
    .rk-pagination .page-item.disabled .page-link { opacity: .35; }

    /* ── Modal ── */
    .rk-modal .modal-content {
        background: var(--bk-card); border: 1px solid var(--bk-border);
        border-radius: 14px; color: var(--bk-text);
    }
    .rk-modal .modal-header { border-bottom: 1px solid var(--bk-border); padding: 18px 24px; }
    .rk-modal .modal-title  { font-size: 18px; font-weight: 700; color: #fff; }
    .rk-modal .btn-close     { filter: invert(1) opacity(.5); }
    .rk-modal .modal-body    { padding: 24px; }
    .rk-modal .modal-footer  { border-top: 1px solid var(--bk-border); padding: 14px 24px; }
    .rk-modal .form-label    { font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--bk-muted); margin-bottom: 6px; }
    .rk-modal .form-control, .rk-modal .form-select {
        background: rgba(255,255,255,.07); border: 1px solid var(--bk-border);
        border-radius: 8px; color: var(--bk-text); font-size: 14px; padding: 9px 13px;
    }
    .rk-modal .form-control:focus, .rk-modal .form-select:focus {
        background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.3);
        box-shadow: none; color: var(--bk-text);
    }
    .rk-modal .form-control.is-invalid { border-color: var(--bk-danger); }
    .rk-modal .invalid-feedback { font-size: 11.5px; color: var(--bk-danger); }
    .rk-modal select.form-select option { background: #2a0a0a; }
    .rk-modal .form-text { font-size: 12px; color: var(--bk-muted); }

    /* ── Empty state ── */
    .rk-empty { text-align: center; padding: 56px 24px; color: var(--bk-muted); }
    .rk-empty-icon { font-size: 44px; margin-bottom: 14px; }
    .rk-empty p { font-size: 14px; }

    /* ── Toast ── */
    #rk-toast { position: fixed; bottom: 24px; right: 24px; z-index: 9999; min-width: 240px; }
    .toast-body { display: flex; align-items: center; gap: 8px; font-size: 13.5px; }
</style>

<div class="rk-wrap">

    {{-- ── CABECERA ── --}}
    <div class="rk-header">
        <div>
            <div class="rk-title">🏆 Rankings</div>
            <div class="rk-subtitle">Clasificación global de jugadores por puntos acumulados</div>
        </div>
        <button class="rk-btn rk-btn-primary" data-bs-toggle="modal" data-bs-target="#modalCrear">
            + Añadir entrada
        </button>
    </div>

    {{-- ── ESTADÍSTICAS ── --}}
    <div class="rk-stats">
        <div class="rk-stat">
            <div class="rk-stat-label">Total jugadores</div>
            <div class="rk-stat-value">{{ $rankings->total() }}</div>
        </div>
        <div class="rk-stat">
            <div class="rk-stat-label">Máx. puntos</div>
            <div class="rk-stat-value gold">{{ number_format($rankings->first()?->puntos ?? 0) }}</div>
        </div>
        <div class="rk-stat">
            <div class="rk-stat-label">Mayor ganancia</div>
            <div class="rk-stat-value green">
                €{{ number_format($rankings->sortByDesc('total_ganado')->first()?->total_ganado ?? 0, 2) }}
            </div>
        </div>
        <div class="rk-stat">
            <div class="rk-stat-label">Promedio puntos</div>
            <div class="rk-stat-value">
                {{ $rankings->total() > 0 ? number_format($rankings->avg('puntos'), 0) : 0 }}
            </div>
        </div>
    </div>

    {{-- ── PODIO TOP 3 ── --}}
    @php
        $top3 = $allRankings->sortBy('posicion')->take(3)->values();
    @endphp
    @if($top3->count() >= 1)
    <div class="rk-podium mb-4">
        {{-- 2º lugar --}}
        @if($top3->count() >= 2)
        <div class="rk-podium-card second">
            <span class="rk-medal">🥈</span>
            <div class="rk-podium-name">{{ $top3[1]->user->name ?? 'Usuario #'.$top3[1]->user_id }}</div>
            <div class="rk-podium-pts">{{ number_format($top3[1]->puntos) }}</div>
            <div class="rk-podium-sub">pts · €{{ number_format($top3[1]->total_ganado, 0) }} ganados</div>
        </div>
        @endif

        {{-- 1º lugar --}}
        <div class="rk-podium-card first">
            <span class="rk-pos-badge">LÍDER</span>
            <span class="rk-medal">🥇</span>
            <div class="rk-podium-name">{{ $top3[0]->user->name ?? 'Usuario #'.$top3[0]->user_id }}</div>
            <div class="rk-podium-pts">{{ number_format($top3[0]->puntos) }}</div>
            <div class="rk-podium-sub">pts · €{{ number_format($top3[0]->total_ganado, 0) }} ganados</div>
        </div>

        {{-- 3º lugar --}}
        @if($top3->count() >= 3)
        <div class="rk-podium-card third">
            <span class="rk-medal">🥉</span>
            <div class="rk-podium-name">{{ $top3[2]->user->name ?? 'Usuario #'.$top3[2]->user_id }}</div>
            <div class="rk-podium-pts">{{ number_format($top3[2]->puntos) }}</div>
            <div class="rk-podium-sub">pts · €{{ number_format($top3[2]->total_ganado, 0) }} ganados</div>
        </div>
        @endif
    </div>
    @endif

    {{-- ── TOOLBAR (búsqueda + filtros + ordenación) ── --}}
    <form method="GET" action="{{ route('rankings.index') }}" id="form-filtros">
        <div class="rk-toolbar">
            <input  type="text"   name="search"
                    class="rk-search"
                    placeholder="🔍  Buscar usuario..."
                    value="{{ request('search') }}">

            <select name="per" class="rk-select" onchange="document.getElementById('form-filtros').submit()">
                <option value="10"  {{ request('per','10')=='10'  ? 'selected':'' }}>10 por página</option>
                <option value="25"  {{ request('per')=='25'       ? 'selected':'' }}>25 por página</option>
                <option value="50"  {{ request('per')=='50'       ? 'selected':'' }}>50 por página</option>
            </select>

            {{-- Mantener sort y dir al filtrar --}}
            <input type="hidden" name="sort" value="{{ request('sort','posicion') }}">
            <input type="hidden" name="dir"  value="{{ request('dir','asc') }}">

            <div class="ms-auto d-flex gap-2">
                <button type="submit" class="rk-btn rk-btn-primary">Buscar</button>
                <a href="{{ route('rankings.index') }}" class="rk-btn">Limpiar</a>
            </div>
        </div>
    </form>

    {{-- ── TABLA ── --}}
    <div class="rk-table-wrap">
        <div class="table-responsive">
            <table class="rk-table">
                <thead>
                    <tr>
                        @php
                            $sort = request('sort', 'posicion');
                            $dir  = request('dir', 'asc');
                            $cols = [
                                'posicion'     => 'Posición',
                                'user_id'      => 'Jugador',
                                'puntos'       => 'Puntos',
                                'total_ganado' => 'Total ganado',
                            ];
                        @endphp
                        @foreach($cols as $col => $label)
                        @php
                            $newDir   = ($sort === $col && $dir === 'asc') ? 'desc' : 'asc';
                            $thClass  = $sort === $col ? ($dir === 'asc' ? 'sort-asc' : 'sort-desc') : '';
                            $qs = array_merge(request()->except(['sort','dir','page']), ['sort'=>$col,'dir'=>$newDir]);
                        @endphp
                        <th class="{{ $thClass }}">
                            <a href="{{ route('rankings.index') }}?{{ http_build_query($qs) }}">
                                {{ $label }}
                            </a>
                        </th>
                        @endforeach
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rankings as $r)
                    <tr>
                        {{-- Posición --}}
                        <td>
                            <div class="rk-pos">
                                @if($r->posicion === 1)
                                    <span class="rk-pos-num top1">🥇</span>
                                    <span class="badge-rk badge-gold">1º</span>
                                @elseif($r->posicion === 2)
                                    <span class="rk-pos-num top2">🥈</span>
                                    <span class="badge-rk badge-silver">2º</span>
                                @elseif($r->posicion === 3)
                                    <span class="rk-pos-num top3">🥉</span>
                                    <span class="badge-rk badge-bronze">3º</span>
                                @else
                                    <span class="rk-pos-num">#{{ $r->posicion }}</span>
                                    <span class="badge-rk badge-default">{{ $r->posicion }}º</span>
                                @endif
                            </div>
                        </td>

                        {{-- Jugador --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:34px;height:34px;border-radius:50%;background:var(--bk-accent);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;">
                                    {{ strtoupper(substr($r->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;color:#fff;">{{ $r->user->name ?? '—' }}</div>
                                    <div style="font-size:11px;color:var(--bk-muted);">ID #{{ $r->user_id }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Puntos --}}
                        <td>
                            <span style="font-size:16px;font-weight:700;color:var(--bk-gold);">
                                {{ number_format($r->puntos) }}
                            </span>
                            <span style="font-size:11px;color:var(--bk-muted);margin-left:3px;">pts</span>
                        </td>

                        {{-- Total ganado --}}
                        <td>
                            <span style="font-weight:600;color:var(--bk-success);">
                                €{{ number_format($r->total_ganado, 2) }}
                            </span>
                        </td>

                        {{-- Acciones --}}
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button"
                                        class="rk-btn rk-btn-sm"
                                        onclick="abrirEditar({{ $r->id }}, {{ $r->user_id }}, {{ $r->posicion }}, {{ $r->puntos }}, {{ $r->total_ganado }})">
                                    ✏️ Editar
                                </button>
                                <form action="{{ route('rankings.destroy', $r->id) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar ranking del usuario {{ $r->user->name ?? '#'.$r->user_id }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rk-btn rk-btn-sm rk-btn-danger">
                                        🗑 Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="rk-empty">
                                <div class="rk-empty-icon">🏆</div>
                                <p>No se encontraron entradas en el ranking.</p>
                                @if(request('search'))
                                    <a href="{{ route('rankings.index') }}" class="rk-btn mt-3 d-inline-flex">Ver todos</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ── PAGINACIÓN ── --}}
        @if($rankings->hasPages())
        <div class="rk-pagination d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div style="font-size:12px;color:var(--bk-muted);">
                Mostrando <strong style="color:#fff;">{{ $rankings->firstItem() }}</strong>–<strong style="color:#fff;">{{ $rankings->lastItem() }}</strong>
                de <strong style="color:#fff;">{{ $rankings->total() }}</strong> jugadores
            </div>
            {{ $rankings->appends(request()->except('page'))->links() }}
        </div>
        @else
        <div class="rk-pagination" style="font-size:12px;color:var(--bk-muted);">
            Total: <strong style="color:#fff;">{{ $rankings->total() }}</strong> jugadores
        </div>
        @endif
    </div>
</div>


{{-- ════════════════════════════════════════
     MODAL: CREAR RANKING
     ════════════════════════════════════════ --}}
<div class="modal fade rk-modal" id="modalCrear" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">+ Nueva entrada de ranking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('rankings.store') }}" method="POST" id="form-crear">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Usuario *</label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">Selecciona un usuario</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected':'' }}>
                                        {{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Posición *</label>
                            <input type="number" name="posicion"
                                   class="form-control @error('posicion') is-invalid @enderror"
                                   value="{{ old('posicion', 1) }}" min="1" required
                                   placeholder="Ej: 1">
                            @error('posicion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Puntos *</label>
                            <input type="number" name="puntos"
                                   class="form-control @error('puntos') is-invalid @enderror"
                                   value="{{ old('puntos', 0) }}" min="0" required
                                   placeholder="Ej: 1500">
                            @error('puntos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Total ganado (€) *</label>
                            <input type="number" name="total_ganado"
                                   class="form-control @error('total_ganado') is-invalid @enderror"
                                   value="{{ old('total_ganado', 0) }}" min="0" step="0.01" required
                                   placeholder="Ej: 250.00">
                            <div class="form-text">Suma total de ganancias en euros.</div>
                            @error('total_ganado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="rk-btn" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="rk-btn rk-btn-primary">Guardar ranking</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════
     MODAL: EDITAR RANKING
     ════════════════════════════════════════ --}}
<div class="modal fade rk-modal" id="modalEditar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">✏️ Editar ranking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-editar" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Usuario</label>
                            <select name="user_id" id="edit-user_id" class="form-select" required>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Posición *</label>
                            <input type="number" name="posicion" id="edit-posicion"
                                   class="form-control" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Puntos *</label>
                            <input type="number" name="puntos" id="edit-puntos"
                                   class="form-control" min="0" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Total ganado (€) *</label>
                            <input type="number" name="total_ganado" id="edit-total_ganado"
                                   class="form-control" min="0" step="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="rk-btn" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="rk-btn rk-btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════
     TOAST DE ÉXITO / ERROR
     ════════════════════════════════════════ --}}
<div id="rk-toast" class="toast-container position-fixed bottom-0 end-0 p-3">
    @if(session('success'))
    <div class="toast align-items-center text-bg-success border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body">✓ {{ session('success') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="toast align-items-center text-bg-danger border-0 show" role="alert">
        <div class="d-flex">
            <div class="toast-body">✗ {{ session('error') }}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    @endif
</div>


@push('scripts')
<script>
/**
 * Abre el modal de edición y rellena los campos con los datos del ranking.
 */
function abrirEditar(id, userId, posicion, puntos, totalGanado) {
    // Construir la URL de update (PUT) dinámica
    const base = "{{ url('rankings') }}";
    document.getElementById('form-editar').action = base + '/' + id;

    // Rellenar campos
    document.getElementById('edit-user_id').value      = userId;
    document.getElementById('edit-posicion').value     = posicion;
    document.getElementById('edit-puntos').value       = puntos;
    document.getElementById('edit-total_ganado').value = totalGanado;

    // Abrir modal
    const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
    modal.show();
}

// Si hay errores de validación al crear, reabrir modal automáticamente
@if($errors->any() && old('_token'))
document.addEventListener('DOMContentLoaded', function () {
    const modal = new bootstrap.Modal(document.getElementById('modalCrear'));
    modal.show();
});
@endif

// Auto-ocultar toasts
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toast').forEach(function(el) {
        setTimeout(function() {
            const t = bootstrap.Toast.getOrCreateInstance(el);
            t.hide();
        }, 3500);
    });
});
</script>
@endpush

@endsection
