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
    $balance = (float) $wallet->saldoDisponible;
@endphp

@extends('layouts.private')

@section('title', 'Ruleta')
@section('topbar_title', 'Ruleta')
@section('active_nav', 'lobby')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Ruleta</h1>
            <p class="page-subtitle">
                Apuesta a rojo o negro. La ruleta tiene 18 casillas rojas, 18 negras y 1 verde.
                Si sale verde, pierdes el 100% de la apuesta.
            </p>
        </div>
        <div class="balance-chip">
            <span>Saldo disponible</span>
            <strong>{{ number_format($balance, 2, ',', '.') }} {{ $wallet->moneda }}</strong>
        </div>
    </div>

    <section class="roulette-layout">
        <article class="panel roulette-wheel-panel">
            <div class="wheel-area">
                <div class="wheel-shell">
                    <div class="pointer" aria-hidden="true"></div>
                    <div
                        id="rouletteWheel"
                        class="roulette-wheel"
                        data-result="{{ $rouletteResult['result_color'] ?? '' }}"
                        aria-label="Ruleta con casillas rojas, negras y una verde"
                        style="--wheel-gradient: {{ $wheelGradient }};"
                    ></div>
                </div>
            </div>
            <div class="legend">
                <span><i class="dot red"></i>18 rojas</span>
                <span><i class="dot black"></i>18 negras</span>
                <span><i class="dot green"></i>1 verde</span>
            </div>
        </article>

        <section class="stack">
            <article class="panel">
                <p class="label">Nueva apuesta</p>
                <h2 class="form-title">Selecciona color y cantidad</h2>
                <p class="muted">Pago 1:1. Si aciertas, ganas la misma cantidad apostada. Si fallas o sale verde, pierdes la apuesta.</p>

                @if ($errors->any())
                    <div class="errors">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="rouletteForm" method="POST" action="{{ route('roulette.play') }}">
                    @csrf

                    <div class="choice-grid" role="radiogroup" aria-label="Color de apuesta">
                        <label class="choice choice-red">
                            <input type="radio" name="selected_color" value="red" @checked(old('selected_color') === 'red') required>
                            <span>Rojo</span>
                        </label>
                        <label class="choice choice-black">
                            <input type="radio" name="selected_color" value="black" @checked(old('selected_color') === 'black') required>
                            <span>Negro</span>
                        </label>
                    </div>

                    <label class="amount-label" for="amount">Cantidad a apostar</label>
                    <input
                        type="number"
                        name="amount"
                        id="amount"
                        min="1"
                        step="0.01"
                        max="{{ max($balance, 1) }}"
                        value="{{ old('amount') }}"
                        placeholder="Ej. 10"
                        required
                    >

                    <div class="quick-buttons" aria-label="Cantidades rapidas">
                        <button type="button" data-add="5">+5</button>
                        <button type="button" data-add="10">+10</button>
                        <button type="button" data-add="25">+25</button>
                        <button type="button" data-all="true">Todo</button>
                    </div>

                    <button id="spinButton" class="spin-button" type="submit">Girar ruleta</button>
                </form>

                @if ($rouletteResult)
                    <div class="result-card {{ $rouletteResult['won'] ? 'win' : 'lose' }}">
                        <h3>{{ $rouletteResult['won'] ? '¡Has ganado!' : 'Has perdido' }}</h3>
                        <p>Apostaste {{ number_format($rouletteResult['amount'], 2, ',', '.') }} {{ $wallet->moneda }} a {{ $colorLabels[$rouletteResult['selected_color']] }}.</p>
                        <p>Resultado: <strong>{{ $colorLabels[$rouletteResult['result_color']] }}</strong>.</p>
                        <p>Nuevo saldo: <strong>{{ number_format($rouletteResult['balance_after'], 2, ',', '.') }} {{ $wallet->moneda }}</strong>.</p>
                    </div>
                @endif
            </article>

            <article class="panel history">
                <p class="label">Historial</p>
                <h2>Ultimas tiradas</h2>

                @if ($lastBets->isEmpty())
                    <p class="empty-state">Todavia no has hecho apuestas en la ruleta.</p>
                @else
                    <div class="history-list">
                        @foreach ($lastBets as $bet)
                            @php
                                $selected = $bet->seleccion ?? null;
                                $result = $bet->resultado ?? null;
                            @endphp
                            <div class="history-item">
                                <div>
                                    <strong>
                                        {{ $colorLabels[$selected] ?? 'Ruleta' }}
                                        @if ($result)
                                            → {{ $colorLabels[$result] ?? ucfirst($result) }}
                                        @endif
                                    </strong>
                                    <small>{{ number_format((float) $bet->monto, 2, ',', '.') }} {{ $wallet->moneda }} · {{ $bet->fecha ? \Illuminate\Support\Carbon::parse($bet->fecha)->format('d/m/Y H:i') : '-' }}</small>
                                </div>
                                <span class="badge {{ $bet->estado }}">{{ ucfirst($bet->estado) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </article>
        </section>
    </section>
@endsection

@push('styles')
    <style>
        .balance-chip {
            min-width: 220px;
            padding: 16px 18px;
            border-radius: 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            text-align: right;
            box-shadow: var(--shadow);
        }

        .balance-chip span {
            display: block;
            color: var(--muted);
            font-size: 13px;
            margin-bottom: 6px;
        }

        .balance-chip strong {
            font-size: 24px;
            color: var(--gold);
        }

        .roulette-layout {
            display: grid;
            grid-template-columns: minmax(300px, 1.05fr) minmax(300px, .95fr);
            gap: 22px;
            align-items: start;
        }

        .roulette-wheel-panel {
            min-height: 520px;
        }

        .wheel-area {
            min-height: 440px;
            display: grid;
            place-items: center;
            position: relative;
            overflow: hidden;
        }

        .wheel-shell {
            position: relative;
            width: min(390px, 76vw);
            height: min(390px, 76vw);
            display: grid;
            place-items: center;
        }

        .pointer {
            position: absolute;
            top: -2px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 4;
            width: 0;
            height: 0;
            border-left: 18px solid transparent;
            border-right: 18px solid transparent;
            border-top: 34px solid var(--gold);
            filter: drop-shadow(0 8px 8px rgba(0, 0, 0, .35));
        }

        .roulette-wheel {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: conic-gradient(var(--wheel-gradient));
            border: 12px solid rgba(255, 247, 242, .88);
            box-shadow: inset 0 0 0 10px rgba(0, 0, 0, .30), 0 28px 70px rgba(0, 0, 0, .36);
            transition: transform 4s cubic-bezier(.12, .72, .16, 1);
            position: relative;
        }

        .roulette-wheel::before {
            content: '';
            position: absolute;
            inset: 50%;
            width: 106px;
            height: 106px;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            background: radial-gradient(circle, var(--gold), #b98716);
            border: 8px solid rgba(255, 247, 242, .9);
            box-shadow: 0 10px 25px rgba(0, 0, 0, .28);
        }

        .roulette-wheel::after {
            content: 'B';
            position: absolute;
            inset: 50%;
            width: 68px;
            height: 68px;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            display: grid;
            place-items: center;
            color: #351010;
            font-weight: 1000;
            font-size: 34px;
        }

        .legend {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .legend span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            font-size: 14px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            display: inline-block;
        }

        .dot.red { background: #dc2626; }
        .dot.black { background: #111827; border: 1px solid rgba(255,255,255,.55); }
        .dot.green { background: #16a34a; }

        .form-title {
            margin: 0 0 8px;
            font-size: 26px;
        }

        .choice-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin: 18px 0;
        }

        .choice input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .choice span {
            min-height: 92px;
            border-radius: 20px;
            border: 2px solid rgba(255, 255, 255, .16);
            display: grid;
            place-items: center;
            font-size: 22px;
            font-weight: 900;
            cursor: pointer;
            transition: transform .15s, border-color .15s, box-shadow .15s;
        }

        .choice span:hover { transform: translateY(-2px); }
        .choice-red span { background: linear-gradient(145deg, #dc2626, #7f1d1d); }
        .choice-black span { background: linear-gradient(145deg, #1f2937, #030712); }

        .choice input:checked + span {
            border-color: var(--gold);
            box-shadow: 0 0 0 4px rgba(240, 192, 64, .22);
        }

        .amount-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 800;
        }

        input[type="number"] {
            width: 100%;
            padding: 14px 15px;
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 14px;
            background: rgba(255, 255, 255, .10);
            color: var(--text);
            font: inherit;
            font-size: 18px;
        }

        input[type="number"]:focus {
            outline: 3px solid rgba(240, 192, 64, .30);
        }

        .quick-buttons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin: 14px 0 18px;
        }

        .quick-buttons button {
            border: 1px solid rgba(255, 255, 255, .14);
            background: rgba(255, 255, 255, .10);
            color: var(--text);
            border-radius: 999px;
            padding: 10px;
            cursor: pointer;
            font-weight: 800;
        }

        .spin-button {
            width: 100%;
            min-height: 52px;
            border: none;
            border-radius: 999px;
            background: var(--gold);
            color: #351010;
            font-weight: 1000;
            font-size: 17px;
            cursor: pointer;
            box-shadow: 0 12px 26px rgba(0, 0, 0, .18);
        }

        .spin-button:disabled {
            opacity: .68;
            cursor: wait;
        }

        .errors {
            background: rgba(239, 154, 154, .16);
            color: #fecaca;
            border: 1px solid rgba(239, 154, 154, .32);
            border-radius: 14px;
            padding: 12px 14px;
            margin: 16px 0;
        }

        .errors ul {
            margin: 0;
            padding-left: 18px;
        }

        .result-card {
            margin-top: 18px;
            border-radius: 20px;
            padding: 18px;
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .16);
        }

        .result-card.win { border-color: rgba(34, 197, 94, .50); }
        .result-card.lose { border-color: rgba(248, 113, 113, .55); }
        .result-card h3 { margin: 0 0 8px; font-size: 22px; }
        .result-card p { margin: 6px 0; color: var(--muted); }

        .history h2 {
            margin: 0 0 14px;
            font-size: 24px;
        }

        .history-list {
            display: grid;
            gap: 10px;
        }

        .history-item {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            padding: 12px;
            border-radius: 16px;
            background: var(--surface-strong);
            border: 1px solid var(--border);
        }

        .history-item strong {
            display: block;
            margin-bottom: 3px;
        }

        .history-item small {
            color: var(--muted);
        }

        @media (max-width: 1050px) {
            .roulette-layout { grid-template-columns: 1fr; }
            .page-header { flex-direction: column; }
            .balance-chip { width: 100%; text-align: left; }
        }

        @media (max-width: 520px) {
            .choice-grid { grid-template-columns: 1fr; }
            .quick-buttons { grid-template-columns: repeat(2, 1fr); }
            .wheel-area { min-height: 340px; }
        }
    </style>
@endpush

@push('scripts')
    <script>
        const balance = @json($balance);
        const amountInput = document.getElementById('amount');
        const form = document.getElementById('rouletteForm');
        const spinButton = document.getElementById('spinButton');
        const wheel = document.getElementById('rouletteWheel');

        document.querySelectorAll('[data-add]').forEach((button) => {
            button.addEventListener('click', () => {
                const current = Number.parseFloat(amountInput.value || '0');
                const add = Number.parseFloat(button.dataset.add || '0');
                const next = Math.min(balance, current + add);
                amountInput.value = next > 0 ? next.toFixed(2) : '';
            });
        });

        document.querySelectorAll('[data-all]').forEach((button) => {
            button.addEventListener('click', () => {
                amountInput.value = balance > 0 ? balance.toFixed(2) : '';
            });
        });

        form.addEventListener('submit', () => {
            spinButton.disabled = true;
            spinButton.textContent = 'Girando...';
        });

        function animateResult(resultColor) {
            if (!resultColor || !wheel) return;

            const slotDegrees = 360 / 37;
            const redSlots = Array.from({ length: 18 }, (_, index) => index + 1);
            const blackSlots = Array.from({ length: 18 }, (_, index) => index + 19);
            const slotsByColor = {
                green: [0],
                red: redSlots,
                black: blackSlots,
            };

            const slots = slotsByColor[resultColor] || [];
            const targetSlot = slots[Math.floor(Math.random() * slots.length)];
            const targetCenter = (targetSlot * slotDegrees) + (slotDegrees / 2);
            const finalRotation = (360 * 6) - targetCenter;

            requestAnimationFrame(() => {
                wheel.style.transform = `rotate(${finalRotation}deg)`;
            });
        }

        animateResult(wheel?.dataset.result);
    </script>
@endpush
