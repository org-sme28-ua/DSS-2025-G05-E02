@extends('layouts.private')

@section('title', 'Billetera')
@section('topbar_title', 'Billetera')
@section('active_nav', 'billetera')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Billetera</h1>
            <p class="page-subtitle">Saldo ficticio disponible y últimos movimientos asociados a apuestas.</p>
        </div>
    </div>

    <div class="stack">
        <section class="hero-grid">
            <article class="panel panel-highlight">
                <p class="label">Total balance</p>
                <p class="balance">{{ number_format((float) $billetera->saldoDisponible, 2, ',', '.') }} <span class="currency">{{ $billetera->moneda }}</span></p>
                <div class="actions">
                    <button class="btn secondary" type="button" disabled>Recarga ficticia próximamente</button>
                    <button class="btn secondary" type="button" disabled>Retirar próximamente</button>
                </div>
            </article>

            <article class="panel">
                <p class="label">Resumen</p>
                <div class="list">
                    <div class="list-item"><span>Total apuestas</span><strong>{{ $totalApuestas }}</strong></div>
                    <div class="list-item"><span>Activas</span><strong>{{ $apuestasPendientes }}</strong></div>
                    <div class="list-item"><span>Ganadas</span><strong>{{ $apuestasGanadas }}</strong></div>
                </div>
            </article>
        </section>

        <section class="panel">
            <p class="label">Últimas apuestas</p>
            @if ($apuestas->isEmpty())
                <p class="empty-state">No hay movimientos todavía.</p>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Juego</th>
                                <th>Detalle</th>
                                <th>Monto</th>
                                <th>Estado</th>
                                <th>Saldo después</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($apuestas as $apuesta)
                                <tr>
                                    <td>{{ $apuesta->juego->nombre ?? ('Juego #' . $apuesta->juego_id) }}</td>
                                    <td>{{ $apuesta->descripcion ?: ($apuesta->seleccion ?: '-') }}</td>
                                    <td>{{ number_format((float) $apuesta->monto, 2, ',', '.') }} {{ $billetera->moneda }}</td>
                                    <td><span class="badge {{ $apuesta->estado }}">{{ $apuesta->estadoEtiqueta() }}</span></td>
                                    <td>{{ $apuesta->balance_despues !== null ? number_format((float) $apuesta->balance_despues, 2, ',', '.') . ' ' . $billetera->moneda : '-' }}</td>
                                    <td>{{ $apuesta->fecha ? $apuesta->fecha->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
