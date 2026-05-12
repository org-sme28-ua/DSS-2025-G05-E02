@extends('layouts.private')

@section('title', 'Cara o Cruz')
@section('topbar_title', 'Cara o Cruz')
@section('active_nav', 'games')

@section('content')
    @php($result = session('coin_result'))

    <style>
        .coin-layout { display: grid; grid-template-columns: minmax(280px, .95fr) minmax(300px, 1.05fr); gap: 18px; align-items: start; }
        .coin-stage { display: grid; place-items: center; min-height: 340px; }
        .coin-disc {
            width: 220px; height: 220px; border-radius: 50%; display: grid; place-items: center;
            background: radial-gradient(circle at 35% 28%, rgba(255,255,255,.28), transparent 28%), linear-gradient(135deg, #f0c040, #a66a12);
            color: #3b130e; font-size: 54px; font-weight: 900; border: 12px solid rgba(255,255,255,.16);
            box-shadow: 0 20px 60px rgba(0,0,0,.32), inset 0 0 30px rgba(255,255,255,.22);
            animation: coinFlip .9s ease-out;
        }
        @keyframes coinFlip { from { transform: rotateY(0deg) scale(.86); } 50% { transform: rotateY(540deg) scale(1.08); } to { transform: rotateY(1080deg) scale(1); } }
        .choice-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .choice-radio input { position: absolute; opacity: 0; pointer-events: none; }
        .choice-radio span { display: flex; min-height: 96px; align-items: center; justify-content: center; border: 1px solid var(--border); border-radius: 16px; background: var(--surface-strong); font-size: 22px; font-weight: 900; cursor: pointer; }
        .choice-radio input:checked + span { outline: 3px solid rgba(240,192,64,.35); border-color: rgba(240,192,64,.65); color: var(--gold); }
        .result-banner { display: grid; gap: 6px; border-radius: 16px; padding: 16px; background: rgba(255,255,255,.08); border: 1px solid var(--border); }
    </style>

    <div class="page-header">
        <div>
            <h1 class="page-title">Cara o Cruz</h1>
            <p class="page-subtitle">Elige una cara de la moneda. Si aciertas, ganas la misma cantidad apostada. Si fallas, pierdes la apuesta.</p>
        </div>
        <a class="btn secondary" href="{{ route('private.games') }}">← Volver a juegos</a>
    </div>

    <div class="stack">
        @if ($errors->any())
            <div class="alert error">
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="coin-layout">
            <article class="panel panel-highlight coin-stage">
                <p class="label">Moneda</p>
                <div class="coin-disc">{{ $result ? ($result['resultado'] === 'cara' ? 'C' : 'X') : '?' }}</div>
                @if ($result)
                    <div class="result-banner" style="margin-top: 20px; width:100%;">
                        <strong>{{ $result['won'] ? 'Has ganado' : 'Has perdido' }}</strong>
                        <span class="muted">Elegiste {{ ucfirst($result['seleccion']) }} y salió {{ ucfirst($result['resultado']) }}.</span>
                        <span>Nuevo saldo: <strong>{{ number_format((float) $result['balance_after'], 2, ',', '.') }} EUR</strong></span>
                    </div>
                @else
                    <p class="muted" style="text-align:center; margin-top:18px;">Lanza la moneda para ver el resultado.</p>
                @endif
            </article>

            <article class="panel">
                <p class="label">Saldo disponible</p>
                <p class="balance">{{ number_format((float) $wallet->saldoDisponible, 2, ',', '.') }} <span class="currency">{{ $wallet->moneda }}</span></p>

                <form class="form-grid" method="POST" action="{{ route('coin.play') }}" style="margin-top:22px;">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Elige tu apuesta</label>
                        <div class="choice-grid">
                            <label class="choice-radio">
                                <input type="radio" name="seleccion" value="cara" @checked(old('seleccion') === 'cara')>
                                <span>🙂 Cara</span>
                            </label>
                            <label class="choice-radio">
                                <input type="radio" name="seleccion" value="cruz" @checked(old('seleccion') === 'cruz')>
                                <span>✖️ Cruz</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="amount">Cantidad</label>
                        <input class="form-control" id="amount" name="amount" type="number" step="0.01" min="1" max="{{ (float) $wallet->saldoDisponible }}" value="{{ old('amount', 10) }}" required>
                    </div>
                    <button class="btn" type="submit">Lanzar moneda</button>
                </form>
            </article>
        </section>

        <section class="panel">
            <p class="label">Últimas jugadas</p>
            @if ($lastBets->isEmpty())
                <p class="empty-state">Todavía no has jugado a cara o cruz.</p>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Selección</th><th>Resultado</th><th>Monto</th><th>Estado</th><th>Fecha</th></tr></thead>
                        <tbody>
                            @foreach ($lastBets as $bet)
                                <tr>
                                    <td>{{ $bet->seleccion }}</td>
                                    <td>{{ $bet->resultado }}</td>
                                    <td>{{ number_format((float) $bet->monto, 2, ',', '.') }} EUR</td>
                                    <td><span class="badge {{ $bet->estado }}">{{ $bet->estadoEtiqueta() }}</span></td>
                                    <td>{{ $bet->fecha?->format('d/m/Y H:i') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
