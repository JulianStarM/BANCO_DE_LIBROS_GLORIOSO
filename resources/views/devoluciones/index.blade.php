@extends('layouts.app')

@section('title', 'Devoluciones')
@section('page-title', 'Devolución de libros')

@section('content')
<div class="d-flex flex-column gap-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
        <div>
            <h1 class="h3 fw-bold text-slate-900 mb-0">Devolución de libros</h1>
            <p class="small text-slate-500 mt-2 mb-0">Registro y control de materiales educativos devueltos por los estudiantes al finalizar el año.</p>
        </div>
        <button type="button" onclick="openModal('modalNuevaDevolucion')" class="btn-app btn-app-primary">
            <i data-lucide="corner-down-left" class="icon"></i>
            Registrar Devolución
        </button>
    </div>

    {{-- Filtros y tabla --}}
    <div class="card-app">
        <div class="card-header-base">
            <form id="filtroDevoluciones" method="GET" action="{{ route('devoluciones.index') }}">
                <div class="row g-3">
                    <div class="col-lg-4 position-relative">
                        <i data-lucide="search" class="icon position-absolute text-slate-400" style="left: 1rem; top: 50%; transform: translateY(-50%);"></i>
                        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="DNI, código, estudiante..." class="form-control-app ps-5" />
                    </div>
                    <div class="col-lg-3">
                        <select name="estado" class="form-select-app">
                            <option value="">Todos los estados</option>
                            <option value="completada" @selected(request('estado') == 'completada')>Completada</option>
                            <option value="parcial" @selected(request('estado') == 'parcial')>Parcial</option>
                            <option value="anulada" @selected(request('estado') == 'anulada')>Anulada</option>
                        </select>
                    </div>
                    <div class="col-lg-3">
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
                        <th>Entrega Original</th>
                        <th>Estudiante</th>
                        <th class="text-center">Devueltos</th>
                        <th>Fecha</th>
                        <th>Responsable</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devoluciones ?? [] as $devolucion)
                        <tr>
                            <td class="fw-medium">{{ $devolucion->codigo_general }}</td>
                            <td class="text-muted">{{ $devolucion->entrega->codigo_general ?? '—' }}</td>
                            <td>{{ $devolucion->estudiante->apellidos ?? '' }}, {{ $devolucion->estudiante->nombres ?? '' }}</td>
                            <td class="text-center fw-medium">{{ $devolucion->total_devueltos }}</td>
                            <td>{{ optional($devolucion->fecha_devolucion)->format('d/m/Y') }}</td>
                            <td>
                                @if($devolucion->tipo_firmante === 'apoderado' && $devolucion->padre)
                                    <span class="d-block fw-medium text-slate-800">{{ $devolucion->padre->apellidos }}, {{ $devolucion->padre->nombres }}</span>
                                    <span class="small text-slate-500">Apoderado</span>
                                @elseif($devolucion->tipo_firmante === 'estudiante')
                                    <span class="d-block fw-medium text-slate-800">{{ $devolucion->estudiante->apellidos ?? '' }}, {{ $devolucion->estudiante->nombres ?? '' }}</span>
                                    <span class="small text-slate-500">Estudiante</span>
                                @else
                                    {{ $devolucion->usuarioRegistro->nombres ?? '—' }}
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn-action btn-action-primary" title="Ver detalle" onclick='verDevolucion(@json($devolucion))'>
                                    <i data-lucide="eye" class="icon"></i>
                                </button>
                                <button type="button" class="btn-action btn-action-warning" title="Editar" onclick='editarDevolucion(@json($devolucion))'>
                                    <i data-lucide="pencil" class="icon"></i>
                                </button>
                                <button type="button" class="btn-action btn-action-success" title="Ver constancia" onclick='verConstanciaDevolucion({{ $devolucion->id }}, "{{ $devolucion->codigo_general }}")'>
                                    <i data-lucide="file-text" class="icon"></i>
                                </button>
                                <a href="{{ route('devoluciones.constancia', $devolucion) }}" class="btn-action btn-action-secondary" title="Descargar PDF">
                                    <i data-lucide="download" class="icon"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Sin devoluciones registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($devoluciones) && $devoluciones->hasPages())
            <div class="card-footer-base d-flex justify-content-center">
                {{ $devoluciones->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Ver Detalle de Devolución --}}
@component('components.modal', ['id' => 'modalVerDevolucion', 'title' => 'Constancia de Devolución', 'size' => 'xl'])
    <div class="d-flex align-items-center justify-content-between mb-3 pb-2" style="border-bottom: 2px solid #1e3a5f;">
        <div>
            <p class="small text-uppercase fw-bold text-slate-500 mb-1" style="letter-spacing: 0.1em;">Código de constancia</p>
            <p class="h4 fw-bold text-slate-900 mb-0" id="devDetCodigo">—</p>
        </div>
        <div id="devDetEstadoBadge"></div>
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
                        <p class="fw-semibold text-slate-800 mb-0" id="devDetNombre">—</p>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <p class="small text-slate-500 mb-0">DNI</p>
                            <p class="fw-semibold text-slate-800 mb-0" id="devDetDni">—</p>
                        </div>
                        <div class="col-6">
                            <p class="small text-slate-500 mb-0">Código</p>
                            <p class="fw-semibold text-slate-800 mb-0" id="devDetCodigoEst">—</p>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <p class="small text-slate-500 mb-0">Grado</p>
                            <p class="fw-semibold text-slate-800 mb-0" id="devDetGrado">—</p>
                        </div>
                        <div class="col-6">
                            <p class="small text-slate-500 mb-0">Sección</p>
                            <p class="fw-semibold text-slate-800 mb-0" id="devDetSeccion">—</p>
                        </div>
                    </div>
                    <div>
                        <p class="small text-slate-500 mb-0">Nivel</p>
                        <p class="fw-semibold text-slate-800 mb-0 text-capitalize" id="devDetNivel">—</p>
                    </div>
                </div>

                <h4 class="h6 fw-bold text-slate-800 text-uppercase pb-2 mb-3 mt-4" style="border-bottom: 1px solid var(--border-color); letter-spacing: 0.05em;">
                    Datos de la Devolución
                </h4>
                <div class="d-flex flex-column gap-3 small">
                    <div class="row g-2">
                        <div class="col-6">
                            <p class="small text-slate-500 mb-0">Fecha</p>
                            <p class="fw-semibold text-slate-800 mb-0" id="devDetFecha">—</p>
                        </div>
                        <div class="col-6">
                            <p class="small text-slate-500 mb-0">Hora</p>
                            <p class="fw-semibold text-slate-800 mb-0" id="devDetHora">—</p>
                        </div>
                    </div>
                    <div>
                        <p class="small text-slate-500 mb-0">Entrega original</p>
                        <p class="fw-semibold text-slate-800 mb-0" id="devDetEntrega">—</p>
                    </div>
                    <div>
                        <p class="small text-slate-500 mb-0">Responsable de registro</p>
                        <p class="fw-semibold text-slate-800 mb-0" id="devDetResponsable">—</p>
                    </div>
                    <div>
                        <p class="small text-slate-500 mb-0">Devuelto por</p>
                        <p class="fw-semibold text-slate-800 mb-0" id="devDetDevuelto">—</p>
                    </div>
                </div>

                <h4 class="h6 fw-bold text-slate-800 text-uppercase pb-2 mb-3 mt-4" style="border-bottom: 1px solid var(--border-color); letter-spacing: 0.05em;">
                    Resumen
                </h4>
                <div class="row g-2 small">
                    <div class="col-6">
                        <p class="small text-slate-500 mb-0">Devueltos</p>
                        <p class="fw-semibold text-success mb-0" id="devDetTotalDevueltos">—</p>
                    </div>
                    <div class="col-6">
                        <p class="small text-slate-500 mb-0">No devueltos</p>
                        <p class="fw-semibold text-warning mb-0" id="devDetTotalNoDevueltos">—</p>
                    </div>
                    <div class="col-6">
                        <p class="small text-slate-500 mb-0">Deficientes</p>
                        <p class="fw-semibold text-orange mb-0" id="devDetTotalDeficientes">—</p>
                    </div>
                    <div class="col-6">
                        <p class="small text-slate-500 mb-0">Perdidos</p>
                        <p class="fw-semibold text-danger mb-0" id="devDetTotalPerdidos">—</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card-app p-3 h-100">
                <h4 class="h6 fw-bold text-slate-800 text-uppercase pb-2 mb-3" style="border-bottom: 1px solid var(--border-color); letter-spacing: 0.05em;">
                    Relación de Libros Devueltos
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
                        <tbody id="devDetTablaLibros"></tbody>
                    </table>
                </div>
                <div id="devDetObservacionesBox" class="mt-3 p-3 bg-warning-subtle rounded d-none">
                    <p class="small fw-bold text-warning-emphasis text-uppercase mb-1">Observaciones:</p>
                    <p class="small text-warning-emphasis mb-0" id="devDetObservaciones"></p>
                </div>
            </div>
        </div>
    </div>

    @slot('footer')
        <button type="button" onclick="closeModal('modalVerDevolucion')" class="btn-app btn-app-secondary">Cerrar</button>
        <a href="#" id="devDetBtnDescargar" class="btn-app btn-app-primary">
            <i data-lucide="download" class="icon"></i> Descargar constancia
        </a>
    @endslot
@endcomponent

{{-- Modal Editar Devolución --}}
@component('components.modal', ['id' => 'modalEditarDevolucion', 'title' => 'Editar Devolución', 'size' => 'lg'])
    <form id="formEditarDevolucion" method="POST" data-action="">
        @csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label-app">Estado <span class="text-danger">*</span></label>
                <select name="estado" id="editDevolucionEstado" class="form-select-app" required>
                    <option value="completada">Completada</option>
                    <option value="parcial">Parcial</option>
                    <option value="anulada">Anulada</option>
                </select>
                <small class="form-text-app text-slate-500 mt-1 d-block">
                    <span id="editDevolucionEstadoHelp" class="small"></span>
                </small>
            </div>
            <div class="col-md-6">
                <label class="form-label-app">Fecha de devolución</label>
                <input type="text" id="editDevolucionFecha" class="form-control-app" disabled>
            </div>
            <div class="col-12">
                <label class="form-label-app">Observaciones generales</label>
                <textarea name="observaciones" id="editDevolucionObservaciones" rows="2" class="form-control-app" placeholder="Motivo del cambio de estado (opcional)"></textarea>
            </div>
            <div class="col-12">
                <label class="form-label-app">Detalle de libros devueltos</label>
                <div class="table-responsive-app">
                    <table class="table table-sm table-app mb-0">
                        <thead>
                            <tr>
                                <th style="width: 8%;">N°</th>
                                <th style="width: 22%;">Código</th>
                                <th>Título</th>
                                <th style="width: 22%;">Estado</th>
                                <th style="width: 28%;">Observación</th>
                            </tr>
                        </thead>
                        <tbody id="editDevolucionTablaDetalles"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 mt-3 border-top">
            <button type="button" onclick="closeModal('modalEditarDevolucion')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="button" id="btnGuardarEditarDevolucion" onclick="guardarEditarDevolucion()" class="btn-app btn-app-primary btn-submit-modal">
                <i data-lucide="save" class="icon"></i> Guardar cambios
            </button>
        </div>
    </form>
@endcomponent

{{-- Modal Ver Constancia (PDF en iframe) --}}
@component('components.modal', ['id' => 'modalVerConstanciaDevolucion', 'title' => 'Constancia de Devolución', 'size' => 'full'])
    <div style="height: 75vh; border: 1px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden;">
        <iframe id="iframeConstanciaDevolucion" src="" style="width: 100%; height: 100%; border: 0;" title="Constancia PDF"></iframe>
    </div>
    @slot('footer')
        <button type="button" onclick="closeModal('modalVerConstanciaDevolucion')" class="btn-app btn-app-secondary">Cerrar</button>
        <a href="#" id="btnDescargarConstanciaDevModal" target="_blank" class="btn-app btn-app-primary">
            <i data-lucide="download" class="icon"></i> Descargar PDF
        </a>
    @endslot
@endcomponent

{{-- =============== MODAL NUEVA DEVOLUCIÓN =============== --}}
<div id="modalNuevaDevolucion" class="modal-app modal-wide hidden" role="dialog" aria-modal="true">
    <div class="modal-overlay" onclick="closeModal('modalNuevaDevolucion')"></div>
    <div class="modal-panel">
        <div class="modal-header">
            <h3 class="modal-title d-flex align-items-center gap-2">
                <i data-lucide="corner-down-left" class="icon text-primary"></i>
                Registrar Devolución
            </h3>
            <button type="button" onclick="closeModal('modalNuevaDevolucion')" class="modal-close" aria-label="Cerrar">
                <i data-lucide="x" class="icon"></i>
            </button>
        </div>
        <div class="modal-body">
            <form id="formNuevaDevolucion" class="d-flex flex-column gap-4"
                  data-action="{{ route('devoluciones.api.store') }}"
                  data-pendientes-url="{{ route('devoluciones.api.pendientes') }}">
                {{-- 1: Buscar Entrega --}}
                <div>
                    <h4 class="section-title">1. Seleccionar Entrega</h4>
                    <div class="search-wrapper" id="dev-buscar-wrapper">
                        <div class="position-relative">
                            <i data-lucide="search" class="icon position-absolute text-slate-400" style="left: 0.75rem; top: 50%; transform: translateY(-50%);"></i>
                            <input type="text" id="modal_dev_buscar" class="form-control-app ps-4" placeholder="Buscar por DNI o código de entrega..." autocomplete="off">
                        </div>
                        <div id="modal_dev_resultados" class="search-results d-none"></div>
                    </div>
                    <input type="hidden" name="id_entrega" id="modal_id_entrega">
                    <div id="modal_dev_info" class="info-box d-none">
                        <div class="d-flex align-items-start justify-content-between">
                            <div>
                                <p class="info-label">Entrega seleccionada:</p>
                                <p class="info-name" id="modal_dev_lbl_cod"></p>
                                <div class="d-flex gap-4 info-meta">
                                    <span>Estudiante: <strong id="modal_dev_lbl_est"></strong></span>
                                    <span>Fecha Entrega: <strong id="modal_dev_lbl_fecha"></strong></span>
                                </div>
                            </div>
                            <button type="button" id="modal_dev_btn_cambiar" class="btn btn-sm btn-outline-primary">Cambiar</button>
                        </div>
                    </div>
                </div>

                {{-- 2: Libros a devolver --}}
                <div id="modal_dev_sec_libros" class="d-none">
                    <h4 class="section-title">2. Libros a Devolver</h4>
                    <div class="table-responsive-app">
                        <table class="table table-app mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 3rem;">#</th>
                                    <th>Libro</th>
                                    <th class="text-center">Estado de Devolución</th>
                                </tr>
                            </thead>
                            <tbody id="modal_dev_lista_libros">
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 3: Quien devuelve --}}
                <div id="modal_dev_sec_firmante" class="d-none">
                    <h4 class="section-title">3. ¿Quién devuelve?</h4>
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
                    <div>
                        <label class="form-label-app">Observaciones</label>
                        <textarea name="observaciones" rows="2" class="form-control-app" placeholder="Opcional"></textarea>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="closeModal('modalNuevaDevolucion')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="button" id="modal_btn_guardar_dev" class="btn-app btn-app-primary btn-submit-modal" disabled>
                <i data-lucide="check-circle" class="icon"></i> Registrar Devolución
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function verDevolucion(devolucion) {
        // Encabezado
        document.getElementById('devDetCodigo').textContent = devolucion.codigo_general;
        const estadoBadge = document.getElementById('devDetEstadoBadge');
        estadoBadge.innerHTML = devolucion.estado === 'completada'
            ? '<span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">COMPLETADA</span>'
            : '<span class="badge bg-light text-dark border px-3 py-2">' + (devolucion.estado ?? 'PENDIENTE').toUpperCase() + '</span>';

        // Estudiante
        document.getElementById('devDetNombre').textContent = (devolucion.estudiante?.apellidos ?? '') + ', ' + (devolucion.estudiante?.nombres ?? '');
        document.getElementById('devDetDni').textContent = devolucion.estudiante?.dni ?? '—';
        document.getElementById('devDetCodigoEst').textContent = devolucion.estudiante?.codigo_estudiante ?? '—';
        document.getElementById('devDetGrado').textContent = (devolucion.estudiante?.grado ?? '—') + '°';
        document.getElementById('devDetSeccion').textContent = devolucion.estudiante?.seccion ?? '—';
        document.getElementById('devDetNivel').textContent = devolucion.estudiante?.nivel ?? '—';

        // Devolución
        const fecha = devolucion.fecha_devolucion ? new Date(devolucion.fecha_devolucion).toLocaleDateString('es-PE') : '—';
        document.getElementById('devDetFecha').textContent = fecha;
        document.getElementById('devDetHora').textContent = devolucion.hora_devolucion ?? '—';
        document.getElementById('devDetEntrega').textContent = devolucion.entrega?.codigo_general ?? '—';
        document.getElementById('devDetResponsable').textContent = devolucion.usuario_registro?.nombres ?? '—';

        const devuelto = devolucion.tipo_firmante === 'apoderado' && devolucion.padre
            ? devolucion.padre.nombre_completo + ' (Apoderado)'
            : 'El propio estudiante';
        document.getElementById('devDetDevuelto').textContent = devuelto;

        // Resumen
        document.getElementById('devDetTotalDevueltos').textContent = devolucion.total_devueltos ?? 0;
        document.getElementById('devDetTotalNoDevueltos').textContent = devolucion.total_no_devueltos ?? 0;
        document.getElementById('devDetTotalDeficientes').textContent = devolucion.total_deficientes ?? 0;
        document.getElementById('devDetTotalPerdidos').textContent = devolucion.total_perdidos ?? 0;

        // Tabla de libros
        const tbody = document.getElementById('devDetTablaLibros');
        tbody.innerHTML = '';
        if (devolucion.detalles && devolucion.detalles.length > 0) {
            devolucion.detalles.forEach((d, i) => {
                const estado = d.estado_libro ?? 'bueno';
                let estadoClass = 'bg-light text-dark border';
                if (estado === 'bueno') estadoClass = 'bg-success-subtle text-success';
                if (estado === 'deteriorado') estadoClass = 'bg-warning-subtle text-warning';
                if (estado === 'deficiente') estadoClass = 'bg-orange-subtle text-orange';
                if (estado === 'perdido') estadoClass = 'bg-danger-subtle text-danger';
                if (estado === 'no_devuelto') estadoClass = 'bg-secondary-subtle text-secondary';
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
        const obsBox = document.getElementById('devDetObservacionesBox');
        if (devolucion.observaciones) {
            obsBox.classList.remove('d-none');
            document.getElementById('devDetObservaciones').textContent = devolucion.observaciones;
        } else {
            obsBox.classList.add('d-none');
        }

        // Botón descargar
        document.getElementById('devDetBtnDescargar').href = `/devoluciones/${devolucion.id}/constancia`;

        openModal('modalVerDevolucion');
    }

    function editarDevolucion(devolucion) {
        const form = document.getElementById('formEditarDevolucion');
        const url = `/devoluciones/${devolucion.id}/api/update`;
        form.action = url;
        form.setAttribute('data-action', url);

        document.getElementById('editDevolucionEstado').value = devolucion.estado ?? 'completada';
        document.getElementById('editDevolucionFecha').value = devolucion.fecha_devolucion
            ? new Date(devolucion.fecha_devolucion).toLocaleDateString('es-PE') + ' ' + (devolucion.hora_devolucion ?? '')
            : '—';
        document.getElementById('editDevolucionObservaciones').value = devolucion.observaciones ?? '';

        // Renderizar tabla de detalles
        const tbody = document.getElementById('editDevolucionTablaDetalles');
        tbody.innerHTML = '';
        const detalles = devolucion.detalles || [];
        const estados = {
            bueno: 'Bueno',
            deteriorado: 'Deteriorado',
            deficiente: 'Deficiente',
            no_devuelto: 'No devuelto',
            perdido: 'Perdido',
        };

        if (detalles.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No hay libros registrados.</td></tr>';
        } else {
            detalles.forEach((d, index) => {
                const options = Object.entries(estados).map(([val, label]) =>
                    `<option value="${val}" ${d.estado_libro === val ? 'selected' : ''}>${label}</option>`
                ).join('');

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="text-center">${index + 1}</td>
                    <td class="font-mono">${d.libro?.codigo_libro ?? '—'}</td>
                    <td>${d.libro?.nombre ?? '—'}</td>
                    <td>
                        <select name="detalles[${index}][estado_libro]" class="form-select-app form-select-app-sm" required>
                            ${options}
                        </select>
                        <input type="hidden" name="detalles[${index}][id]" value="${d.id}">
                        <input type="hidden" name="detalles[${index}][id_libro]" value="${d.id_libro}">
                    </td>
                    <td>
                        <input type="text" name="detalles[${index}][observaciones]" value="${d.observaciones ?? ''}" class="form-control-app form-control-app-sm" placeholder="Opcional">
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        actualizarAyudaEstadoDev(devolucion.estado ?? 'completada');
        openModal('modalEditarDevolucion');
    }

    function actualizarAyudaEstadoDev(estado) {
        const help = document.getElementById('editDevolucionEstadoHelp');
        const mensajes = {
            completada: 'La devolución está completa y activa.',
            parcial: 'Solo algunos libros fueron devueltos.',
            anulada: 'La devolución será anulada y el estado de los libros se revertirá.',
        };
        if (help) help.textContent = mensajes[estado] ?? '';
    }

    document.getElementById('editDevolucionEstado')?.addEventListener('change', function() {
        actualizarAyudaEstadoDev(this.value);
    });

    function guardarEditarDevolucion() {
        const form = document.getElementById('formEditarDevolucion');
        const url = form?.getAttribute('data-action');

        if (!form || !url) {
            window.showToast('Error: URL no configurada. Recargue la página (Ctrl+F5).', 'error');
            return;
        }

        const btn = document.getElementById('btnGuardarEditarDevolucion');
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
                    message: data.message || 'Devolución actualizada correctamente.',
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

    function verConstanciaDevolucion(id, codigo) {
        document.getElementById('iframeConstanciaDevolucion').src = `/devoluciones/${id}/ver-constancia`;
        document.getElementById('btnDescargarConstanciaDevModal').href = `/devoluciones/${id}/constancia`;
        openModal('modalVerConstanciaDevolucion');
    }
</script>
@endpush
@endsection
