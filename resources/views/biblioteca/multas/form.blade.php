@php($prefijo = ($modo ?? 'crear') === 'editar' ? 'edit' : '')

<div class="row g-3">
    @if(($modo ?? 'crear') === 'crear')
        <div class="col-12 form-group-app">
            <label class="form-label-app">Préstamo *</label>
            <select name="id_prestamo" class="form-select-app" required>
                <option value="">Seleccione...</option>
                @foreach($prestamos as $prestamo)
                    <option value="{{ $prestamo->id }}">{{ $prestamo->codigo_prestamo }} - {{ $prestamo->socio->nombre_completo ?? '—' }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 form-group-app">
            <label class="form-label-app">Lector *</label>
            <select name="id_socio" class="form-select-app" required>
                <option value="">Seleccione...</option>
                @foreach($socios as $socio)
                    <option value="{{ $socio->id }}">{{ $socio->nombre_completo }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="col-6 form-group-app">
        <label class="form-label-app">Tipo de multa *</label>
        <select name="tipo_multa" id="{{ $prefijo }}TipoMulta" class="form-select-app" required>
            <option value="mora">Mora</option>
            <option value="perdida">Pérdida</option>
            <option value="dano">Daño</option>
            <option value="otro">Otro</option>
        </select>
    </div>
    <div class="col-6 form-group-app">
        <label class="form-label-app">Días de atraso</label>
        <input type="number" name="dias_atraso" id="{{ $prefijo }}DiasAtraso" class="form-control-app" min="0">
    </div>

    <div class="col-6 form-group-app">
        <label class="form-label-app">Monto (S/) *</label>
        <input type="number" step="0.01" name="monto" id="{{ $prefijo }}Monto" class="form-control-app" required min="0">
    </div>
    <div class="col-6 form-group-app">
        <label class="form-label-app">Fecha de multa *</label>
        <input type="date" name="fecha_multa" class="form-control-app" required value="{{ date('Y-m-d') }}">
    </div>

    @if(($modo ?? 'crear') === 'editar')
        <div class="col-6 form-group-app">
            <label class="form-label-app">Estado *</label>
            <select name="estado" id="{{ $prefijo }}EstadoMulta" class="form-select-app" required>
                <option value="pendiente">Pendiente</option>
                <option value="pagada">Pagada</option>
                <option value="exonerada">Exonerada</option>
            </select>
        </div>
        <div class="col-6 form-group-app">
            <label class="form-label-app">Fecha de pago</label>
            <input type="date" name="fecha_pago" id="{{ $prefijo }}FechaPago" class="form-control-app">
        </div>
    @endif

    <div class="col-12 form-group-app">
        <label class="form-label-app">Observaciones</label>
        <textarea name="observaciones" id="{{ $prefijo }}ObservacionesMulta" rows="2" class="form-control-app"></textarea>
    </div>
</div>
