<div class="table-scroll">
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Juego</th>
        <th>Detalle</th>
        <th>Monto</th>
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
          <td><span class="badge {{ $statusClass($bet->estado) }}">{{ $bet->estadoEtiqueta() }}</span></td>
          <td>{{ $bet->fecha ? $bet->fecha->format('d/m/Y H:i') : '-' }}</td>
          <td class="actions-cell">
            <div class="action-buttons">
              <button class="btn btn-sm" onclick="showUserSummary({{ $bet->user_id }})">Usuario</button>
              @if($bet->tipo === 'prediccion' && in_array($bet->estado, ['pendiente', 'aceptada'], true))
                <a class="btn btn-sm" href="{{ $sectionUrl('predicciones', ['pred_user_id' => $bet->user_id]) }}">Resolver</a>
              @elseif($bet->tipo === 'prediccion')
                <a class="btn btn-sm" href="{{ $sectionUrl('predicciones', ['pred_user_id' => $bet->user_id]) }}">Ver predicción</a>
              @endif
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="8" class="muted">No hay apuestas con estos filtros.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="pagination-wrap">@include('partials.pagination', ['paginator' => $apuestas])</div>
