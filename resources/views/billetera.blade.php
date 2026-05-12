@extends('layouts.private')

@section('title', 'Billetera')
@section('topbar_title', 'Billetera')
@section('active_nav', 'billetera')

@section('content')
    <style>
        .wallet-forms { display: grid; grid-template-columns: repeat(auto-fit, minmax(310px, 1fr)); gap: 18px; }
        .payment-card-preview {
            border-radius: 18px; padding: 18px; min-height: 128px;
            background: linear-gradient(135deg, rgba(240,192,64,.34), rgba(255,255,255,.08)), var(--surface-strong);
            border: 1px solid rgba(240,192,64,.26);
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .payment-card-preview .digits { letter-spacing: .18em; font-weight: 800; color: #fff; }
        .payment-card-preview .small { display:flex; justify-content:space-between; gap:10px; color: var(--muted); font-size: 13px; }
        .wallet-help { margin: 0; color: var(--muted); line-height: 1.6; font-size: 14px; }
    </style>

    <div class="page-header">
        <div>
            <h1 class="page-title">Billetera</h1>
            <p class="page-subtitle">Saldo disponible, ingresos, retiradas y últimos movimientos asociados a tus apuestas.</p>
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
                <p class="label">Total balance</p>
                <p class="balance">{{ number_format((float) $billetera->saldoDisponible, 2, ',', '.') }} <span class="currency">{{ $billetera->moneda }}</span></p>
                <p class="wallet-help" style="margin-top:18px;">Gestiona tu saldo con validación de tarjeta y consulta el estado de tus movimientos.</p>
                <div class="payment-card-preview" style="margin-top:22px;">
                    <div class="digits">•••• •••• •••• 0000</div>
                    <div class="small"><span>BOOKIE CARD</span><span>MM/AA</span></div>
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

        <section class="wallet-forms">
            <article class="panel">
                <p class="label">Ingresar dinero</p>
                <p class="wallet-help">Añade saldo validando el formato de tarjeta, caducidad y CVC.</p>

                <form class="form-grid" method="POST" action="{{ route('billetera.deposit') }}" style="margin-top:18px;">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="deposit_amount">Cantidad</label>
                        <input class="form-control" id="deposit_amount" name="amount" type="number" step="0.01" min="5" max="10000" value="{{ old('amount') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="deposit_holder">Titular</label>
                        <input class="form-control" id="deposit_holder" name="card_holder" type="text" maxlength="80" placeholder="Nombre Apellido" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="deposit_card">Número de tarjeta</label>
                        <input class="form-control" id="deposit_card" name="card_number" type="text" inputmode="numeric" maxlength="25" placeholder="1234 5678 9012 3456" autocomplete="off" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="deposit_expiry">Caducidad</label>
                            <input class="form-control" id="deposit_expiry" name="card_expiry" type="text" maxlength="5" placeholder="MM/AA" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="deposit_cvc">CVC</label>
                            <input class="form-control" id="deposit_cvc" name="card_cvc" type="text" inputmode="numeric" maxlength="4" placeholder="123" autocomplete="off" required>
                        </div>
                    </div>
                    <button class="btn" type="submit">Ingresar saldo</button>
                </form>
            </article>

            <article class="panel">
                <p class="label">Retirar dinero</p>
                <p class="wallet-help">Retira saldo a tarjeta. La cantidad no puede superar tu saldo actual.</p>

                <form class="form-grid" method="POST" action="{{ route('billetera.withdraw') }}" style="margin-top:18px;">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="withdraw_amount">Cantidad</label>
                        <input class="form-control" id="withdraw_amount" name="amount" type="number" step="0.01" min="1" max="{{ (float) $billetera->saldoDisponible }}" value="{{ old('amount') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="withdraw_holder">Titular</label>
                        <input class="form-control" id="withdraw_holder" name="card_holder" type="text" maxlength="80" placeholder="Nombre Apellido" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="withdraw_card">Número de tarjeta</label>
                        <input class="form-control" id="withdraw_card" name="card_number" type="text" inputmode="numeric" maxlength="25" placeholder="1234 5678 9012 3456" autocomplete="off" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="withdraw_expiry">Caducidad</label>
                            <input class="form-control" id="withdraw_expiry" name="card_expiry" type="text" maxlength="5" placeholder="MM/AA" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="withdraw_cvc">CVC</label>
                            <input class="form-control" id="withdraw_cvc" name="card_cvc" type="text" inputmode="numeric" maxlength="4" placeholder="123" autocomplete="off" required>
                        </div>
                    </div>
                    <button class="btn secondary" type="submit">Retirar saldo</button>
                </form>
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
