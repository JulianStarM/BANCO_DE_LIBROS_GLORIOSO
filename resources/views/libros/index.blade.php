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
        <button type="button" onclick="openModal('modalNuevoLibro')" class="btn-app btn-app-primary">
            <i data-lucide="plus" class="icon"></i>
            Nuevo Libro
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="card-app p-4 mb-4">
    <form method="GET" action="{{ route('libros.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label-app">Buscar</label>
                <div class="position-relative">
                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400">
                        <i data-lucide="search" class="icon"></i>
                    </span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control-app ps-5" placeholder="Nombre, código o área...">
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label-app">Área</label>
                <select name="area" class="form-select-app">
                    <option value="">Todas las áreas</option>
                    @foreach($areas as $area)
                        <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>{{ $area }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label-app">Grado</label>
                <select name="grado" class="form-select-app">
                    <option value="">Todos los grados</option>
                    @for($i=1; $i<=6; $i++)
                        <option value="{{ $i }}" {{ request('grado') == $i ? 'selected' : '' }}>{{ $i }}° Grado</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 d-flex gap-2">
                <button type="submit" class="btn-app btn-app-light flex-fill">
                    Filtrar
                </button>
                @if(request()->anyFilled(['buscar', 'area', 'grado']))
                    <a href="{{ route('libros.index') }}" class="btn-app btn-app-icon btn-app-light" title="Limpiar filtros">
                        <i data-lucide="x" class="icon"></i>
                    </a>
                @endif
            </div>
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
@component('components.modal', ['id' => 'modalVerLibro', 'title' => 'Detalle del Material', 'size' => 'md'])
    <div class="row g-3">
        <div class="col-md-6">
            <p class="small text-slate-500 mb-0">Código</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verCodigoLibro">—</p>
        </div>
        <div class="col-md-6">
            <p class="small text-slate-500 mb-0">Estado</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verEstadoLibro">—</p>
        </div>
        <div class="col-12">
            <p class="small text-slate-500 mb-0">Nombre / Título</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verNombreLibro">—</p>
        </div>
        <div class="col-md-6">
            <p class="small text-slate-500 mb-0">Tipo de material</p>
            <p class="fw-semibold text-slate-800 mb-0 text-capitalize" id="verTipoMaterial">—</p>
        </div>
        <div class="col-md-6">
            <p class="small text-slate-500 mb-0">Área</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verArea">—</p>
        </div>
        <div class="col-md-4">
            <p class="small text-slate-500 mb-0">Nivel</p>
            <p class="fw-semibold text-slate-800 mb-0 text-capitalize" id="verNivelLibro">—</p>
        </div>
        <div class="col-md-4">
            <p class="small text-slate-500 mb-0">Grado</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verGradoLibro">—</p>
        </div>
        <div class="col-md-4">
            <p class="small text-slate-500 mb-0">Año edición</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verAnioEdicion">—</p>
        </div>
        <div class="col-md-6">
            <p class="small text-slate-500 mb-0">Editorial</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verEditorial">—</p>
        </div>
        <div class="col-md-6">
            <p class="small text-slate-500 mb-0">Observaciones</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verObservacionesLibro">—</p>
        </div>
        <div class="col-12">
            <div class="card-app p-3 bg-slate-50">
                <p class="small fw-semibold text-slate-700 mb-2">Inventario</p>
                <div class="row g-2 small">
                    <div class="col-6 col-md-3">
                        <span class="text-slate-500">Total:</span>
                        <span class="fw-semibold text-slate-800" id="verCantidadTotal">—</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="text-slate-500">Disponible:</span>
                        <span class="fw-semibold text-slate-800" id="verCantidadDisponible">—</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="text-slate-500">Entregado:</span>
                        <span class="fw-semibold text-slate-800" id="verCantidadEntregada">—</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <span class="text-slate-500">Devuelto:</span>
                        <span class="fw-semibold text-slate-800" id="verCantidadDevuelta">—</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endcomponent

{{-- Modal Editar Libro --}}
@component('components.modal', ['id' => 'modalEditarLibro', 'title' => 'Editar Material', 'size' => 'xl'])
    <form id="formEditarLibro" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-12 col-md-6">
                <h3 class="h6 fw-bold text-slate-800 text-uppercase pb-2 mb-3" style="border-bottom: 1px solid var(--border-color); letter-spacing: 0.05em;">
                    Información Principal
                </h3>

                <div class="form-group-app">
                    <label class="form-label-app">Código del Libro *</label>
                    <input type="text" name="codigo_libro" id="editCodigoLibro" class="form-control-app" required>
                </div>

                <div class="form-group-app">
                    <label class="form-label-app">Nombre / Título *</label>
                    <input type="text" name="nombre" id="editNombreLibro" class="form-control-app" required>
                </div>

                <div class="row g-3">
                    <div class="col-6 form-group-app">
                        <label class="form-label-app">Tipo de Material *</label>
                        <select name="tipo_material" id="editTipoMaterial" class="form-select-app" required>
                            <option value="libro">Libro</option>
                            <option value="cuaderno_trabajo">Cuaderno de Trabajo</option>
                            <option value="guia_docente">Guía Docente</option>
                            <option value="material_complementario">Material Complementario</option>
                        </select>
                    </div>
                    <div class="col-6 form-group-app">
                        <label class="form-label-app">Área *</label>
                        <input type="text" name="area" id="editAreaLibro" class="form-control-app" required>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6 form-group-app">
                        <label class="form-label-app">Nivel *</label>
                        <select name="nivel" id="editNivelLibro" class="form-select-app" required>
                            <option value="inicial">Inicial</option>
                            <option value="primaria">Primaria</option>
                            <option value="secundaria">Secundaria</option>
                        </select>
                    </div>
                    <div class="col-6 form-group-app">
                        <label class="form-label-app">Grado *</label>
                        <select name="grado" id="editGradoLibro" class="form-select-app" required>
                            @for($i=1; $i<=6; $i++)
                                <option value="{{ $i }}">{{ $i }}°</option>
                            @endfor
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
                        <input type="text" name="editorial" id="editEditorial" class="form-control-app">
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
    <form id="formNuevoLibro" data-action="{{ route('libros.api.store') }}">
        @include('libros.form')
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
        document.getElementById('verGradoLibro').textContent = (libro.grado ?? '') + '°';
        document.getElementById('verEditorial').textContent = libro.editorial ?? '—';
        document.getElementById('verAnioEdicion').textContent = libro.anio_edicion ?? '—';
        document.getElementById('verObservacionesLibro').textContent = libro.observaciones ?? '—';
        document.getElementById('verEstadoLibro').textContent = (libro.estado ?? '').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        document.getElementById('verCantidadTotal').textContent = libro.cantidad_total ?? 0;
        document.getElementById('verCantidadDisponible').textContent = libro.cantidad_disponible ?? 0;
        document.getElementById('verCantidadEntregada').textContent = libro.cantidad_entregada ?? 0;
        document.getElementById('verCantidadDevuelta').textContent = libro.cantidad_devuelta ?? 0;
        openModal('modalVerLibro');
    }

    function editarLibro(libro) {
        document.getElementById('formEditarLibro').action = `/libros/${libro.id}`;
        document.getElementById('editCodigoLibro').value = libro.codigo_libro ?? '';
        document.getElementById('editNombreLibro').value = libro.nombre ?? '';
        document.getElementById('editTipoMaterial').value = libro.tipo_material ?? 'libro';
        document.getElementById('editAreaLibro').value = libro.area ?? '';
        document.getElementById('editNivelLibro').value = libro.nivel ?? 'primaria';
        document.getElementById('editGradoLibro').value = libro.grado ?? 1;
        document.getElementById('editEditorial').value = libro.editorial ?? '';
        document.getElementById('editAnioEdicion').value = libro.anio_edicion ?? '';
        document.getElementById('editIdAnioEscolar').value = libro.id_anio_escolar ?? '';
        document.getElementById('editCantidadTotal').value = libro.cantidad_total ?? 0;
        document.getElementById('editEstadoLibro').value = libro.estado ?? 'activo';
        document.getElementById('editObservacionesLibro').value = libro.observaciones ?? '';
        openModal('modalEditarLibro');
    }
</script>
@endpush
@endsection
