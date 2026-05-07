@extends('layouts.private')

@section('title', 'Predicción')
@section('topbar_title', 'Predicción')
@section('active_nav', 'games')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Predicción</h1>
            <p class="page-subtitle">
                Crea apuestas sobre cualquier predicción. La apuesta queda pendiente y un administrador debe aceptarla,
                rechazarla o resolverla como ganada/perdida.
            </p>
        </div>
        <div class="panel" style="text-align:right; min-width:220px;">
            <p class="label">Saldo disponible</p>
            <div class="stat-value">{{ number_format((float) $wallet->saldoDisponible, 2, ',', '.') }} EUR</div>
        </div>
    </div>

    <div class="stack">
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert error">
                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="hero-grid">
            <article class="panel panel-highlight">
                <p class="label">Nueva predicción</p>
                <form method="POST" action="{{ route('prediction.store') }}" class="form-grid">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="descripcion">¿Qué quieres predecir?</label>
                        <input class="form-control" id="descripcion" name="descripcion" maxlength="255" value="{{ old('descripcion') }}" placeholder="Ej: El Real Madrid gana su próximo partido">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="seleccion">Tu resultado esperado</label>
                        <textarea class="form-control" id="seleccion" name="seleccion" placeholder="Explica claramente qué tiene que pasar para que tu predicción sea ganadora">{{ old('seleccion') }}</textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="amount">Cantidad a apostar</label>
                            <input class="form-control" id="amount" name="amount" type="number" min="1" step="0.01" value="{{ old('amount', 10) }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cuota inicial</label>
                            <input class="form-control" value="2.00" disabled>
                        </div>
                    </div>

                    <button class="btn" type="submit">Enviar a revisión</button>
                </form>
            </article>

            <article class="panel">
                <p class="label">Reglas básicas</p>
                <div class="list">
                    <div class="list-item">
                        <div>
                            <strong>1. Pendiente</strong>
                            <div class="muted">Al crearla se descuenta la cantidad apostada y queda esperando revisión.</div>
                        </div>
                    </div>
                    <div class="list-item">
                        <div>
                            <strong>2. Revisión admin</strong>
                            <div class="muted">El administrador puede aceptarla o rechazarla. Si la rechaza, se reembolsa.</div>
                        </div>
                    </div>
                    <div class="list-item">
                        <div>
                            <strong>3. Resolución</strong>
                            <div class="muted">Cuando se sepa el resultado, el administrador la marca como ganada o perdida.</div>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        <section class="panel">
            <p class="label">Mis predicciones</p>
            @if ($bets->isEmpty())
                <p class="empty-state">Todavía no has creado predicciones.</p>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Predicción</th>
                                <th>Tu selección</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Resultado admin</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bets as $bet)
                                <tr>
                                    <td><strong>{{ $bet->descripcion }}</strong></td>
                                    <td>{{ $bet->seleccion }}</td>
                                    <td>{{ number_format((float) $bet->monto, 2, ',', '.') }} EUR</td>
                                    <td><span class="badge {{ $bet->estado }}">{{ $bet->estadoEtiqueta() }}</span></td>
                                    <td>{{ $bet->resultado ?: '-' }}</td>
                                    <td>{{ $bet->fecha ? $bet->fecha->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
