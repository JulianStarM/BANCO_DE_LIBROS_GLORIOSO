@extends('layouts.app')

@section('title', 'Préstamos y Devoluciones')
@section('page-title', 'Biblioteca General')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="h3 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-1">
            <span class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success" style="width: 2.5rem; height: 2.5rem; font-size: 1.125rem;">
                <i data-lucide="clipboard-list" class="icon"></i>
            </span>
            Préstamos
        </h2>
        <p class="text-sm text-slate-500 mb-0">Registra y consulta los préstamos de ejemplares.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button type="button" onclick="openModal('modalCrearPrestamo')" class="btn-app btn-app-primary">
            <i data-lucide="plus" class="icon"></i>
            Nuevo préstamo
        </button>
    </div>
</div>

<div class="filter-bar mb-4">
    <form method="GET" class="row g-3 align-items-end w-100">
        <div class="col-lg-6">
            <label class="form-label-app">Buscar</label>
            <div class="position-relative">
                <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400"><i data-lucide="search" class="icon"></i></span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control-app ps-5" placeholder="Código o lector...">
            </div>
        </div>
        <div class="col-lg-3">
            <label class="form-label-app">Estado</label>
            <select name="estado" class="form-select-app">
                <option value="">Todos</option>
                <option value="activo" @selected(request('estado') === 'activo')>Activo</option>
                <option value="devuelto" @selected(request('estado') === 'devuelto')>Devuelto</option>
                <option value="vencido" @selected(request('estado') === 'vencido')>Vencido</option>
                <option value="perdido" @selected(request('estado') === 'perdido')>Perdido</option>
            </select>
        </div>
        <div class="col-lg-2">
            <button type="submit" class="btn-app btn-app-secondary w-100">Filtrar</button>
        </div>
    </form>
</div>

<div class="table-responsive-app">
    <table class="table-app">
        <thead>
            <tr>
                <th>Código</th>
                <th>Lector</th>
                <th>Ejemplares</th>
                <th>Fecha límite</th>
                <th>Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prestamos as $prestamo)
                <tr>
                    <td class="fw-medium text-slate-900">{{ $prestamo->codigo_prestamo }}</td>
                    <td>{{ $prestamo->socio->nombre_completo ?? '—' }}</td>
                    <td>
                        @foreach($prestamo->detalles as $detalle)
                            <div class="text-sm">{{ $detalle->ejemplar->codigo_ejemplar }} - {{ $detalle->ejemplar->catalogo->titulo ?? '' }}</div>
                        @endforeach
                    </td>
                    <td>{{ $prestamo->fecha_limite->format('d/m/Y') }}</td>
                    <td class="text-center">
                        @if($prestamo->estado === 'devuelto')
                            <span class="badge rounded-pill bg-success-subtle text-success">Devuelto</span>
                        @elseif($prestamo->estaVencido())
                            <span class="badge rounded-pill bg-danger-subtle text-danger">Vencido</span>
                        @else
                            <span class="badge rounded-pill bg-warning-subtle text-warning">Activo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('biblioteca.devoluciones.index', ['prestamo' => $prestamo->id]) }}" class="btn-action btn-action-secondary" title="Registrar devolución">
                            <i data-lucide="rotate-ccw" class="icon"></i>
                        </a>
                        <form action="{{ route('biblioteca.prestamos.destroy', $prestamo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este préstamo?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action btn-action-danger" title="Eliminar">
                                <i data-lucide="trash-2" class="icon"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-slate-500">No hay préstamos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $prestamos->links() }}
</div>

@component('components.modal', ['id' => 'modalCrearPrestamo', 'title' => 'Registrar Préstamo', 'icon' => 'plus', 'size' => 'lg'])
    <form method="POST" action="{{ route('biblioteca.prestamos.store') }}">
        @csrf
        <div class="row g-3">
            <div class="col-12 form-group-app">
                <label class="form-label-app">Lector *</label>
                <select name="id_socio" class="form-select-app" required>
                    <option value="">Seleccione...</option>
                    @foreach($socios as $socio)
                        <option value="{{ $socio->id }}">{{ $socio->nombre_completo }} ({{ $socio->codigo_socio }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 form-group-app">
                <label class="form-label-app">Tipo de préstamo *</label>
                <select name="tipo_prestamo" class="form-select-app" required>
                    <option value="domicilio">Domicilio</option>
                    <option value="sala">Sala</option>
                </select>
            </div>
            <div class="col-3 form-group-app">
                <label class="form-label-app">Fecha préstamo *</label>
                <input type="date" name="fecha_prestamo" class="form-control-app" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-3 form-group-app">
                <label class="form-label-app">Fecha límite *</label>
                <input type="date" name="fecha_limite" class="form-control-app" required>
            </div>
            <div class="col-12 form-group-app">
                <label class="form-label-app">Ejemplares disponibles *</label>
                <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                    @forelse($ejemplares as $ejemplar)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="ejemplares[]" value="{{ $ejemplar->id }}" id="ejemplar_{{ $ejemplar->id }}">
                            <label class="form-check-label" for="ejemplar_{{ $ejemplar->id }}">
                                {{ $ejemplar->codigo_ejemplar }} - {{ $ejemplar->catalogo->titulo }}
                            </label>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 mb-0">No hay ejemplares disponibles.</p>
                    @endforelse
                </div>
            </div>
            <div class="col-12 form-group-app">
                <label class="form-label-app">Observaciones</label>
                <textarea name="observaciones" rows="2" class="form-control-app"></textarea>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('modalCrearPrestamo')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">Guardar</button>
        </div>
    </form>
@endcomponent
@endsection
