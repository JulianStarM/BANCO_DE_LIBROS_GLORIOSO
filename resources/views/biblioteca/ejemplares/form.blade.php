@php($prefijo = ($modo ?? 'crear') === 'editar' ? 'edit' : '')

<div class="row g-3">
    <div class="col-12 form-group-app">
        <label class="form-label-app">Título *</label>
        <select name="id_catalogo" id="{{ $prefijo }}IdCatalogo" class="form-select-app" required>
            <option value="">Seleccione...</option>
            @foreach($catalogos as $catalogo)
                <option value="{{ $catalogo->id }}">{{ $catalogo->titulo }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-6 form-group-app">
        <label class="form-label-app">Código ejemplar *</label>
        <input type="text" name="codigo_ejemplar" id="{{ $prefijo }}CodigoEjemplar" class="form-control-app" required>
    </div>
    <div class="col-6 form-group-app">
        <label class="form-label-app">N° registro *</label>
        <input type="number" name="numero_registro" id="{{ $prefijo }}NumeroRegistro" class="form-control-app" required min="1">
    </div>

    <div class="col-6 form-group-app">
        <label class="form-label-app">Fecha de ingreso *</label>
        <input type="date" name="fecha_ingreso" id="{{ $prefijo }}FechaIngreso" class="form-control-app" required>
    </div>
    <div class="col-6 form-group-app">
        <label class="form-label-app">Procedencia</label>
        <input type="text" name="procedencia" id="{{ $prefijo }}Procedencia" class="form-control-app">
    </div>

    <div class="col-6 form-group-app">
        <label class="form-label-app">Estado físico *</label>
        <select name="estado_fisico" id="{{ $prefijo }}EstadoFisico" class="form-select-app" required>
            <option value="bueno">Bueno</option>
            <option value="regular">Regular</option>
            <option value="malo">Malo</option>
            <option value="baja">Baja</option>
        </select>
    </div>
    <div class="col-6 form-group-app">
        <label class="form-label-app">Disponibilidad *</label>
        <select name="estado_disponibilidad" id="{{ $prefijo }}EstadoDisponibilidad" class="form-select-app" required>
            <option value="disponible">Disponible</option>
            <option value="prestado">Prestado</option>
            <option value="reservado">Reservado</option>
            <option value="baja">Baja</option>
        </select>
    </div>

    <div class="col-6 form-group-app">
        <label class="form-label-app">Estante</label>
        <select name="id_estante" id="{{ $prefijo }}IdEstante" class="form-select-app">
            <option value="">Seleccione...</option>
            @foreach($estantes as $estante)
                <option value="{{ $estante->id }}">{{ $estante->codigo_estante }} - {{ $estante->nombre }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 form-group-app">
        <label class="form-label-app">Precio adquisición</label>
        <input type="number" step="0.01" name="precio_adquisicion" id="{{ $prefijo }}PrecioAdquisicion" class="form-control-app" min="0">
    </div>

    <div class="col-12 form-group-app">
        <label class="form-label-app">Observaciones</label>
        <textarea name="observaciones" id="{{ $prefijo }}ObservacionesEjemplar" rows="2" class="form-control-app"></textarea>
    </div>
</div>
