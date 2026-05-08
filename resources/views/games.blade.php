@extends('layouts.private')

@section('title', 'Juegos')
@section('topbar_title', 'Juegos')
@section('active_nav', 'games')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Juegos</h1>
            <p class="page-subtitle">
                Elige un juego para apostar con dinero ficticio. Predicción sustituye a la antigua sección de deportes.
            </p>
        </div>
    </div>

    <div class="stack">
        <section class="game-grid">
            <a class="panel game-card" href="{{ route('roulette.index') }}">
                <strong>Ruleta</strong>
                <span>🎯</span>
                <p class="muted">Rojo, negro y verde. La animación muestra el resultado al finalizar.</p>
            </a>

            <a class="panel game-card" href="{{ route('prediction.index') }}">
                <strong>Predicción</strong>
                <span>🔮</span>
                <p class="muted">Crea una predicción y espera revisión del administrador.</p>
            </a>

            <article class="panel game-card">
                <strong>Bingo</strong>
                <span>🔢</span>
                <p class="muted">Pendiente de implementar.</p>
            </article>

            <article class="panel game-card">
                <strong>Slot machine</strong>
                <span>🎰</span>
                <p class="muted">Pendiente de implementar.</p>
            </article>
        </section>

        <section class="panel panel-highlight">
            <p class="label">Próximos pasos</p>
            <p class="muted">Cuando implementemos Bingo o Slot machine, los añadiremos aquí sin tocar el dashboard principal.</p>
        </section>
    </div>
@endsection
