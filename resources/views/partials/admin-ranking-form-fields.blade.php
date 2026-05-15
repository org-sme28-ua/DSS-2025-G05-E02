@php $ranking = $ranking ?? null; @endphp
<label><span class="field-label">Usuario</span><select name="user_id" class="input-sm" required>@foreach($usuariosAdmin as $u)<option value="{{ $u->id }}" @selected($ranking && (int)$ranking->user_id === (int)$u->id)>{{ $u->name }} ({{ $u->email }})</option>@endforeach</select></label>
<label><span class="field-label">Posición</span><input type="number" name="posicion" class="input-sm" value="{{ old('posicion', $ranking->posicion ?? 1) }}" min="1" required></label>
<label><span class="field-label">Puntos</span><input type="number" name="puntos" class="input-sm" value="{{ old('puntos', $ranking->puntos ?? 0) }}" min="0" required></label>
<label><span class="field-label">Total ganado</span><input type="number" name="total_ganado" class="input-sm" value="{{ old('total_ganado', $ranking->total_ganado ?? 0) }}" min="0" step="0.01" required></label>
