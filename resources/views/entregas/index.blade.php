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
                    <div class="col-lg-3">
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
                            <td>{{ $entrega->usuarioRegistro->nombres ?? '—' }}</td>
                            <td>
                                @if($entrega->estado === 'completada')
                                    <span class="badge bg-success-subtle text-success">Entregado</span>
                                @else
                                    <span class="badge bg-light text-dark border">{{ ucfirst($entrega->estado) }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('entregas.show', $entrega) }}" class="btn-action btn-action-primary">
                                    <i data-lucide="eye" class="icon"></i>
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
@endsection
