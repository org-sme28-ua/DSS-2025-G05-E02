<div class="table-scroll">
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Juego</th>
        <th>Detalle</th>
        <th>Monto</th>
        <th>Balance</th>
        <th>Estado</th>
        <th>Fecha</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($apuestas as $bet)
        <tr>
          <td>#{{ $bet->id }}</td>
          <td>
            <strong>{{ $bet->user->name ?? ('User #' . $bet->user_id) }}</strong>
            <div class="muted">{{ $bet->user->email ?? '' }}</div>
          </td>
          <td>
            <strong>{{ $bet->juego->nombre ?? ('Juego #' . $bet->juego_id) }}</strong>
            <div class="muted">{{ ucfirst($bet->tipo ?? 'general') }}</div>
          </td>
          <td>
            {{ $bet->descripcion ?: '-' }}
            @if($bet->seleccion)<div class="muted">Selección: {{ $bet->seleccion }}</div>@endif
            @if($bet->resultado)<div class="muted">Resultado: {{ $bet->resultado }}</div>@endif
          </td>
          <td>{{ $money($bet->monto) }}<div class="muted">Cuota {{ number_format((float)$bet->cuota, 2, ',', '.') }}</div></td>
          <td>
            <span class="muted">Antes</span> {{ $bet->balance_antes !== null ? $money($bet->balance_antes) : '-' }}
            <div><span class="muted">Después</span> {{ $bet->balance_despues !== null ? $money($bet->balance_despues) : '-' }}</div>
          </td>
          <td><span class="badge {{ $statusClass($bet->estado) }}">{{ $bet->estadoEtiqueta() }}</span></td>
          <td>{{ $bet->fecha ? $bet->fecha->format('d/m/Y H:i') : '-' }}</td>
          <td class="actions-cell">
            <div class="action-buttons">
              <button class="btn btn-sm" onclick="showUserSummary({{ $bet->user_id }})">Usuario</button>
              <button class="btn btn-sm" onclick="showGameSummary({{ $bet->juego_id }})">Juego</button>
              @if($bet->tipo === 'prediccion')
                <a class="btn btn-sm" href="{{ $sectionUrl('predicciones', ['pred_user_id' => $bet->user_id]) }}">Predicción</a>
              @endif
              <details class="crud-details">
                <summary class="btn btn-sm">Editar</summary>
                <div class="edit-popover">
                  <h4>Editar apuesta #{{ $bet->id }}</h4>
                  <form method="POST" action="{{ route('admin.apuestas.update', $bet) }}" class="admin-form-grid">
                    @csrf
                    @method('PUT')
                    @include('partials.admin-apuesta-form-fields', ['bet' => $bet, 'allUsers' => $allUsers, 'juegos' => $juegos])
                    <div class="form-actions"><button class="btn btn-primary">Guardar</button></div>
                  </form>
                </div>
              </details>
              <form method="POST" action="{{ route('admin.apuestas.destroy', $bet) }}" onsubmit="return confirm('¿Eliminar esta apuesta?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger">Eliminar</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="9" class="muted">No hay apuestas con estos filtros.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="pagination-wrap">@include('partials.pagination', ['paginator' => $apuestas])</div>
