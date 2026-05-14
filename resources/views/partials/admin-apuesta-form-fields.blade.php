@php
  $bet = $bet ?? null;
@endphp
<label><span class="field-label">Usuario</span><select class="input-sm" name="user_id" required>@foreach($allUsers as $usuario)<option value="{{ $usuario->id }}" @selected($bet && (int)$bet->user_id === (int)$usuario->id)>{{ $usuario->name }} · {{ $usuario->email }}</option>@endforeach</select></label>
<label><span class="field-label">Juego</span><select class="input-sm" name="juego_id" required>@foreach($juegos as $juego)<option value="{{ $juego->id }}" @selected($bet && (int)$bet->juego_id === (int)$juego->id)>{{ $juego->nombre }}</option>@endforeach</select></label>
<label><span class="field-label">Tipo</span><input class="input-sm" name="tipo" value="{{ old('tipo', $bet->tipo ?? 'general') }}"></label>
<label><span class="field-label">Estado</span><select class="input-sm" name="estado" required>@foreach(['pendiente','aceptada','rechazada','ganada','perdida'] as $estado)<option value="{{ $estado }}" @selected(old('estado', $bet->estado ?? 'pendiente') === $estado)>{{ ucfirst($estado) }}</option>@endforeach</select></label>
<label><span class="field-label">Monto</span><input class="input-sm" type="number" step="0.01" min="0.01" name="monto" value="{{ old('monto', $bet->monto ?? 1) }}" required></label>
<label><span class="field-label">Cuota</span><input class="input-sm" type="number" step="0.01" min="1" name="cuota" value="{{ old('cuota', $bet->cuota ?? 2) }}" required></label>
<label class="full"><span class="field-label">Descripción</span><textarea class="input-sm" name="descripcion" rows="2">{{ old('descripcion', $bet->descripcion ?? '') }}</textarea></label>
<label><span class="field-label">Selección</span><input class="input-sm" name="seleccion" value="{{ old('seleccion', $bet->seleccion ?? '') }}"></label>
<label><span class="field-label">Resultado</span><input class="input-sm" name="resultado" value="{{ old('resultado', $bet->resultado ?? '') }}"></label>
<label><span class="field-label">Fecha</span><input class="input-sm" type="datetime-local" name="fecha" value="{{ old('fecha', $bet && $bet->fecha ? $bet->fecha->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required></label>
<label><span class="field-label">Admin</span><select class="input-sm" name="admin_id"><option value="">Sin admin</option>@foreach($allUsers as $usuario)<option value="{{ $usuario->id }}" @selected($bet && (int)$bet->admin_id === (int)$usuario->id)>{{ $usuario->name }}</option>@endforeach</select></label>
<label><span class="field-label">Balance antes</span><input class="input-sm" type="number" step="0.01" name="balance_antes" value="{{ old('balance_antes', $bet->balance_antes ?? '') }}"></label>
<label><span class="field-label">Balance después</span><input class="input-sm" type="number" step="0.01" name="balance_despues" value="{{ old('balance_despues', $bet->balance_despues ?? '') }}"></label>
<label><span class="field-label">Resuelta en</span><input class="input-sm" type="datetime-local" name="resuelta_at" value="{{ old('resuelta_at', $bet && $bet->resuelta_at ? $bet->resuelta_at->format('Y-m-d\TH:i') : '') }}"></label>
