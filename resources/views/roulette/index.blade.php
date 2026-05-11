@php
    $colorLabels = [
        'red' => 'Rojo',
        'black' => 'Negro',
        'green' => 'Verde',
    ];

    $wheelSegments = [];
    $slotDegrees = 360 / 37;

    for ($i = 0; $i < 37; $i++) {
        $start = round($i * $slotDegrees, 4);
        $end = round(($i + 1) * $slotDegrees, 4);
        $color = '#16a34a';

        if ($i > 0 && $i <= 18) {
            $color = '#dc2626';
        } elseif ($i > 18) {
            $color = '#111827';
        }

        $wheelSegments[] = "{$color} {$start}deg {$end}deg";
    }

    $wheelGradient = implode(', ', $wheelSegments);
    $rouletteResult = session('roulette_result');
    $displayBalance = $rouletteResult ? ($rouletteResult['balance_before'] ?? $wallet->saldoDisponible) : $wallet->saldoDisponible;
@endphp

@extends('layouts.private')

@section('title', 'Ruleta')
@section('topbar_title', 'Ruleta')
@section('active_nav', 'games')

@section('content')
    <style>
        .roulette-layout { display: grid; grid-template-columns: minmax(300px, 1.05fr) minmax(300px, .95fr); gap: 22px; align-items: start; }
        .wheel-area { min-height: 450px; display: grid; place-items: center; position: relative; overflow: hidden; }
        .wheel-shell { position: relative; width: min(390px, 76vw); height: min(390px, 76vw); display: grid; place-items: center; }
        .pointer { position: absolute; top: -2px; left: 50%; transform: translateX(-50%); z-index: 4; width: 0; height: 0; border-left: 18px solid transparent; border-right: 18px solid transparent; border-top: 34px solid var(--gold); filter: drop-shadow(0 8px 8px rgba(0, 0, 0, .35)); }
        .roulette-wheel { width: 100%; height: 100%; border-radius: 50%; background: conic-gradient({{ $wheelGradient }}); border: 12px solid rgba(255, 247, 242, .88); box-shadow: inset 0 0 0 10px rgba(0, 0, 0, .30), 0 28px 70px rgba(0, 0, 0, .36); transition: transform 4s cubic-bezier(.12, .72, .16, 1); position: relative; transform: rotate(0deg); }
        .roulette-wheel::before { content: ''; position: absolute; inset: 50%; width: 106px; height: 106px; transform: translate(-50%, -50%); border-radius: 50%; background: radial-gradient(circle, var(--gold), #b98716); border: 8px solid rgba(255, 247, 242, .9); box-shadow: 0 10px 25px rgba(0, 0, 0, .28); }
        .roulette-wheel::after { content: 'B'; position: absolute; inset: 50%; width: 68px; height: 68px; transform: translate(-50%, -50%); border-radius: 50%; display: grid; place-items: center; color: #351010; font-weight: 1000; font-size: 34px; }
        .spin-message { position:absolute; bottom:20px; left:50%; transform:translateX(-50%); padding:10px 16px; border-radius:999px; background:rgba(0,0,0,.45); border:1px solid rgba(255,255,255,.16); color:#fff; font-weight:800; opacity:0; transition:opacity .2s ease; }
        .spin-message.visible { opacity:1; }
        .choice-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 18px; }
        .choice input { position: absolute; opacity: 0; pointer-events: none; }
        .choice span { min-height: 92px; border-radius: 20px; border: 2px solid rgba(255, 255, 255, .16); display: grid; place-items: center; font-size: 22px; font-weight: 900; cursor: pointer; transition: transform .15s, border-color .15s, box-shadow .15s; }
        .choice span:hover { transform: translateY(-2px); }
        .choice-red span { background: linear-gradient(145deg, #dc2626, #7f1d1d); }
        .choice-black span { background: linear-gradient(145deg, #1f2937, #030712); }
        .choice input:checked + span { border-color: var(--gold); box-shadow: 0 0 0 4px rgba(243, 198, 75, .22); }
        .quick-buttons { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin: 10px 0 16px; }
        .quick-btn { border: 1px solid var(--border); background: rgba(255,255,255,.08); color: var(--text); border-radius: 999px; padding: 10px; cursor: pointer; font-weight: 700; }
        .legend { display: flex; justify-content: center; flex-wrap: wrap; gap: 10px; margin-top: 18px; }
        .legend span { display: inline-flex; align-items: center; gap: 8px; color: var(--muted); font-size: 14px; }
        .dot { width: 12px; height: 12px; border-radius: 999px; display: inline-block; }
        .dot.red { background: #dc2626; } .dot.black { background: #111827; border: 1px solid rgba(255,255,255,.55); } .dot.green { background: #16a34a; }
        .reveal-after-spin { transition: opacity .25s ease, transform .25s ease; }
        .reveal-after-spin.waiting { opacity: 0; transform: translateY(8px); pointer-events: none; }
        @media (max-width: 1000px) { .roulette-layout { grid-template-columns: 1fr; } }
    </style>

    <div class="page-header">
        <div>
            <h1 class="page-title">Ruleta</h1>
            <p class="page-subtitle">Apuesta a rojo o negro. Si sale verde, pierdes la apuesta completa.</p>
        </div>
        <div class="panel" style="text-align:right; min-width:220px;">
            <p class="label">Saldo disponible</p>
            <div class="stat-value" id="rouletteBalance" data-final-balance="{{ number_format((float) $wallet->saldoDisponible, 2, '.', '') }}">
                {{ number_format((float) $displayBalance, 2, ',', '.') }} EUR
            </div>
        </div>
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

        @if ($rouletteResult)
            <div class="alert {{ $rouletteResult['won'] ? 'success' : 'error' }} reveal-after-spin waiting" id="rouletteResultAlert">
                <strong>{{ $rouletteResult['won'] ? 'Has ganado' : 'Has perdido' }}</strong> —
                Apostaste {{ number_format((float) $rouletteResult['amount'], 2, ',', '.') }} EUR a
                {{ $colorLabels[$rouletteResult['selected_color']] ?? $rouletteResult['selected_color'] }} y salió
                {{ $colorLabels[$rouletteResult['result_color']] ?? $rouletteResult['result_color'] }}.
                Nuevo saldo: {{ number_format((float) $rouletteResult['balance_after'], 2, ',', '.') }} EUR.
            </div>
        @endif

        <section class="roulette-layout">
            <article class="panel">
                <div class="wheel-area">
                    <div class="wheel-shell">
                        <div class="pointer"></div>
                        <div class="roulette-wheel" id="rouletteWheel"></div>
                    </div>
                    <div class="spin-message {{ $rouletteResult ? 'visible' : '' }}" id="spinMessage">Girando...</div>
                </div>
                <div class="legend">
                    <span><i class="dot red"></i> 18 rojas</span>
                    <span><i class="dot black"></i> 18 negras</span>
                    <span><i class="dot green"></i> 1 verde</span>
                </div>
            </article>

            <article class="panel">
                <p class="label">Nueva apuesta</p>
                <form method="POST" action="{{ route('roulette.play') }}" id="rouletteForm" class="form-grid">
                    @csrf

                    <div class="choice-grid">
                        <label class="choice choice-red">
                            <input type="radio" name="selected_color" value="red" @checked(old('selected_color') === 'red')>
                            <span>Rojo</span>
                        </label>
                        <label class="choice choice-black">
                            <input type="radio" name="selected_color" value="black" @checked(old('selected_color') === 'black')>
                            <span>Negro</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="amount">Cantidad</label>
                        <input class="form-control" id="amount" name="amount" type="number" min="1" step="0.01" value="{{ old('amount', 10) }}">
                    </div>

                    <div class="quick-buttons">
                        <button class="quick-btn" type="button" data-add="5">+5</button>
                        <button class="quick-btn" type="button" data-add="10">+10</button>
                        <button class="quick-btn" type="button" data-add="25">+25</button>
                        <button class="quick-btn" type="button" data-add="50">+50</button>
                    </div>

                    <button class="btn" type="submit" id="spinButton">Girar</button>
                </form>
            </article>
        </section>

        <section class="panel reveal-after-spin {{ $rouletteResult ? 'waiting' : '' }}" id="rouletteHistory">
            <p class="label">Últimas tiradas</p>
            @if ($lastBets->isEmpty())
                <p class="empty-state">Todavía no has jugado a la ruleta.</p>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Elección</th>
                                <th>Resultado</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Saldo después</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lastBets as $bet)
                                <tr>
                                    <td>{{ $colorLabels[$bet->seleccion] ?? $bet->seleccion }}</td>
                                    <td>{{ $colorLabels[$bet->resultado] ?? $bet->resultado }}</td>
                                    <td>{{ number_format((float) $bet->monto, 2, ',', '.') }} EUR</td>
                                    <td><span class="badge {{ $bet->estado }}">{{ $bet->estadoEtiqueta() }}</span></td>
                                    <td>{{ $bet->balance_despues !== null ? number_format((float) $bet->balance_despues, 2, ',', '.') . ' EUR' : '-' }}</td>
                                    <td>{{ $bet->fecha ? $bet->fecha->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>

    <script>
        const amount = document.getElementById('amount');
        document.querySelectorAll('[data-add]').forEach((button) => {
            button.addEventListener('click', () => {
                amount.value = ((parseFloat(amount.value || '0') || 0) + parseFloat(button.dataset.add)).toFixed(2);
            });
        });

        document.getElementById('rouletteForm').addEventListener('submit', () => {
            const button = document.getElementById('spinButton');
            const message = document.getElementById('spinMessage');
            button.disabled = true;
            button.textContent = 'Calculando...';
            message.classList.add('visible');
            message.textContent = 'Preparando giro...';
        });

        const serverResult = @json($rouletteResult);
        const slotDegrees = 360 / 37;

        function randomSlotForColor(color) {
            if (color === 'green') return 0;
            if (color === 'red') return 1 + Math.floor(Math.random() * 18);
            return 19 + Math.floor(Math.random() * 18);
        }

        function revealSpinResult() {
            const alert = document.getElementById('rouletteResultAlert');
            const history = document.getElementById('rouletteHistory');
            const message = document.getElementById('spinMessage');
            const balance = document.getElementById('rouletteBalance');

            if (alert) alert.classList.remove('waiting');
            if (history) history.classList.remove('waiting');
            if (message) {
                message.textContent = serverResult && serverResult.won ? '¡Ganaste!' : 'Resultado final';
                setTimeout(() => message.classList.remove('visible'), 1000);
            }
            if (balance && balance.dataset.finalBalance) {
                const finalValue = Number(balance.dataset.finalBalance || 0);
                balance.textContent = finalValue.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' EUR';
            }
        }

        if (serverResult && serverResult.result_color) {
            const wheel = document.getElementById('rouletteWheel');
            const message = document.getElementById('spinMessage');
            const slot = randomSlotForColor(serverResult.result_color);
            const slotCenter = (slot * slotDegrees) + (slotDegrees / 2);
            const randomFineTune = (Math.random() - 0.5) * (slotDegrees * 0.55);
            const finalRotation = (360 * 6) - slotCenter + randomFineTune;

            if (message) {
                message.classList.add('visible');
                message.textContent = 'Girando...';
            }

            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    wheel.style.transform = `rotate(${finalRotation}deg)`;
                    window.setTimeout(revealSpinResult, 4300);
                });
            });
        }
    </script>
@endsection
