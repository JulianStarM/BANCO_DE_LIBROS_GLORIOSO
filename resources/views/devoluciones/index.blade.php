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
                    <div class="col-lg-8 position-relative">
                        <i data-lucide="search" class="icon position-absolute text-slate-400" style="left: 1rem; top: 50%; transform: translateY(-50%);"></i>
                        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por DNI, estudiante, código..." class="form-control-app ps-5" />
                    </div>
                    <div class="col-lg-4">
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
                            <td class="text-center fw-medium">{{ $devolucion->total_libros_devueltos }}</td>
                            <td>{{ optional($devolucion->fecha_devolucion)->format('d/m/Y') }}</td>
                            <td>{{ $devolucion->usuarioRegistro->nombres ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('devoluciones.show', $devolucion) }}" class="btn-action btn-action-primary">
                                    <i data-lucide="eye" class="icon"></i>
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
@endsection
