@extends('layouts.private')

@section('title', 'Rankings')
@section('active_nav', 'rankings')
@section('topbar_title', 'Rankings')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">🏆 Rankings</h1>
        <p class="page-subtitle">Clasificación global de jugadores por puntos acumulados.</p>
    </div>
</div>

{{-- ── PODIO TOP 3 ── --}}
@if($top3->count() > 0)
<div style="display:flex; align-items:flex-end; justify-content:center; gap:16px; margin-bottom:28px; flex-wrap:wrap;">

    {{-- 2º lugar --}}
    @if($top3->count() >= 2)
    <div class="panel" style="text-align:center; min-width:150px; padding:20px 24px; order:1;">
        <div style="font-size:36px; margin-bottom:8px;">🥈</div>
        <div style="font-weight:700; color:#fff; font-size:15px;">{{ $top3[1]->user->name ?? '—' }}</div>
        <div style="font-size:22px; font-weight:800; color:var(--gold); margin-top:4px;">{{ number_format($top3[1]->puntos) }}</div>
        <div style="font-size:11px; color:var(--muted); margin-top:2px;">pts · €{{ number_format($top3[1]->total_ganado, 0) }} ganados</div>
    </div>
    @endif

    {{-- 1º lugar --}}
    <div class="panel panel-highlight" style="text-align:center; min-width:170px; padding:28px 28px; order:2; border-color:var(--gold);">
        <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--gold);margin-bottom:6px;">LÍDER</div>
        <div style="font-size:42px; margin-bottom:8px;">🥇</div>
        <div style="font-weight:700; color:#fff; font-size:16px;">{{ $top3[0]->user->name ?? '—' }}</div>
        <div style="font-size:26px; font-weight:800; color:var(--gold); margin-top:4px;">{{ number_format($top3[0]->puntos) }}</div>
        <div style="font-size:11px; color:var(--muted); margin-top:2px;">pts · €{{ number_format($top3[0]->total_ganado, 0) }} ganados</div>
    </div>

    {{-- 3º lugar --}}
    @if($top3->count() >= 3)
    <div class="panel" style="text-align:center; min-width:150px; padding:20px 24px; order:3;">
        <div style="font-size:36px; margin-bottom:8px;">🥉</div>
        <div style="font-weight:700; color:#fff; font-size:15px;">{{ $top3[2]->user->name ?? '—' }}</div>
        <div style="font-size:22px; font-weight:800; color:var(--gold); margin-top:4px;">{{ number_format($top3[2]->puntos) }}</div>
        <div style="font-size:11px; color:var(--muted); margin-top:2px;">pts · €{{ number_format($top3[2]->total_ganado, 0) }} ganados</div>
    </div>
    @endif

</div>
@endif

{{-- ── BÚSQUEDA Y ORDENACIÓN ── --}}
<form method="GET" action="{{ route('private.rankings') }}" style="margin-bottom:0;">
    <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;
                background:var(--surface); border:1px solid var(--border);
                border-radius:16px 16px 0 0; padding:14px 18px;">

        <input type="text" name="search"
               value="{{ request('search') }}"
               placeholder="🔍  Buscar jugador..."
               class="form-control"
               style="max-width:220px; padding:9px 13px; border-radius:999px; margin:0;">

        <select name="sort" class="form-control" style="max-width:180px; border-radius:999px; margin:0;"
                onchange="this.form.submit()">
            <option value="posicion"     {{ request('sort','posicion')==='posicion'     ? 'selected':'' }}>Por posición</option>
            <option value="puntos"       {{ request('sort')==='puntos'                  ? 'selected':'' }}>Por puntos</option>
            <option value="total_ganado" {{ request('sort')==='total_ganado'            ? 'selected':'' }}>Por total ganado</option>
        </select>

        <select name="dir" class="form-control" style="max-width:140px; border-radius:999px; margin:0;"
                onchange="this.form.submit()">
            <option value="asc"  {{ request('dir','asc')==='asc'  ? 'selected':'' }}>↑ Ascendente</option>
            <option value="desc" {{ request('dir')==='desc'       ? 'selected':'' }}>↓ Descendente</option>
        </select>

        <button type="submit" class="btn" style="padding:9px 20px; border-radius:999px;">Buscar</button>
        @if(request('search'))
            <a href="{{ route('private.rankings') }}" class="btn secondary" style="padding:9px 20px; border-radius:999px;">Limpiar</a>
        @endif
    </div>
</form>

{{-- ── TABLA ── --}}
<div class="panel" style="border-radius:0 0 16px 16px; padding:0; border-top:none;">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    @php
                        $s = request('sort', 'posicion');
                        $d = request('dir', 'asc');
                        $cols = ['posicion' => 'Posición', 'puntos' => 'Puntos', 'total_ganado' => 'Total ganado'];
                    @endphp
                    @foreach($cols as $col => $lbl)
                    @php
                        $newDir = ($s === $col && $d === 'asc') ? 'desc' : 'asc';
                        $arrow  = $s === $col ? ($d === 'asc' ? ' ↑' : ' ↓') : '';
                        $qs = array_merge(request()->except(['sort','dir','page']), ['sort'=>$col,'dir'=>$newDir]);
                    @endphp
                    <th>
                        <a href="{{ route('private.rankings') }}?{{ http_build_query($qs) }}"
                           style="color:inherit; text-decoration:none;">
                            {{ $lbl }}{{ $arrow }}
                        </a>
                    </th>
                    @endforeach
                    <th>Jugador</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rankings as $r)
                <tr>
                    {{-- Posición --}}
                    <td>
                        @if($r->posicion === 1)
                            <span style="font-size:20px;">🥇</span>
                            <strong style="color:var(--gold);"> 1º</strong>
                        @elseif($r->posicion === 2)
                            <span style="font-size:20px;">🥈</span>
                            <strong style="color:#b0bec5;"> 2º</strong>
                        @elseif($r->posicion === 3)
                            <span style="font-size:20px;">🥉</span>
                            <strong style="color:#cd7f32;"> 3º</strong>
                        @else
                            <strong style="color:var(--muted);">#{{ $r->posicion }}</strong>
                        @endif
                    </td>

                    {{-- Puntos --}}
                    <td>
                        <span style="font-size:17px; font-weight:800; color:var(--gold);">
                            {{ number_format($r->puntos) }}
                        </span>
                        <span style="color:var(--muted); font-size:12px;"> pts</span>
                    </td>

                    {{-- Total ganado --}}
                    <td>
                        <strong style="color:var(--success);">
                            {{ number_format($r->total_ganado, 2, ',', '.') }} EUR
                        </strong>
                    </td>

                    {{-- Jugador --}}
                    <td>
                        <div style="display:flex; align-items:center; gap:10px;">
                            <div style="width:34px;height:34px;border-radius:50%;
                                        background:var(--gold);color:#4b1717;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:13px;font-weight:800;flex-shrink:0;">
                                {{ strtoupper(substr($r->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:600;color:#fff;">{{ $r->user->name ?? '—' }}</div>
                                {{-- Destacar al usuario actual --}}
                                @if($r->user_id === auth()->id())
                                    <span class="badge info" style="font-size:10px;padding:2px 8px;">Tú</span>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <p class="empty-state" style="text-align:center;">
                            No hay jugadores en el ranking aún.
                        </p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── PAGINACIÓN ── --}}
    @if($rankings->hasPages())
    <div style="padding:14px 18px; border-top:1px solid var(--border);
                display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
        <span style="font-size:12px; color:var(--muted);">
            Mostrando <strong style="color:#fff;">{{ $rankings->firstItem() }}</strong>–<strong style="color:#fff;">{{ $rankings->lastItem() }}</strong>
            de <strong style="color:#fff;">{{ $rankings->total() }}</strong> jugadores
        </span>
        {{ $rankings->appends(request()->except('page'))->links() }}
    </div>
    @else
    <div style="padding:12px 18px; border-top:1px solid var(--border); font-size:12px; color:var(--muted);">
        Total: <strong style="color:#fff;">{{ $rankings->total() }}</strong> jugadores
    </div>
    @endif
</div>

{{-- ── HISTÓRICO TOP 5 SEMANAL ── --}}
@php
    $semanas = \App\Models\RankingSemanal::with('user')
        ->orderByDesc('anio')
        ->orderByDesc('semana')
        ->orderBy('posicion')
        ->get()
        ->groupBy(fn ($r) => $r->anio . '-' . str_pad($r->semana, 2, '0', STR_PAD_LEFT));
@endphp

@if ($semanas->isNotEmpty())
<div style="margin-top:32px;">

    <div style="margin-bottom:16px;">
        <h2 style="margin:0; font-family:'Playfair Display',serif; font-size:26px; color:#fff;">
            📅 Top 5 por semana
        </h2>
        <p style="margin:6px 0 0; color:var(--muted); font-size:13px;">
            Histórico de los mejores jugadores de las últimas {{ $semanas->count() }} semanas.
        </p>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:16px;">
        @foreach ($semanas as $key => $filas)
        @php $primera = $filas->first(); @endphp

        <div class="panel" style="padding:0; overflow:hidden;">

            {{-- Cabecera de semana --}}
            <div style="padding:14px 18px; background:var(--surface-strong); border-bottom:1px solid var(--border);">
                <div style="font-size:11px; font-weight:700; text-transform:uppercase;
                            letter-spacing:.1em; color:var(--gold);">
                    Semana {{ $primera->semana }} · {{ $primera->anio }}
                </div>
                <div style="font-size:12px; color:var(--muted); margin-top:3px;">
                    {{ $primera->fecha_inicio->format('d/m') }}
                    –
                    {{ $primera->fecha_fin->format('d/m/Y') }}
                </div>
            </div>

            {{-- Filas Top 5 --}}
            <div style="padding:8px 0;">
                @foreach ($filas as $f)
                <div style="display:flex; align-items:center; gap:12px;
                            padding:10px 18px; border-bottom:1px solid rgba(255,255,255,.05);">

                    {{-- Medalla / número --}}
                    <div style="width:28px; text-align:center; flex-shrink:0; font-size:18px; line-height:1;">
                        @if ($f->posicion === 1) 🥇
                        @elseif ($f->posicion === 2) 🥈
                        @elseif ($f->posicion === 3) 🥉
                        @else <strong style="color:var(--muted); font-size:13px;">#{{ $f->posicion }}</strong>
                        @endif
                    </div>

                    {{-- Avatar --}}
                    <div style="width:30px; height:30px; border-radius:50%; background:var(--gold);
                                color:#4b1717; display:flex; align-items:center; justify-content:center;
                                font-size:12px; font-weight:800; flex-shrink:0;">
                        {{ strtoupper(substr($f->user->name ?? 'U', 0, 1)) }}
                    </div>

                    {{-- Nombre + datos --}}
                    <div style="flex:1; min-width:0;">
                        <div style="font-size:13px; font-weight:700; color:#fff;
                                    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $f->user->name ?? '—' }}
                            @if ($f->user_id === auth()->id())
                                <span class="badge info" style="font-size:10px; padding:1px 6px; margin-left:4px;">Tú</span>
                            @endif
                        </div>
                        <div style="font-size:11px; color:var(--muted); margin-top:1px;">
                            {{ number_format($f->puntos) }} pts
                            · {{ number_format($f->total_ganado, 0) }} EUR
                        </div>
                    </div>

                </div>
                @endforeach
            </div>

        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
