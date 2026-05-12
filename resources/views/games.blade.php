@extends('layouts.private')

@section('title', 'Juegos')
@section('topbar_title', 'Juegos')
@section('active_nav', 'games')

@section('content')
    <style>
        .game-image-card {
            padding: 0;
            overflow: hidden;
            min-height: 360px;
            position: relative;
            isolation: isolate;
        }

        .game-image-frame {
            margin: 14px;
            height: 170px;
            border-radius: 16px;
            overflow: hidden;
            border: 2px solid rgba(240, 192, 64, .45);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,.12), 0 18px 34px rgba(0,0,0,.25);
            background: rgba(255,255,255,.05);
        }

        .game-image-frame img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            transition: transform .45s ease, filter .45s ease;
        }

        .game-image-card:hover .game-image-frame img {
            transform: scale(1.07);
            filter: saturate(1.12) contrast(1.06);
        }

        .game-card-body {
            padding: 0 18px 18px;
            display: grid;
            grid-template-rows: 56px minmax(72px, 1fr) auto;
            gap: 10px;
            min-height: 170px;
        }

        .game-card-title {
            min-height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .game-card-title strong {
            font-size: 24px;
            color: #fff;
            line-height: 1.08;
        }

        .game-card-title span {
            font-size: 34px;
            filter: drop-shadow(0 6px 12px rgba(0,0,0,.24));
        }

        .game-card-cta {
            margin-top: auto;
            color: var(--gold);
            font-weight: 900;
            font-size: 13px;
            letter-spacing: .03em;
        }
    </style>

    <div class="page-header">
        <div>
            <h1 class="page-title">Juegos</h1>
            <p class="page-subtitle">
                Elige un juego para apostar con tu saldo. Las imágenes están enmarcadas para mantener el estilo casino de Bookie 2.0.
            </p>
        </div>
    </div>

    <div class="stack">
        <section class="game-grid">
            <a class="panel game-card game-image-card" href="{{ route('roulette.index') }}">
                <div class="game-image-frame">
                    <img src="{{ asset('assets/casino/game-roulette.jpg') }}" alt="Mesa de ruleta de casino">
                </div>
                <div class="game-card-body">
                    <div class="game-card-title"><strong>Ruleta</strong><span>🎯</span></div>
                    <p class="muted">Rojo, negro y verde. La animación muestra el resultado al finalizar.</p>
                    <span class="game-card-cta">Jugar ahora →</span>
                </div>
            </a>

            <a class="panel game-card game-image-card" href="{{ route('prediction.index') }}">
                <div class="game-image-frame">
                    <img src="{{ asset('assets/casino/game-prediction.jpg') }}" alt="Predicciones y apuestas">
                </div>
                <div class="game-card-body">
                    <div class="game-card-title"><strong>Predicción</strong><span>🔮</span></div>
                    <p class="muted">Crea una predicción y espera revisión del administrador.</p>
                    <span class="game-card-cta">Crear predicción →</span>
                </div>
            </a>

            <a class="panel game-card game-image-card" href="{{ route('dice.index') }}">
                <div class="game-image-frame">
                    <img src="{{ asset('assets/casino/game-dice.jpg') }}" alt="Dados de casino">
                </div>
                <div class="game-card-body">
                    <div class="game-card-title"><strong>Dados</strong><span>🎲</span></div>
                    <p class="muted">Apuesta a bajo o alto con un dado de seis caras.</p>
                    <span class="game-card-cta">Tirar dado →</span>
                </div>
            </a>

            <a class="panel game-card game-image-card" href="{{ route('coin.index') }}">
                <div class="game-image-frame">
                    <img src="{{ asset('assets/casino/game-coinflip.jpg') }}" alt="Moneda sobre mesa de casino">
                </div>
                <div class="game-card-body">
                    <div class="game-card-title"><strong>Cara o Cruz</strong><span>🪙</span></div>
                    <p class="muted">Elige cara o cruz y lanza la moneda.</p>
                    <span class="game-card-cta">Lanzar moneda →</span>
                </div>
            </a>
        </section>

        <section class="panel panel-highlight">
            <p class="label">Juegos disponibles</p>
            <p class="muted">Todos los juegos actualizan tu billetera y quedan reflejados en tu historial de apuestas.</p>
        </section>
    </div>
@endsection
