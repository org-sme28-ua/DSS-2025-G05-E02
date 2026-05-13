@extends('layouts.private')

@section('title', 'Dados')
@section('topbar_title', 'Dados')
@section('active_nav', 'games')

@section('content')
    @php($result = session('dice_result'))

    <style>
        .dice-layout { display: grid; grid-template-columns: minmax(280px, .95fr) minmax(300px, 1.05fr); gap: 18px; align-items: start; }
        .dice-stage { display: grid; place-items: center; min-height: 340px; }
        .dice-cube {
            width: 170px; height: 170px; border-radius: 30px; display: grid; place-items: center;
            background: linear-gradient(135deg, #fff7f2, #e8c9c1); color: #4b1717; font-size: 72px; font-weight: 900;
            box-shadow: 0 20px 60px rgba(0,0,0,.34), inset -10px -10px 22px rgba(0,0,0,.12);
            animation: dicePop .65s ease-out;
        }
        @keyframes dicePop { from { transform: rotate(-18deg) scale(.75); opacity:.3; } 70% { transform: rotate(8deg) scale(1.08); } to { transform: rotate(0deg) scale(1); opacity:1; } }
        .choice-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .choice-radio input { position: absolute; opacity: 0; pointer-events: none; }
        .choice-radio span { display: flex; min-height: 96px; align-items: center; justify-content: center; text-align:center; border: 1px solid var(--border); border-radius: 16px; background: var(--surface-strong); font-size: 20px; font-weight: 900; cursor: pointer; padding: 10px; }
        .choice-radio input:checked + span { outline: 3px solid rgba(240,192,64,.35); border-color: rgba(240,192,64,.65); color: var(--gold); }
        .result-banner { display: grid; gap: 6px; border-radius: 16px; padding: 16px; background: rgba(255,255,255,.08); border: 1px solid var(--border); }
    </style>

    <div class="page-header">
        <div>
            <h1 class="page-title">Dados</h1>
            <p class="page-subtitle">Apuesta si el dado caerá bajo o alto. Bajo es 1-3, alto es 4-6. Pago simple 1:1.</p>
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

        <section class="dice-layout">
            <article class="panel panel-highlight dice-stage">
                <p class="label">Dado</p>
                <div class="dice-cube">{{ $result['roll'] ?? '?' }}</div>
                @if ($result)
                    <div class="result-banner" style="margin-top: 20px; width:100%;">
                        <strong>{{ $result['won'] ? 'Has ganado' : 'Has perdido' }}</strong>
                        <span class="muted">Elegiste {{ $result['seleccion'] === 'bajo' ? 'Bajo (1-3)' : 'Alto (4-6)' }} y salió {{ $result['roll'] }}.</span>
                        <span>Nuevo saldo: <strong>{{ number_format((float) $result['balance_after'], 2, ',', '.') }} EUR</strong></span>
                    </div>
                @else
                    <p class="muted" style="text-align:center; margin-top:18px;">Tira el dado para ver el resultado.</p>
                @endif
            </article>

            <article class="panel">
                <p class="label">Saldo disponible</p>
                <p class="balance">{{ number_format((float) $wallet->saldoDisponible, 2, ',', '.') }} <span class="currency">{{ $wallet->moneda }}</span></p>

                <form class="form-grid" method="POST" action="{{ route('dice.play') }}" style="margin-top:22px;">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Elige tu apuesta</label>
                        <div class="choice-grid">
                            <label class="choice-radio">
                                <input type="radio" name="seleccion" value="bajo" @checked(old('seleccion') === 'bajo')>
                                <span>Bajo<br>1-3</span>
                            </label>
                            <label class="choice-radio">
                                <input type="radio" name="seleccion" value="alto" @checked(old('seleccion') === 'alto')>
                                <span>Alto<br>4-6</span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="amount">Cantidad</label>
                        <input class="form-control" id="amount" name="amount" type="number" step="0.01" min="1" max="{{ (float) $wallet->saldoDisponible }}" value="{{ old('amount', 10) }}" required>
                    </div>
                    <button class="btn" type="submit">Tirar dado</button>
                </form>
            </article>
        </section>

        <section class="panel">
            <p class="label">Últimas jugadas</p>
            @if ($lastBets->isEmpty())
                <p class="empty-state">Todavía no has jugado a dados.</p>
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
