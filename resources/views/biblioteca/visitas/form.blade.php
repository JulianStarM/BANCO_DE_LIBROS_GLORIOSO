@php($prefijo = ($modo ?? 'crear') === 'editar' ? 'edit' : '')

<div class="row g-3">
    <div class="col-12 form-group-app">
        <label class="form-label-app">Socio registrado</label>
        <select name="id_socio" id="{{ $prefijo }}IdSocioVisita" class="form-select-app">
            <option value="">Ninguno</option>
            @foreach($socios as $socio)
                <option value="{{ $socio->id }}">{{ $socio->nombre_completo }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-6 form-group-app">
        <label class="form-label-app">Tipo de visitante *</label>
        <select name="tipo_visitante" id="{{ $prefijo }}TipoVisitante" class="form-select-app" required>
            <option value="estudiante">Estudiante</option>
            <option value="docente">Docente</option>
            <option value="administrativo">Administrativo</option>
            <option value="externo">Externo</option>
        </select>
    </div>
    <div class="col-6 form-group-app">
        <label class="form-label-app">Nombres (si no es socio)</label>
        <input type="text" name="nombres" id="{{ $prefijo }}NombresVisita" class="form-control-app">
    </div>

    <div class="col-4 form-group-app">
        <label class="form-label-app">Grado</label>
        <input type="number" name="grado" id="{{ $prefijo }}GradoVisita" class="form-control-app" min="1">
    </div>
    <div class="col-4 form-group-app">
        <label class="form-label-app">Sección</label>
        <input type="text" name="seccion" id="{{ $prefijo }}SeccionVisita" class="form-control-app">
    </div>
    <div class="col-4 form-group-app">
        <label class="form-label-app">Motivo *</label>
        <select name="motivo" id="{{ $prefijo }}MotivoVisita" class="form-select-app" required>
            <option value="lectura">Lectura</option>
            <option value="investigacion">Investigación</option>
            <option value="prestamo">Préstamo</option>
            <option value="devolucion">Devolución</option>
            <option value="otro">Otro</option>
        </select>
    </div>

    <div class="col-4 form-group-app">
        <label class="form-label-app">Fecha *</label>
        <input type="date" name="fecha" id="{{ $prefijo }}FechaVisita" class="form-control-app" required value="{{ date('Y-m-d') }}">
    </div>
    <div class="col-4 form-group-app">
        <label class="form-label-app">Hora entrada *</label>
        <input type="time" name="hora_entrada" id="{{ $prefijo }}HoraEntrada" class="form-control-app" required value="{{ date('H:i') }}">
    </div>
    <div class="col-4 form-group-app">
        <label class="form-label-app">Hora salida</label>
        <input type="time" name="hora_salida" id="{{ $prefijo }}HoraSalida" class="form-control-app">
    </div>
</div>
