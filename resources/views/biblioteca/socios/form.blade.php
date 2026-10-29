@php($prefijo = ($modo ?? 'crear') === 'editar' ? 'edit' : '')

<div class="row g-3">
    <div class="col-12 form-group-app">
        <label class="form-label-app">Vincular con estudiante</label>
        <select name="id_estudiante" id="{{ $prefijo }}IdEstudiante" class="form-select-app">
            <option value="">Ninguno</option>
            @foreach($estudiantes as $estudiante)
                <option value="{{ $estudiante->id }}">{{ $estudiante->nombre_completo }} ({{ $estudiante->dni }})</option>
            @endforeach
        </select>
    </div>

    <div class="col-6 form-group-app">
        <label class="form-label-app">Tipo de lector *</label>
        <select name="tipo_socio" id="{{ $prefijo }}TipoSocio" class="form-select-app" required>
            <option value="estudiante">Estudiante</option>
            <option value="docente">Docente</option>
            <option value="administrativo">Administrativo</option>
            <option value="externo">Externo</option>
        </select>
    </div>
    <div class="col-6 form-group-app">
        <label class="form-label-app">Código lector</label>
        <input type="text" name="codigo_socio" id="{{ $prefijo }}CodigoSocio" class="form-control-app">
    </div>

    <div class="col-6 form-group-app">
        <label class="form-label-app">DNI *</label>
        <input type="text" name="dni" id="{{ $prefijo }}Dni" class="form-control-app" required>
    </div>
    <div class="col-6 form-group-app">
        <label class="form-label-app">Teléfono</label>
        <input type="text" name="telefono" id="{{ $prefijo }}Telefono" class="form-control-app">
    </div>

    <div class="col-6 form-group-app">
        <label class="form-label-app">Nombres *</label>
        <input type="text" name="nombres" id="{{ $prefijo }}Nombres" class="form-control-app" required>
    </div>
    <div class="col-6 form-group-app">
        <label class="form-label-app">Apellidos *</label>
        <input type="text" name="apellidos" id="{{ $prefijo }}Apellidos" class="form-control-app" required>
    </div>

    <div class="col-4 form-group-app">
        <label class="form-label-app">Grado</label>
        <input type="number" name="grado" id="{{ $prefijo }}Grado" class="form-control-app" min="1">
    </div>
    <div class="col-4 form-group-app">
        <label class="form-label-app">Sección</label>
        <input type="text" name="seccion" id="{{ $prefijo }}Seccion" class="form-control-app">
    </div>
    <div class="col-4 form-group-app">
        <label class="form-label-app">Fecha inscripción *</label>
        <input type="date" name="fecha_inscripcion" id="{{ $prefijo }}FechaInscripcion" class="form-control-app" required value="{{ date('Y-m-d') }}">
    </div>

    <div class="col-6 form-group-app">
        <label class="form-label-app">Estado *</label>
        <select name="estado" id="{{ $prefijo }}EstadoSocio" class="form-select-app" required>
            <option value="activo">Activo</option>
            <option value="suspendido">Suspendido</option>
            <option value="inactivo">Inactivo</option>
        </select>
    </div>

    <div class="col-12 form-group-app">
        <label class="form-label-app">Observaciones</label>
        <textarea name="observaciones" id="{{ $prefijo }}ObservacionesSocio" rows="2" class="form-control-app"></textarea>
    </div>
</div>
