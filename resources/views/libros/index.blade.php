@extends('layouts.app')

@section('title', 'Libros')
@section('page-title', 'Gestión de Libros')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="h3 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-1">
            <span class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success" style="width: 2.5rem; height: 2.5rem; font-size: 1.125rem;">
                <i data-lucide="book" class="icon"></i>
            </span>
            Catálogo de Libros
        </h2>
        <p class="text-sm text-slate-500 mb-0">Administra el inventario de libros y materiales educativos.</p>
    </div>

    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button type="button" onclick="abrirModalNuevoLibro()" class="btn-app btn-app-primary">
            <i data-lucide="plus" class="icon"></i>
            Nuevo Libro
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="filter-bar mb-4">
    <form method="GET" action="{{ route('libros.index') }}" class="row g-3 align-items-end w-100">
        <div class="col-lg-4">
            <label class="form-label-app">Buscar</label>
            <div class="position-relative">
                <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400">
                    <i data-lucide="search" class="icon"></i>
                </span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control-app ps-5" placeholder="Nombre, código o área...">
            </div>
        </div>
        <div class="col-lg-2">
            <label class="form-label-app">Área</label>
            <select name="area" class="form-select-app">
                <option value="">Todas las áreas</option>
                @foreach($areas as $area)
                    <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>{{ $area }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2">
            <label class="form-label-app">Nivel</label>
            <select name="nivel" class="form-select-app">
                <option value="">Todos los niveles</option>
                @foreach($niveles as $nivel)
                    <option value="{{ $nivel }}" {{ request('nivel') == $nivel ? 'selected' : '' }}>{{ ucfirst($nivel) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2">
            <label class="form-label-app">Grado</label>
            <select name="grado" class="form-select-app">
                <option value="">Todos los grados</option>
                @foreach($grados as $grado)
                    <option value="{{ $grado }}" {{ request('grado') == $grado ? 'selected' : '' }}>{{ $grado }}° Grado</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2">
            <button type="submit" class="btn-app btn-app-secondary w-100">
                Filtrar
            </button>
        </div>
        <div class="col-lg-2">
            <a href="{{ route('libros.index') }}" class="btn-app btn-app-light w-100">Limpiar</a>
        </div>
    </form>
</div>

<!-- Tabla -->
<div class="table-responsive-app">
    <table class="table-app">
        <thead>
            <tr>
                <th>Código</th>
                <th>Libro</th>
                <th>Área / Grado</th>
                <th class="text-center">Disponibles</th>
                <th class="text-center">Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($libros as $libro)
                <tr>
                    <td class="fw-medium text-slate-900 text-nowrap">
                        {{ $libro->codigo_libro }}
                    </td>
                    <td>
                        <div class="book-title">{{ $libro->nombre }}</div>
                        <div class="book-meta text-capitalize">{{ str_replace('_', ' ', $libro->tipo_material) }}</div>
                    </td>
                    <td>
                        <span class="badge bg-slate-100 text-slate-700">{{ $libro->area }}</span>
                        <span class="text-xs text-slate-500 ms-2">{{ $libro->grado }}° {{ ucfirst($libro->nivel) }}</span>
                    </td>
                    <td class="text-center">
                        @if($libro->cantidad_disponible > 5)
                            <span class="stock-badge success">
                                {{ $libro->cantidad_disponible }} / {{ $libro->cantidad_total }}
                            </span>
                        @elseif($libro->cantidad_disponible > 0)
                            <span class="stock-badge warning">
                                {{ $libro->cantidad_disponible }} / {{ $libro->cantidad_total }}
                            </span>
                        @else
                            <span class="stock-badge danger">
                                Agotado
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        @switch($libro->estado)
                            @case('activo')
                                <span class="book-status activo">
                                    <span class="d-inline-block rounded-circle bg-success me-1" style="width: 0.375rem; height: 0.375rem;"></span> Activo
                                </span>
                                @break
                            @case('agotado')
                                <span class="book-status agotado">
                                    <span class="d-inline-block rounded-circle bg-warning me-1" style="width: 0.375rem; height: 0.375rem;"></span> Agotado
                                </span>
                                @break
                            @case('descontinuado')
                                <span class="book-status descontinuado">
                                    <span class="d-inline-block rounded-circle bg-secondary me-1" style="width: 0.375rem; height: 0.375rem;"></span> Descontinuado
                                </span>
                                @break
                            @case('en_revision')
                                <span class="book-status en_revision">
                                    <span class="d-inline-block rounded-circle bg-info me-1" style="width: 0.375rem; height: 0.375rem;"></span> Revisión
                                </span>
                                @break
                        @endswitch
                    </td>
                    <td class="actions-cell">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <button type="button"
                                    class="btn-action"
                                    title="Ver detalles"
                                    onclick="verLibro({{ json_encode([
                                        'id' => $libro->id,
                                        'codigo_libro' => $libro->codigo_libro,
                                        'nombre' => $libro->nombre,
                                        'tipo_material' => $libro->tipo_material,
                                        'area' => $libro->area,
                                        'grado' => $libro->grado,
                                        'nivel' => $libro->nivel,
                                        'editorial' => $libro->editorial,
                                        'anio_edicion' => $libro->anio_edicion,
                                        'cantidad_total' => $libro->cantidad_total,
                                        'cantidad_disponible' => $libro->cantidad_disponible,
                                        'cantidad_entregada' => $libro->cantidad_entregada,
                                        'cantidad_devuelta' => $libro->cantidad_devuelta,
                                        'cantidad_perdida' => $libro->cantidad_perdida,
                                        'cantidad_deficiente' => $libro->cantidad_deficiente,
                                        'estado' => $libro->estado,
                                        'observaciones' => $libro->observaciones,
                                    ]) }})">
                                <i data-lucide="eye" class="icon"></i>
                            </button>
                            <button type="button"
                                    class="btn-action btn-action-primary"
                                    title="Editar"
                                    onclick="editarLibro({{ json_encode([
                                        'id' => $libro->id,
                                        'codigo_libro' => $libro->codigo_libro,
                                        'nombre' => $libro->nombre,
                                        'tipo_material' => $libro->tipo_material,
                                        'area' => $libro->area,
                                        'grado' => $libro->grado,
                                        'nivel' => $libro->nivel,
                                        'editorial' => $libro->editorial,
                                        'anio_edicion' => $libro->anio_edicion,
                                        'id_anio_escolar' => $libro->id_anio_escolar,
                                        'cantidad_total' => $libro->cantidad_total,
                                        'estado' => $libro->estado,
                                        'observaciones' => $libro->observaciones,
                                    ]) }})">
                                <i data-lucide="pencil" class="icon"></i>
                            </button>
                            <form action="{{ route('libros.destroy', $libro) }}" method="POST" class="d-inline"
                                  data-confirm="true"
                                  data-confirm-title="¿Eliminar libro?"
                                  data-confirm-message="El libro {{ $libro->nombre }} será eliminado junto con todo su historial de inventario, entregas y devoluciones."
                                  data-confirm-text="Eliminar">
                                @csrf @method('DELETE')
                                <input type="hidden" name="force" value="1">
                                <button type="submit" class="btn-action btn-action-danger" title="Eliminar">
                                    <i data-lucide="trash-2" class="icon"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center py-4">
                            <span class="d-flex align-items-center justify-content-center rounded-circle bg-slate-100 text-slate-400 mb-3" style="width: 3rem; height: 3rem; font-size: 1.25rem;">
                                <i data-lucide="book-x" class="icon"></i>
                            </span>
                            <p class="fw-medium text-slate-900 mb-1">No se encontraron libros</p>
                            <p class="text-sm text-slate-500 mb-0">Ajusta los filtros o registra un nuevo material educativo.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($libros->hasPages())
    <div class="mt-3">
        {{ $libros->links('pagination::bootstrap-5') }}
    </div>
@endif

{{-- Modal Ver Libro --}}
@component('components.modal', ['id' => 'modalVerLibro', 'title' => 'Detalle del Material', 'subtitle' => 'Información del libro', 'icon' => 'book', 'size' => 'md'])
    <div class="detail-panel">
        <div class="detail-section">
            <h4 class="detail-section-title">Información del material</h4>
            <div class="detail-grid">
                <div class="detail-field">
                    <span class="detail-label">Código</span>
                    <span class="detail-value" id="verCodigoLibro">—</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Estado</span>
                    <span class="detail-value" id="verEstadoLibroBadge"><span class="book-status descontinuado">—</span></span>
                </div>
            </div>
            <div class="detail-grid">
                <div class="detail-field full-width">
                    <span class="detail-label">Nombre / Título</span>
                    <span class="detail-value" id="verNombreLibro">—</span>
                </div>
            </div>
            <div class="detail-grid">
                <div class="detail-field">
                    <span class="detail-label">Tipo de material</span>
                    <span class="detail-value text-capitalize" id="verTipoMaterial">—</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Área</span>
                    <span class="detail-value" id="verArea">—</span>
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h4 class="detail-section-title">Información académica</h4>
            <div class="detail-grid detail-grid-3">
                <div class="detail-field">
                    <span class="detail-label">Nivel</span>
                    <span class="detail-value text-capitalize" id="verNivelLibro">—</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Grado</span>
                    <span class="detail-value" id="verGradoLibro">—</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Año edición</span>
                    <span class="detail-value" id="verAnioEdicion">—</span>
                </div>
            </div>
            <div class="detail-grid">
                <div class="detail-field full-width">
                    <span class="detail-label">Editorial</span>
                    <span class="detail-value" id="verEditorial">—</span>
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h4 class="detail-section-title">Observaciones</h4>
            <div class="detail-grid">
                <div class="detail-field full-width">
                    <span class="detail-value" id="verObservacionesLibro">—</span>
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h4 class="detail-section-title">Inventario</h4>
            <div class="detail-stat-grid">
                <div class="detail-stat-card detail-stat-total">
                    <span class="detail-stat-label">Total</span>
                    <span class="detail-stat-value" id="verCantidadTotal">—</span>
                </div>
                <div class="detail-stat-card detail-stat-disponible">
                    <span class="detail-stat-label">Disponible</span>
                    <span class="detail-stat-value" id="verCantidadDisponible">—</span>
                </div>
                <div class="detail-stat-card detail-stat-entregado">
                    <span class="detail-stat-label">Entregado</span>
                    <span class="detail-stat-value" id="verCantidadEntregada">—</span>
                </div>
                <div class="detail-stat-card detail-stat-devuelto">
                    <span class="detail-stat-label">Devuelto</span>
                    <span class="detail-stat-value" id="verCantidadDevuelta">—</span>
                </div>
            </div>
        </div>
    </div>

    @slot('footer')
        <button type="button" onclick="closeModal('modalVerLibro')" class="btn-app btn-app-secondary">Cerrar</button>
    @endslot
@endcomponent

{{-- Modal Editar Libro --}}
@component('components.modal', ['id' => 'modalEditarLibro', 'title' => 'Editar Material', 'icon' => 'pencil', 'size' => 'xl'])
    <form id="formEditarLibro" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-12 col-md-6">
                <h3 class="h6 fw-bold text-slate-800 text-uppercase pb-2 mb-3" style="border-bottom: 1px solid var(--border-color); letter-spacing: 0.05em;">
                    Información Principal
                </h3>

                <div class="form-group-app">
                    <label class="form-label-app">Código del Libro</label>
                    <input type="text" name="codigo_libro" id="editCodigoLibro" class="form-control-app" readonly>
                </div>

                <div class="form-group-app">
                    <label class="form-label-app">Nombre / Título *</label>
                    <input type="text" name="nombre" id="editNombreLibro" class="form-control-app" required oninput="this.value = this.value.toUpperCase()">
                </div>

                <div class="row g-3">
                    <div class="col-6 form-group-app">
                        <label class="form-label-app">Tipo de Material *</label>
                        <select name="tipo_material" id="editTipoMaterial" class="form-select-app" required>
                            @foreach($tiposMaterial ?? [] as $tipo)
                                <option value="{{ $tipo }}">{{ ucfirst(str_replace('_', ' ', $tipo)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 form-group-app">
                        <label class="form-label-app">Área *</label>
                        <select name="area" id="editAreaLibro" class="form-select-app" required>
                            @foreach($areas ?? [] as $area)
                                <option value="{{ $area }}">{{ $area }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6 form-group-app">
                        <label class="form-label-app">Nivel *</label>
                        <select name="nivel" id="editNivelLibro" class="form-select-app" required>
                            @foreach($niveles ?? [] as $nivel)
                                <option value="{{ $nivel }}">{{ ucfirst($nivel) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 form-group-app">
                        <label class="form-label-app">Grado *</label>
                        <select name="grado" id="editGradoLibro" class="form-select-app" required>
                            @foreach($grados ?? [] as $grado)
                                <option value="{{ $grado }}">{{ $grado }}°</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <h3 class="h6 fw-bold text-slate-800 text-uppercase pb-2 mb-3" style="border-bottom: 1px solid var(--border-color); letter-spacing: 0.05em;">
                    Detalles Adicionales
                </h3>

                <div class="row g-3">
                    <div class="col-6 form-group-app">
                        <label class="form-label-app">Editorial</label>
                        <select name="editorial" id="editEditorial" class="form-select-app">
                            <option value="">Ninguna</option>
                            @foreach($editoriales ?? [] as $editorial)
                                <option value="{{ $editorial }}">{{ $editorial }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 form-group-app">
                        <label class="form-label-app">Año Edición</label>
                        <input type="number" name="anio_edicion" id="editAnioEdicion" class="form-control-app" min="1900" max="{{ date('Y')+1 }}">
                    </div>
                </div>

                <div class="form-group-app">
                    <label class="form-label-app">Año Escolar (ID)</label>
                    <select name="id_anio_escolar" id="editIdAnioEscolar" class="form-select-app">
                        <option value="">Ninguno</option>
                        @foreach($aniosEscolares as $anio)
                            <option value="{{ $anio->id }}">{{ $anio->anio }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-6 form-group-app">
                        <label class="form-label-app">Cantidad Total *</label>
                        <input type="number" name="cantidad_total" id="editCantidadTotal" class="form-control-app" min="0" required>
                    </div>
                    <div class="col-6 form-group-app">
                        <label class="form-label-app">Estado *</label>
                        <select name="estado" id="editEstadoLibro" class="form-select-app" required>
                            <option value="activo">Activo</option>
                            <option value="agotado">Agotado</option>
                            <option value="descontinuado">Descontinuado</option>
                            <option value="en_revision">En Revisión</option>
                        </select>
                    </div>
                </div>

                <div class="form-group-app">
                    <label class="form-label-app">Observaciones</label>
                    <textarea name="observaciones" id="editObservacionesLibro" rows="3" class="form-control-app"></textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('modalEditarLibro')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">
                <i data-lucide="save" class="icon"></i> Guardar Cambios
            </button>
        </div>
    </form>
@endcomponent

{{-- =============== MODAL NUEVO LIBRO =============== --}}
<x-modal id="modalNuevoLibro" title="Registrar Nuevo Material" size="xl">
    <form id="formNuevoLibro" class="js-form-crear-libro" data-action="{{ route('libros.api.store') }}">
        @include('libros.form', ['modo' => 'crear'])
    </form>

    <x-slot:footer>
        <button type="button" onclick="closeModal('modalNuevoLibro')" class="btn-app btn-app-secondary">
            Cancelar
        </button>
        <button type="button" id="btnGuardarLibro" class="btn-app btn-app-primary btn-submit-modal">
            <i data-lucide="save" class="icon"></i> Guardar Libro
        </button>
    </x-slot:footer>
</x-modal>

@push('scripts')
<script>
    function verLibro(libro) {
        document.getElementById('verCodigoLibro').textContent = libro.codigo_libro ?? '—';
        document.getElementById('verNombreLibro').textContent = libro.nombre ?? '—';
        document.getElementById('verTipoMaterial').textContent = (libro.tipo_material ?? '').replace(/_/g, ' ');
        document.getElementById('verArea').textContent = libro.area ?? '—';
        document.getElementById('verNivelLibro').textContent = libro.nivel ?? '—';
        document.getElementById('verEditorial').textContent = libro.editorial ?? '—';
        document.getElementById('verAnioEdicion').textContent = libro.anio_edicion ?? '—';
        document.getElementById('verObservacionesLibro').textContent = libro.observaciones ?? '—';
        document.getElementById('verGradoLibro').textContent = libro.grado ? (libro.grado + '°') : '—';

        const estadoLibroBadge = document.getElementById('verEstadoLibroBadge');
        const estadoLibroText = (libro.estado ?? '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) || '—';
        let estadoLibroClass = 'descontinuado';
        if (libro.estado === 'activo') {
            estadoLibroClass = 'activo';
        } else if (libro.estado === 'agotado') {
            estadoLibroClass = 'agotado';
        } else if (libro.estado === 'en_revision') {
            estadoLibroClass = 'en_revision';
        }
        estadoLibroBadge.innerHTML = '<span class="book-status ' + estadoLibroClass + '">' + estadoLibroText + '</span>';

        document.getElementById('verCantidadTotal').textContent = libro.cantidad_total ?? 0;
        document.getElementById('verCantidadDisponible').textContent = libro.cantidad_disponible ?? 0;
        document.getElementById('verCantidadEntregada').textContent = libro.cantidad_entregada ?? 0;
        document.getElementById('verCantidadDevuelta').textContent = libro.cantidad_devuelta ?? 0;
        openModal('modalVerLibro');
    }

    function setSelectValueWithFallback(selectId, value, text) {
        const select = document.getElementById(selectId);
        if (! select) {
            return;
        }

        const strValue = String(value ?? '');

        if (strValue === '') {
            select.value = '';
            return;
        }

        const exists = Array.from(select.options).some(option => option.value === strValue);

        if (! exists) {
            const option = document.createElement('option');
            option.value = strValue;
            option.textContent = text || strValue;
            select.appendChild(option);
        }

        select.value = strValue;
    }

    function editarLibro(libro) {
        document.getElementById('formEditarLibro').action = `/libros/${libro.id}`;
        document.getElementById('editCodigoLibro').value = libro.codigo_libro ?? '';
        document.getElementById('editNombreLibro').value = libro.nombre ?? '';

        setSelectValueWithFallback('editTipoMaterial', libro.tipo_material, (libro.tipo_material ?? '').replace(/_/g, ' '));
        setSelectValueWithFallback('editAreaLibro', libro.area, libro.area);
        setSelectValueWithFallback('editNivelLibro', libro.nivel, libro.nivel);
        setSelectValueWithFallback('editGradoLibro', libro.grado, libro.grado ? (libro.grado + '°') : '');
        setSelectValueWithFallback('editEditorial', libro.editorial, libro.editorial);
        setSelectValueWithFallback('editIdAnioEscolar', libro.id_anio_escolar, null);

        document.getElementById('editAnioEdicion').value = libro.anio_edicion ?? '';
        document.getElementById('editCantidadTotal').value = libro.cantidad_total ?? 0;
        document.getElementById('editEstadoLibro').value = libro.estado ?? 'activo';
        document.getElementById('editObservacionesLibro').value = libro.observaciones ?? '';
        openModal('modalEditarLibro');
    }
</script>
@endpush
@endsection
