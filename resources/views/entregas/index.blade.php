@extends('layouts.app')

@section('title', 'Entregas')
@section('page-title', 'Entrega de libros')

@section('content')
<div class="d-flex flex-column gap-4">
    <div class="card-app p-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
            <div>
                <h1 class="h3 fw-bold text-slate-900 mb-0">Entrega de libros</h1>
                <p class="small text-slate-500 mt-2 mb-0">Asignación de libros por grado con código de entrega único por estudiante.</p>
            </div>
            <button type="button" onclick="openModal('modalNuevaEntrega')" class="btn-app btn-app-primary">
                <i data-lucide="plus" class="icon"></i>
                Nueva Entrega
            </button>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-6 g-3 mt-4">
            <div class="col">
                <div class="card-app p-3 text-center">
                    <p class="small text-slate-500 text-uppercase mb-2" style="letter-spacing: 0.2em;">Estudiantes</p>
                    <p class="h3 fw-semibold text-slate-900 mb-0">{{ $totalEstudiantes ?? 0 }}</p>
                </div>
            </div>
            <div class="col">
                <div class="card-app p-3 text-center">
                    <p class="small text-slate-500 text-uppercase mb-2" style="letter-spacing: 0.2em;">Libros</p>
                    <p class="h3 fw-semibold text-slate-900 mb-0">{{ $totalLibros ?? 0 }}</p>
                </div>
            </div>
            <div class="col">
                <div class="card-app p-3 text-center">
                    <p class="small text-slate-500 text-uppercase mb-2" style="letter-spacing: 0.2em;">Entregas</p>
                    <p class="h3 fw-semibold text-slate-900 mb-0">{{ $totalEntregas ?? 0 }}</p>
                </div>
            </div>
            <div class="col">
                <div class="card-app p-3 text-center">
                    <p class="small text-slate-500 text-uppercase mb-2" style="letter-spacing: 0.2em;">Libros entregados</p>
                    <p class="h3 fw-semibold text-slate-900 mb-0">{{ $librosEntregados ?? 0 }}</p>
                </div>
            </div>
            <div class="col">
                <div class="card-app p-3 text-center">
                    <p class="small text-slate-500 text-uppercase mb-2" style="letter-spacing: 0.2em;">Pendientes</p>
                    <p class="h3 fw-semibold text-slate-900 mb-0">{{ $estudiantesPendientes ?? 0 }}</p>
                </div>
            </div>
            <div class="col">
                <div class="card-app p-3 text-center">
                    <p class="small text-slate-500 text-uppercase mb-2" style="letter-spacing: 0.2em;">Avance</p>
                    <p class="h3 fw-semibold text-slate-900 mb-0">{{ $avance ?? 0 }}%</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros y tabla --}}
    <div class="card-app">
        <div class="card-header-base">
            <div class="d-flex flex-column flex-sm-row align-items-sm-end justify-content-sm-between gap-3 mb-4">
                <div>
                    <h2 class="h5 fw-bold text-slate-900 mb-0">Historial de entregas</h2>
                    <p class="small text-slate-500 mt-1 mb-0">Busca y filtra las entregas registradas.</p>
                </div>
                <span class="badge bg-light text-dark border">{{ $entregas->total() }} registros</span>
            </div>

            <form id="filtroEntregas" method="GET" action="{{ route('entregas.index') }}">
                <div class="row g-3">
                    <div class="col-lg-5 position-relative">
                        <i data-lucide="search" class="icon position-absolute text-slate-400" style="left: 1rem; top: 50%; transform: translateY(-50%);"></i>
                        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="DNI, nombres, apellidos, código..." class="form-control-app ps-5" />
                    </div>
                    <div class="col-lg-2">
                        <select name="grado" class="form-select-app">
                            <option value="">Todos los grados</option>
                            @foreach($gradoOptions as $grado)
                                <option value="{{ $grado }}" @selected(request('grado') == $grado)>{{ $grado }}° Grado</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <select name="seccion" class="form-select-app">
                            <option value="">Todas las secciones</option>
                            @foreach($seccionOptions as $seccion)
                                <option value="{{ $seccion }}" @selected(request('seccion') == $seccion)>Sección {{ $seccion }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <select name="anio_escolar" class="form-select-app">
                            <option value="">Todos los años</option>
                            @foreach($anioEscolarOptions as $id => $anio)
                                <option value="{{ $id }}" @selected(request('anio_escolar') == $id)>Año {{ $anio }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn-app btn-app-primary w-100">
                            <i data-lucide="filter" class="icon"></i> Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive-app">
            <table class="table table-app">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>DNI</th>
                        <th>Estudiante</th>
                        <th>Grado</th>
                        <th>Sec.</th>
                        <th class="text-end">Libros</th>
                        <th>Fecha</th>
                        <th>Responsable</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-entregas-body">
                    @forelse($entregas as $entrega)
                        <tr>
                            <td class="fw-medium">{{ $entrega->codigo_general }}</td>
                            <td>{{ $entrega->estudiante->dni ?? '—' }}</td>
                            <td>{{ $entrega->estudiante->apellidos ?? '' }}, {{ $entrega->estudiante->nombres ?? '' }}</td>
                            <td>{{ $entrega->estudiante->grado ?? '—' }}°</td>
                            <td>{{ $entrega->estudiante->seccion ?? '—' }}</td>
                            <td class="text-end">{{ $entrega->total_libros }}</td>
                            <td>{{ optional($entrega->fecha_entrega)->format('d/m/Y') }}</td>
                            <td>
                                @if($entrega->tipo_firmante === 'apoderado' && $entrega->padre)
                                    <span class="d-block fw-medium text-slate-800">{{ $entrega->padre->apellidos }}, {{ $entrega->padre->nombres }}</span>
                                    <span class="small text-slate-500">Apoderado</span>
                                @elseif($entrega->tipo_firmante === 'estudiante')
                                    <span class="d-block fw-medium text-slate-800">{{ $entrega->estudiante->apellidos ?? '' }}, {{ $entrega->estudiante->nombres ?? '' }}</span>
                                    <span class="small text-slate-500">Estudiante</span>
                                @else
                                    {{ $entrega->usuarioRegistro->nombres ?? '—' }}
                                @endif
                            </td>
                            <td>
                                @if($entrega->estado === 'completada')
                                    <span class="badge bg-success-subtle text-success">Entregado</span>
                                @else
                                    <span class="badge bg-light text-dark border">{{ ucfirst($entrega->estado) }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn-action btn-action-primary" title="Ver detalle" onclick='verEntrega(@json($entrega))'>
                                    <i data-lucide="eye" class="icon"></i>
                                </button>
                                <button type="button" class="btn-action btn-action-warning" title="Editar" onclick='editarEntrega(@json($entrega))'>
                                    <i data-lucide="pencil" class="icon"></i>
                                </button>
                                <button type="button" class="btn-action btn-action-success" title="Ver constancia" onclick='verConstanciaEntrega({{ $entrega->id }}, "{{ $entrega->codigo_general }}")'>
                                    <i data-lucide="file-text" class="icon"></i>
                                </button>
                                <a href="{{ route('entregas.constancia', $entrega) }}" class="btn-action btn-action-secondary" title="Descargar PDF">
                                    <i data-lucide="download" class="icon"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">Sin entregas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($entregas->hasPages())
            <div class="card-footer-base d-flex justify-content-center">
                {{ $entregas->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Ver Detalle de Entrega --}}
@component('components.modal', ['id' => 'modalVerEntrega', 'title' => 'Constancia de Entrega', 'size' => 'xl'])
    <div class="d-flex align-items-center justify-content-between mb-3 pb-2" style="border-bottom: 2px solid #1e3a5f;">
        <div>
            <p class="small text-uppercase fw-bold text-slate-500 mb-1" style="letter-spacing: 0.1em;">Código de constancia</p>
            <p class="h4 fw-bold text-slate-900 mb-0" id="detCodigo">—</p>
        </div>
        <div id="detEstadoBadge"></div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card-app p-3 bg-slate-50 h-100">
                <h4 class="h6 fw-bold text-slate-800 text-uppercase pb-2 mb-3" style="border-bottom: 1px solid var(--border-color); letter-spacing: 0.05em;">
                    Datos del Estudiante
                </h4>
                <div class="d-flex flex-column gap-3 small">
                    <div>
                        <p class="small text-slate-500 mb-0">Apellidos y nombres</p>
                        <p class="fw-semibold text-slate-800 mb-0" id="detNombre">—</p>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <p class="small text-slate-500 mb-0">DNI</p>
                            <p class="fw-semibold text-slate-800 mb-0" id="detDni">—</p>
                        </div>
                        <div class="col-6">
                            <p class="small text-slate-500 mb-0">Código</p>
                            <p class="fw-semibold text-slate-800 mb-0" id="detCodigoEst">—</p>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <p class="small text-slate-500 mb-0">Grado</p>
                            <p class="fw-semibold text-slate-800 mb-0" id="detGrado">—</p>
                        </div>
                        <div class="col-6">
                            <p class="small text-slate-500 mb-0">Sección</p>
                            <p class="fw-semibold text-slate-800 mb-0" id="detSeccion">—</p>
                        </div>
                    </div>
                    <div>
                        <p class="small text-slate-500 mb-0">Nivel</p>
                        <p class="fw-semibold text-slate-800 mb-0 text-capitalize" id="detNivel">—</p>
                    </div>
                </div>

                <h4 class="h6 fw-bold text-slate-800 text-uppercase pb-2 mb-3 mt-4" style="border-bottom: 1px solid var(--border-color); letter-spacing: 0.05em;">
                    Datos de la Entrega
                </h4>
                <div class="d-flex flex-column gap-3 small">
                    <div class="row g-2">
                        <div class="col-6">
                            <p class="small text-slate-500 mb-0">Fecha</p>
                            <p class="fw-semibold text-slate-800 mb-0" id="detFecha">—</p>
                        </div>
                        <div class="col-6">
                            <p class="small text-slate-500 mb-0">Hora</p>
                            <p class="fw-semibold text-slate-800 mb-0" id="detHora">—</p>
                        </div>
                    </div>
                    <div>
                        <p class="small text-slate-500 mb-0">Año escolar</p>
                        <p class="fw-semibold text-slate-800 mb-0" id="detAnio">—</p>
                    </div>
                    <div>
                        <p class="small text-slate-500 mb-0">Responsable de registro</p>
                        <p class="fw-semibold text-slate-800 mb-0" id="detResponsable">—</p>
                    </div>
                    <div>
                        <p class="small text-slate-500 mb-0">Recibido por</p>
                        <p class="fw-semibold text-slate-800 mb-0" id="detRecibido">—</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card-app p-3 h-100">
                <h4 class="h6 fw-bold text-slate-800 text-uppercase pb-2 mb-3" style="border-bottom: 1px solid var(--border-color); letter-spacing: 0.05em;">
                    Relación de Textos Entregados
                </h4>
                <div class="table-responsive-app">
                    <table class="table table-sm table-app mb-0">
                        <thead>
                            <tr>
                                <th style="width: 8%;">N°</th>
                                <th style="width: 22%;">Código</th>
                                <th>Título</th>
                                <th style="width: 18%;" class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody id="detTablaLibros"></tbody>
                    </table>
                </div>
                <div id="detObservacionesBox" class="mt-3 p-3 bg-warning-subtle rounded d-none">
                    <p class="small fw-bold text-warning-emphasis text-uppercase mb-1">Observaciones:</p>
                    <p class="small text-warning-emphasis mb-0" id="detObservaciones"></p>
                </div>
            </div>
        </div>
    </div>

    @slot('footer')
        <button type="button" onclick="closeModal('modalVerEntrega')" class="btn-app btn-app-secondary">Cerrar</button>
        <a href="#" id="detBtnDescargar" class="btn-app btn-app-primary">
            <i data-lucide="download" class="icon"></i> Descargar constancia
        </a>
    @endslot
@endcomponent

{{-- Modal Editar Entrega --}}
@component('components.modal', ['id' => 'modalEditarEntrega', 'title' => 'Editar Entrega', 'size' => 'lg'])
    <form id="formEditarEntrega" method="POST" data-action="">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label-app">Estado <span class="text-danger">*</span></label>
                <select name="estado" id="editEntregaEstado" class="form-select-app" required>
                    <option value="completada">Completada</option>
                    <option value="parcial">Parcial</option>
                    <option value="anulada">Anulada</option>
                </select>
                <small class="form-text-app text-slate-500 mt-1 d-block">
                    <span id="editEntregaEstadoHelp" class="small"></span>
                </small>
            </div>
            <div class="col-md-6">
                <label class="form-label-app">Fecha de entrega</label>
                <input type="text" id="editEntregaFecha" class="form-control-app" disabled>
            </div>
            <div class="col-12">
                <label class="form-label-app">Observaciones</label>
                <textarea name="observaciones" id="editEntregaObservaciones" rows="2" class="form-control-app" placeholder="Motivo del cambio de estado (opcional)"></textarea>
            </div>
            <div class="col-12">
                <label class="form-label-app d-flex justify-content-between align-items-center">
                    <span>Libros entregados</span>
                    <span class="small text-slate-500" id="editEntregaContadorLibros">0 libro(s)</span>
                </label>
                <div id="editEntregaLibrosContainer" class="d-flex flex-wrap gap-2 mb-2"></div>
                <div id="editEntregaLibrosInputs"></div>
                <div class="search-wrapper" id="editEntregaBuscarWrapper">
                    <div class="position-relative">
                        <i data-lucide="search" class="icon position-absolute text-slate-400" style="left: 0.75rem; top: 50%; transform: translateY(-50%);"></i>
                        <input type="text" id="editEntregaBuscarLibro" class="form-control-app ps-4" placeholder="Buscar libro por nombre, código o área..." autocomplete="off">
                    </div>
                    <div id="editEntregaResultadosLibro" class="search-results d-none"></div>
                </div>
                <small class="form-text-app text-slate-500 mt-1 d-block">Puede quitar libros actuales o agregar nuevos desde el inventario.</small>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 mt-3 border-top">
            <button type="button" onclick="closeModal('modalEditarEntrega')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="button" id="btnGuardarEditarEntrega" onclick="guardarEditarEntrega()" class="btn-app btn-app-primary btn-submit-modal">
                <i data-lucide="save" class="icon"></i> Guardar cambios
            </button>
        </div>
    </form>
@endcomponent

{{-- Modal Ver Constancia (PDF en iframe) --}}
@component('components.modal', ['id' => 'modalVerConstanciaEntrega', 'title' => 'Constancia de Entrega', 'size' => 'full'])
    <div style="height: 75vh; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
        <iframe id="iframeConstanciaEntrega" src="" style="width: 100%; height: 100%; border: 0;" title="Constancia PDF"></iframe>
    </div>
    @slot('footer')
        <button type="button" onclick="closeModal('modalVerConstanciaEntrega')" class="btn-app btn-app-secondary">Cerrar</button>
        <a href="#" id="btnDescargarConstanciaModal" target="_blank" class="btn-app btn-app-primary">
            <i data-lucide="download" class="icon"></i> Descargar PDF
        </a>
    @endslot
@endcomponent

{{-- =============== MODAL NUEVA ENTREGA =============== --}}
<div id="modalNuevaEntrega" class="modal-app modal-wide hidden" role="dialog" aria-modal="true">
    <div class="modal-overlay" onclick="closeModal('modalNuevaEntrega')"></div>
    <div class="modal-panel">
        <div class="modal-header">
            <h3 class="modal-title d-flex align-items-center gap-2">
                <i data-lucide="clipboard-list" class="icon text-primary"></i>
                Registrar Nueva Entrega
            </h3>
            <button type="button" onclick="closeModal('modalNuevaEntrega')" class="modal-close" aria-label="Cerrar">
                <i data-lucide="x" class="icon"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="formNuevaEntrega" class="d-flex flex-column gap-4"
                  data-action="{{ route('entregas.api.store') }}"
                  data-buscar-url="{{ route('entregas.api.buscar-estudiantes') }}"
                  data-libros-url="{{ route('entregas.api.libros-por-grado') }}">
                {{-- Paso 1: Buscar Estudiante --}}
                <div>
                    <h4 class="section-title">1. Seleccionar Estudiante</h4>
                    <div class="search-wrapper" id="entrega-buscar-wrapper">
                        <div class="position-relative">
                            <i data-lucide="search" class="icon position-absolute text-slate-400" style="left: 0.75rem; top: 50%; transform: translateY(-50%);"></i>
                            <input type="text" id="modal_buscar_est" class="form-control-app ps-4" placeholder="Buscar por DNI, nombres o apellidos..." autocomplete="off">
                        </div>
                        <div id="modal_resultados_est" class="search-results d-none"></div>
                    </div>
                    <input type="hidden" name="id_estudiante" id="modal_id_estudiante">
                    <div id="modal_est_info" class="info-box d-none">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <p class="info-label">Estudiante seleccionado:</p>
                                <p class="info-name" id="modal_lbl_nombre"></p>
                                <div class="d-flex gap-4 info-meta">
                                    <span>DNI: <strong id="modal_lbl_dni"></strong></span>
                                    <span>Grado: <strong id="modal_lbl_grado"></strong></span>
                                    <span>Nivel: <strong id="modal_lbl_nivel"></strong></span>
                                </div>
                            </div>
                            <button type="button" id="modal_btn_cambiar_est" class="btn btn-sm btn-outline-primary">Cambiar</button>
                        </div>
                        <div id="modal_alerta_entrega" class="alert alert-warning d-none mt-3 d-flex align-items-start gap-2 py-2 small">
                            <i data-lucide="alert-triangle" class="icon flex-shrink-0 mt-1"></i>
                            <span><strong>¡Atención!</strong> Este estudiante ya tiene una entrega registrada.</span>
                        </div>
                    </div>
                </div>

                {{-- Paso 2: Libros --}}
                <div id="modal_sec_libros" class="d-none">
                    <h4 class="section-title d-flex align-items-center justify-content-between">
                        <span>2. Libros Asignados</span>
                        <span class="badge-counter" id="modal_lbl_total_libros">0 libros</span>
                    </h4>
                    <div id="modal_loading_libros" class="d-none text-center py-4 text-muted small">
                        <i data-lucide="loader-2" class="icon spin mb-2"></i>
                        <div>Cargando libros...</div>
                    </div>
                    <div id="modal_lista_libros" class="d-flex flex-column gap-2" style="max-height: 12rem; overflow-y: auto;"></div>
                </div>

                {{-- Paso 3: Recepción --}}
                <div id="modal_sec_recepcion" class="d-none">
                    <h4 class="section-title">3. ¿Quién recibe?</h4>
                    <div class="radio-group mb-3">
                        <label class="radio-card">
                            <input type="radio" name="tipo_firmante" value="estudiante" class="radio-card-input" checked>
                            <div class="radio-card-body">
                                <div class="radio-card-icon">
                                    <i data-lucide="user" class="icon"></i>
                                </div>
                                <span class="radio-card-text">Estudiante</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="tipo_firmante" value="apoderado" class="radio-card-input">
                            <div class="radio-card-body">
                                <div class="radio-card-icon">
                                    <i data-lucide="users" class="icon"></i>
                                </div>
                                <span class="radio-card-text">Apoderado</span>
                            </div>
                        </label>
                    </div>
                    <div id="modal_form_apoderado" class="d-none card-app p-3 mb-3 bg-slate-50">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-app">DNI *</label>
                                <input type="text" name="apoderado_dni" class="form-control-app">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-app">Parentesco *</label>
                                <select name="apoderado_parentesco" class="form-select-app">
                                    <option value="">Seleccione...</option>
                                    <option value="Madre">Madre</option>
                                    <option value="Padre">Padre</option>
                                    <option value="Tutor">Tutor/a</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-app">Nombres *</label>
                                <input type="text" name="apoderado_nombres" class="form-control-app">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-app">Apellidos *</label>
                                <input type="text" name="apoderado_apellidos" class="form-control-app">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label-app">Observaciones</label>
                        <textarea name="observaciones" rows="2" class="form-control-app" placeholder="Opcional"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('modalNuevaEntrega')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="button" id="modal_btn_guardar_entrega" class="btn-app btn-app-primary btn-submit-modal" disabled>
                <i data-lucide="check-circle" class="icon"></i> Registrar Entrega
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function verEntrega(entrega) {
        // Encabezado
        document.getElementById('detCodigo').textContent = entrega.codigo_general;
        const estadoBadge = document.getElementById('detEstadoBadge');
        estadoBadge.innerHTML = entrega.estado === 'completada'
            ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">COMPLETADA</span>'
            : '<span class="badge bg-light text-dark border px-3 py-2">' + (entrega.estado ?? 'PENDIENTE').toUpperCase() + '</span>';

        // Estudiante
        document.getElementById('detNombre').textContent = (entrega.estudiante?.apellidos ?? '') + ', ' + (entrega.estudiante?.nombres ?? '');
        document.getElementById('detDni').textContent = entrega.estudiante?.dni ?? '—';
        document.getElementById('detCodigoEst').textContent = entrega.estudiante?.codigo_estudiante ?? '—';
        document.getElementById('detGrado').textContent = (entrega.estudiante?.grado ?? '—') + '°';
        document.getElementById('detSeccion').textContent = entrega.estudiante?.seccion ?? '—';
        document.getElementById('detNivel').textContent = entrega.estudiante?.nivel ?? '—';

        // Entrega
        const fecha = entrega.fecha_entrega ? new Date(entrega.fecha_entrega).toLocaleDateString('es-PE') : '—';
        document.getElementById('detFecha').textContent = fecha;
        document.getElementById('detHora').textContent = entrega.hora_entrega ?? '—';
        document.getElementById('detAnio').textContent = entrega.anio_escolar?.anio ?? '—';
        document.getElementById('detResponsable').textContent = entrega.usuario_registro?.nombres ?? '—';

        const recibido = entrega.tipo_firmante === 'apoderado' && entrega.padre
            ? entrega.padre.nombre_completo + ' (Apoderado)'
            : 'El propio estudiante';
        document.getElementById('detRecibido').textContent = recibido;

        // Tabla de libros
        const tbody = document.getElementById('detTablaLibros');
        tbody.innerHTML = '';
        if (entrega.detalles && entrega.detalles.length > 0) {
            entrega.detalles.forEach((d, i) => {
                const estado = d.estado_entrega ?? 'entregado';
                const estadoClass = estado === 'bueno' || estado === 'entregado' ? 'bg-success-subtle text-success' : 'bg-light text-dark border';
                tbody.innerHTML += `
                    <tr>
                        <td>${i + 1}</td>
                        <td class="fw-medium">${d.libro?.codigo_libro ?? '—'}</td>
                        <td>${d.libro?.nombre ?? 'Libro no encontrado'}</td>
                        <td class="text-center"><span class="badge ${estadoClass}">${estado.charAt(0).toUpperCase() + estado.slice(1)}</span></td>
                    </tr>
                `;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No se registraron libros.</td></tr>';
        }

        // Observaciones
        const obsBox = document.getElementById('detObservacionesBox');
        if (entrega.observaciones) {
            obsBox.classList.remove('d-none');
            document.getElementById('detObservaciones').textContent = entrega.observaciones;
        } else {
            obsBox.classList.add('d-none');
        }

        // Botón descargar
        document.getElementById('detBtnDescargar').href = `/entregas/${entrega.id}/constancia`;

        openModal('modalVerEntrega');
    }

    // ===================== EDITAR ENTREGA: GESTIÓN DE LIBROS =====================
    let librosEditEntrega = [];
    let timeoutBuscarLibro = null;
    const buscarLibrosUrl = '{{ route('libros.api.buscar') }}';

    function editarEntrega(entrega) {
        const form = document.getElementById('formEditarEntrega');
        const url = `/entregas/${entrega.id}/api/update`;
        form.action = url;
        form.setAttribute('data-action', url);

        document.getElementById('editEntregaEstado').value = entrega.estado ?? 'completada';
        document.getElementById('editEntregaFecha').value = entrega.fecha_entrega
            ? new Date(entrega.fecha_entrega).toLocaleDateString('es-PE') + ' ' + (entrega.hora_entrega ?? '')
            : '—';
        document.getElementById('editEntregaObservaciones').value = entrega.observaciones ?? '';

        // Inicializar libros desde detalles existentes
        librosEditEntrega = (entrega.detalles || []).map(d => ({
            id: d.id_libro,
            nombre: d.libro?.nombre ?? 'Libro',
            codigo: d.libro?.codigo_libro ?? ''
        }));
        renderLibrosEditEntrega();

        actualizarAyudaEstado(entrega.estado ?? 'completada');
        openModal('modalEditarEntrega');
    }

    function renderLibrosEditEntrega() {
        const container = document.getElementById('editEntregaLibrosContainer');
        const inputs = document.getElementById('editEntregaLibrosInputs');
        const contador = document.getElementById('editEntregaContadorLibros');

        container.innerHTML = '';
        inputs.innerHTML = '';

        if (librosEditEntrega.length === 0) {
            container.innerHTML = '<span class="text-muted small">No hay libros seleccionados.</span>';
        } else {
            librosEditEntrega.forEach((libro, index) => {
                const chip = document.createElement('div');
                chip.className = 'd-inline-flex align-items-center gap-1 px-2 py-1 bg-primary-subtle text-primary-emphasis rounded small border border-primary-subtle';
                chip.innerHTML = `
                    <span class="fw-medium">${libro.codigo ? libro.codigo + ' - ' : ''}${libro.nombre}</span>
                    <button type="button" onclick="quitarLibroEditEntrega(${index})" class="btn btn-link btn-sm p-0 ms-1 text-primary" title="Quitar">
                        <i data-lucide="x" class="icon" style="width:14px;height:14px;"></i>
                    </button>
                `;
                container.appendChild(chip);

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'libros[]';
                input.value = libro.id;
                inputs.appendChild(input);
            });
        }

        if (contador) {
            contador.textContent = `${librosEditEntrega.length} libro(s)`;
        }

        window.initLucide && window.initLucide();
    }

    function quitarLibroEditEntrega(index) {
        librosEditEntrega.splice(index, 1);
        renderLibrosEditEntrega();
    }

    // ========== Buscador de libros para editar entrega ==========
    const mBuscarLibro = document.getElementById('editEntregaBuscarLibro');
    const mResultadosLibro = document.getElementById('editEntregaResultadosLibro');

    if (mBuscarLibro) {
        mBuscarLibro.addEventListener('input', function() {
            const q = this.value.trim();
            clearTimeout(timeoutBuscarLibro);
            if (q.length < 2) { mResultadosLibro.classList.add('d-none'); return; }
            timeoutBuscarLibro = setTimeout(() => {
                fetch(`${buscarLibrosUrl}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => {
                        mResultadosLibro.innerHTML = '';
                        if (data.length === 0) {
                            mResultadosLibro.innerHTML = '<div class="result-empty">No se encontraron libros.</div>';
                        } else {
                            data.forEach(libro => {
                                const div = document.createElement('div');
                                div.className = 'result-item';
                                div.innerHTML = `<div class="result-title">${libro.codigo_libro} - ${libro.nombre}</div><div class="result-meta">${libro.area} | ${libro.grado}° ${libro.nivel || ''} | Disp: ${libro.cantidad_disponible}</div>`;
                                div.addEventListener('click', () => seleccionarLibroEditEntrega(libro));
                                mResultadosLibro.appendChild(div);
                            });
                        }
                        mResultadosLibro.classList.remove('d-none');
                    }).catch(() => {
                        mResultadosLibro.innerHTML = '<div class="result-empty text-danger">Error al buscar.</div>';
                        mResultadosLibro.classList.remove('d-none');
                    });
            }, 300);
        });
    }

    document.addEventListener('click', (e) => {
        if (mBuscarLibro && !mBuscarLibro.contains(e.target) && mResultadosLibro && !mResultadosLibro.contains(e.target)) {
            mResultadosLibro.classList.add('d-none');
        }
    });

    function seleccionarLibroEditEntrega(libro) {
        mResultadosLibro.classList.add('d-none');
        mBuscarLibro.value = '';

        if (librosEditEntrega.some(l => l.id === libro.id)) {
            window.showToast('Este libro ya está en la lista.', 'warning');
            return;
        }

        librosEditEntrega.push({
            id: libro.id,
            nombre: libro.nombre,
            codigo: libro.codigo_libro
        });
        renderLibrosEditEntrega();
    }

    function actualizarAyudaEstado(estado) {
        const help = document.getElementById('editEntregaEstadoHelp');
        const mensajes = {
            completada: 'La entrega está completa y activa.',
            parcial: 'Solo algunos libros fueron entregados.',
            anulada: 'La entrega será anulada y los libros volverán al inventario.',
        };
        if (help) help.textContent = mensajes[estado] ?? '';
    }

    document.getElementById('editEntregaEstado')?.addEventListener('change', function() {
        actualizarAyudaEstado(this.value);
    });

    function guardarEditarEntrega() {
        const form = document.getElementById('formEditarEntrega');
        const url = form?.getAttribute('data-action');
        
        if (!form || !url) {
            window.showToast('Error: URL no configurada. Recargue la página (Ctrl+F5).', 'error');
            return;
        }

        if (librosEditEntrega.length === 0) {
            window.showToast('Debe seleccionar al menos un libro.', 'warning');
            return;
        }

        const btn = document.getElementById('btnGuardarEditarEntrega');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';

        const formData = new FormData(form);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(async (response) => {
            const text = await response.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Respuesta completa del servidor:', text);
                throw new Error('El servidor no devolvió JSON. Ver consola (F12) para detalles.');
            }
        })
        .then(data => {
            if (data.success) {
                localStorage.setItem('app_toast', JSON.stringify({
                    message: data.message || 'Entrega actualizada correctamente.',
                    type: 'success'
                }));
                location.reload();
            } else {
                window.showToast(data.message || 'No se pudo guardar.', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        })
        .catch(err => {
            console.error('Error completo:', err);
            window.showToast('Error al guardar: ' + err.message, 'error');
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        });
    }

    function verConstanciaEntrega(id, codigo) {
        document.getElementById('iframeConstanciaEntrega').src = `/entregas/${id}/ver-constancia`;
        document.getElementById('btnDescargarConstanciaModal').href = `/entregas/${id}/constancia`;
        openModal('modalVerConstanciaEntrega');
    }
</script>
@endpush
@endsection
