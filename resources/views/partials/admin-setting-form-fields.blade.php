@php $setting = $setting ?? null; @endphp
<label><span class="field-label">Clave</span><input class="input-sm" name="clave" value="{{ old('clave', $setting->clave ?? '') }}" required></label>
<label><span class="field-label">Valor</span><input class="input-sm" name="valor" value="{{ old('valor', $setting->valor ?? '') }}" required></label>
<label class="full"><span class="field-label">Descripción</span><textarea class="input-sm" name="descripcion" rows="3">{{ old('descripcion', $setting->descripcion ?? '') }}</textarea></label>
<label><span class="field-label">Activo</span><input type="hidden" name="activo" value="0"><select class="input-sm" name="activo"><option value="1" @selected(!$setting || $setting->activo)>Sí</option><option value="0" @selected($setting && !$setting->activo)>No</option></select></label>
