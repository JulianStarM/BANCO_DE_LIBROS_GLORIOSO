@php($modo = $modo ?? 'crear')
@php($prefijo = $modo === 'editar' ? 'edit' : '')

<div class="row g-4">
    <div class="col-12 col-md-6">
        <h3 class="h6 fw-bold text-slate-800 text-uppercase pb-2 mb-3" style="border-bottom: 1px solid var(--border-color); letter-spacing: 0.05em;">Información Principal</h3>

        <div class="form-group-app">
            <label class="form-label-app">Título *</label>
            <input type="text" name="titulo" id="{{ $prefijo }}Titulo" class="form-control-app" required value="{{ $modo === 'editar' ? '' : '' }}">
        </div>

        <div class="form-group-app">
            <label class="form-label-app">Subtítulo</label>
            <input type="text" name="subtitulo" id="{{ $prefijo }}Subtitulo" class="form-control-app">
        </div>

        <div class="row g-3">
            <div class="col-6 form-group-app">
                <label class="form-label-app">ISBN</label>
                <input type="text" name="isbn" id="{{ $prefijo }}Isbn" class="form-control-app">
            </div>
            <div class="col-6 form-group-app">
                <label class="form-label-app">Edición</label>
                <input type="text" name="edicion" id="{{ $prefijo }}Edicion" class="form-control-app">
            </div>
        </div>

        <div class="row g-3">
            <div class="col-6 form-group-app">
                <label class="form-label-app">Año publicación</label>
                <input type="number" name="anio_publicacion" id="{{ $prefijo }}AnioPublicacion" class="form-control-app" min="1000" max="{{ date('Y')+1 }}">
            </div>
            <div class="col-6 form-group-app">
                <label class="form-label-app">Páginas</label>
                <input type="number" name="numero_paginas" id="{{ $prefijo }}NumeroPaginas" class="form-control-app" min="1">
            </div>
        </div>

        <div class="form-group-app">
            <label class="form-label-app">Idioma</label>
            <input type="text" name="idioma" id="{{ $prefijo }}Idioma" class="form-control-app" value="Español">
        </div>
    </div>

    <div class="col-12 col-md-6">
        <h3 class="h6 fw-bold text-slate-800 text-uppercase pb-2 mb-3" style="border-bottom: 1px solid var(--border-color); letter-spacing: 0.05em;">Clasificación y Autores</h3>

        <div class="form-group-app">
            <label class="form-label-app">Tipo de material *</label>
            <select name="id_tipo_material" id="{{ $prefijo }}IdTipoMaterial" class="form-select-app" required>
                <option value="">Seleccione...</option>
                @foreach($tipos as $tipo)
                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="row g-3">
            <div class="col-6 form-group-app">
                <label class="form-label-app">Área temática</label>
                <select name="id_area" id="{{ $prefijo }}IdArea" class="form-select-app">
                    <option value="">Seleccione...</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 form-group-app">
                <label class="form-label-app">Editorial</label>
                <select name="id_editorial" id="{{ $prefijo }}IdEditorial" class="form-select-app">
                    <option value="">Seleccione...</option>
                    @foreach($editoriales as $editorial)
                        <option value="{{ $editorial->id }}">{{ $editorial->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group-app">
            <label class="form-label-app">Autores</label>
            <div class="border rounded p-2" style="max-height: 160px; overflow-y: auto;">
                @forelse($autores as $autor)
                    <div class="form-check">
                        <input class="form-check-input checkbox-autor-{{ $modo === 'editar' ? 'edit' : 'create' }}" type="checkbox" name="autores[]" value="{{ $autor->id }}" id="autor_{{ $modo }}_{{ $autor->id }}">
                        <label class="form-check-label" for="autor_{{ $modo }}_{{ $autor->id }}">
                            {{ $autor->nombre_completo }}
                        </label>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 mb-0">No hay autores registrados.</p>
                @endforelse
            </div>
        </div>

        <div class="form-group-app">
            <label class="form-label-app">Descripción</label>
            <textarea name="descripcion" id="{{ $prefijo }}Descripcion" rows="3" class="form-control-app"></textarea>
        </div>

        <input type="hidden" name="estado" value="0">
        <div class="form-group-app">
            <label class="form-check-app d-flex align-items-center gap-2">
                <input type="checkbox" name="estado" id="{{ $prefijo }}EstadoCatalogo" value="1" checked class="form-check-input">
                <span>Activo</span>
            </label>
        </div>
    </div>
</div>
