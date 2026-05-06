@extends('layouts.private')

@section('title', 'Billetera')
@section('topbar_title', 'Billetera')
@section('active_nav', 'billetera')

@section('content')
    <div class="page-header">
        <div>
            <h1 class="page-title">Billetera</h1>
        </div>
    </div>

    <div class="stack">
        <section class="hero-grid">
            <article class="panel panel-highlight">
                <p class="label">Saldo disponible</p>
                <p class="balance">
                    {{ number_format((float) $billetera->saldoDisponible, 2, ',', '.') }}
                    <span class="currency">{{ $billetera->moneda }}</span>
                </p>
                <div class="actions">
                    <button class="btn" type="button">Depositar</button>
                    <button class="btn secondary" type="button">Retirar</button>
                </div>
            </article>

            <article class="panel">
                <p class="label">Resumen</p>
                <div class="list">
                    <div class="list-item">
                        <span>Moneda</span>
                        <strong>{{ $billetera->moneda }}</strong>
                    </div>
                    <div class="list-item">
                        <span>Total de apuestas</span>
                        <strong>{{ $totalApuestas }}</strong>
                    </div>
                    <div class="list-item">
                        <span>Apuestas pendientes</span>
                        <strong>{{ $apuestasPendientes }}</strong>
                    </div>
                    <div class="list-item">
                        <span>Apuestas ganadas</span>
                        <strong>{{ $apuestasGanadas }}</strong>
                    </div>
                </div>
            </article>
        </section>

        <section class="panel">
            <p class="label">Ultimas apuestas</p>

            @if ($apuestas->isEmpty())
                <p class="empty-state">Todavia no hay apuestas asociadas a esta cuenta.</p>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Juego</th>
                                <th>Monto</th>
                                <th>Cuota</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($apuestas as $apuesta)
                                <tr>
                                    <td>{{ $apuesta->juego->nombre ?? ('Juego #' . $apuesta->juego_id) }}</td>
                                    <td>{{ number_format((float) $apuesta->monto, 2, ',', '.') }} {{ $billetera->moneda }}</td>
                                    <td>{{ number_format((float) $apuesta->cuota, 2, ',', '.') }}</td>
                                    <td><span class="badge {{ $apuesta->estado }}">{{ ucfirst($apuesta->estado) }}</span></td>
                                    <td>{{ $apuesta->fecha ? \Illuminate\Support\Carbon::parse($apuesta->fecha)->format('d/m/Y H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection
