@if ($errors->any())
    <div class="alert alert-danger mb-4">
        <div class="d-flex align-items-center gap-2 fw-bold mb-2">
            <i data-lucide="alert-circle" class="icon"></i>
            Hay errores en el formulario:
        </div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <!-- Información Principal -->
    <div class="col-12 col-md-6">
        <h3 class="h6 fw-bold text-slate-800 text-uppercase pb-2 mb-3" style="border-bottom: 1px solid var(--border-color); letter-spacing: 0.05em;">
            Información Principal
        </h3>

        <div class="form-group-app">
            <label class="form-label-app">Código del Libro *</label>
            <input type="text" name="codigo_libro" value="{{ old('codigo_libro', $libro->codigo_libro ?? '') }}" class="form-control-app" required>
        </div>

        <div class="form-group-app">
            <label class="form-label-app">Nombre / Título *</label>
            <input type="text" name="nombre" value="{{ old('nombre', $libro->nombre ?? '') }}" class="form-control-app" required>
        </div>

        <div class="row g-3">
            <div class="col-6 form-group-app">
                <label class="form-label-app">Tipo de Material *</label>
                <select name="tipo_material" class="form-select-app" required>
                    <option value="">Seleccione...</option>
                    <option value="libro" @selected(old('tipo_material', $libro->tipo_material ?? '') == 'libro')>Libro</option>
                    <option value="cuaderno_trabajo" @selected(old('tipo_material', $libro->tipo_material ?? '') == 'cuaderno_trabajo')>Cuaderno de Trabajo</option>
                    <option value="guia_docente" @selected(old('tipo_material', $libro->tipo_material ?? '') == 'guia_docente')>Guía Docente</option>
                    <option value="material_complementario" @selected(old('tipo_material', $libro->tipo_material ?? '') == 'material_complementario')>Material Complementario</option>
                </select>
            </div>

            <div class="col-6 form-group-app">
                <label class="form-label-app">Área *</label>
                <input type="text" name="area" value="{{ old('area', $libro->area ?? '') }}" class="form-control-app" required>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-6 form-group-app">
                <label class="form-label-app">Nivel *</label>
                <select name="nivel" class="form-select-app" required>
                    <option value="">Seleccione...</option>
                    <option value="inicial" @selected(old('nivel', $libro->nivel ?? '') == 'inicial')>Inicial</option>
                    <option value="primaria" @selected(old('nivel', $libro->nivel ?? 'primaria') == 'primaria')>Primaria</option>
                    <option value="secundaria" @selected(old('nivel', $libro->nivel ?? '') == 'secundaria')>Secundaria</option>
                </select>
            </div>

            <div class="col-6 form-group-app">
                <label class="form-label-app">Grado *</label>
                <select name="grado" class="form-select-app" required>
                    <option value="">Seleccione...</option>
                    @for($i=1; $i<=6; $i++)
                        <option value="{{ $i }}" @selected(old('grado', $libro->grado ?? '') == $i)>{{ $i }}°</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <!-- Detalles Adicionales -->
    <div class="col-12 col-md-6">
        <h3 class="h6 fw-bold text-slate-800 text-uppercase pb-2 mb-3" style="border-bottom: 1px solid var(--border-color); letter-spacing: 0.05em;">
            Detalles Adicionales
        </h3>

        <div class="row g-3">
            <div class="col-6 form-group-app">
                <label class="form-label-app">Editorial</label>
                <input type="text" name="editorial" value="{{ old('editorial', $libro->editorial ?? '') }}" class="form-control-app">
            </div>

            <div class="col-6 form-group-app">
                <label class="form-label-app">Año Edición</label>
                <input type="number" name="anio_edicion" value="{{ old('anio_edicion', $libro->anio_edicion ?? '') }}" class="form-control-app" min="1900" max="{{ date('Y')+1 }}">
            </div>
        </div>

        <div class="form-group-app">
            <label class="form-label-app">Año Escolar (ID)</label>
            <select name="id_anio_escolar" class="form-select-app">
                <option value="">Ninguno</option>
                @foreach($aniosEscolares ?? [] as $anio)
                    <option value="{{ $anio->id }}" @selected(old('id_anio_escolar', $libro->id_anio_escolar ?? '') == $anio->id)>{{ $anio->anio }}</option>
                @endforeach
            </select>
        </div>

        <div class="row g-3">
            <div class="col-6 form-group-app">
                <label class="form-label-app">Cantidad Total *</label>
                <input type="number" name="cantidad_total" value="{{ old('cantidad_total', $libro->cantidad_total ?? 0) }}" class="form-control-app" min="0" required>
                @if(isset($libro))
                    <p class="form-text-app mb-0">Disp: {{ $libro->cantidad_disponible }} | Entr: {{ $libro->cantidad_entregada }}</p>
                @endif
            </div>

            @if(isset($libro))
                <div class="col-6 form-group-app">
                    <label class="form-label-app">Estado *</label>
                    <select name="estado" class="form-select-app" required>
                        <option value="activo" @selected(old('estado', $libro->estado ?? '') == 'activo')>Activo</option>
                        <option value="agotado" @selected(old('estado', $libro->estado ?? '') == 'agotado')>Agotado</option>
                        <option value="descontinuado" @selected(old('estado', $libro->estado ?? '') == 'descontinuado')>Descontinuado</option>
                        <option value="en_revision" @selected(old('estado', $libro->estado ?? '') == 'en_revision')>En Revisión</option>
                    </select>
                </div>
            @endif
        </div>

        <div class="form-group-app">
            <label class="form-label-app">Observaciones</label>
            <textarea name="observaciones" rows="3" class="form-control-app">{{ old('observaciones', $libro->observaciones ?? '') }}</textarea>
        </div>
    </div>
</div>
