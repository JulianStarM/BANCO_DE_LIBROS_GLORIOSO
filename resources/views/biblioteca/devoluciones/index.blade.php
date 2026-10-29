@extends('layouts.app')

@section('title', 'Devoluciones de Biblioteca')
@section('page-title', 'Biblioteca General')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="h3 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-1">
            <span class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success" style="width: 2.5rem; height: 2.5rem; font-size: 1.125rem;">
                <i data-lucide="rotate-ccw" class="icon"></i>
            </span>
            Registrar Devoluciones
        </h2>
        <p class="text-sm text-slate-500 mb-0">Registra la devolución de ejemplares prestados.</p>
    </div>
</div>

<div class="filter-bar mb-4">
    <form method="GET" class="row g-3 align-items-end w-100">
        <div class="col-lg-6">
            <label class="form-label-app">Buscar préstamo</label>
            <div class="position-relative">
                <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400"><i data-lucide="search" class="icon"></i></span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control-app ps-5" placeholder="Código o lector...">
            </div>
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
                <th>Ejemplares pendientes</th>
                <th>Fecha límite</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prestamos as $prestamo)
                <tr>
                    <td class="fw-medium text-slate-900">{{ $prestamo->codigo_prestamo }}</td>
                    <td>{{ $prestamo->socio->nombre_completo ?? '—' }}</td>
                    <td>
                        @foreach($prestamo->detalles->where('estado_devolucion', 'pendiente') as $detalle)
                            <div class="text-sm">{{ $detalle->ejemplar->codigo_ejemplar }} - {{ $detalle->ejemplar->catalogo->titulo ?? '' }}</div>
                        @endforeach
                    </td>
                    <td>
                        @if($prestamo->estaVencido())
                            <span class="text-danger fw-medium">{{ $prestamo->fecha_limite->format('d/m/Y') }}</span>
                        @else
                            {{ $prestamo->fecha_limite->format('d/m/Y') }}
                        @endif
                    </td>
                    <td class="text-end">
                        <button type="button" onclick="abrirDevolucion({{ json_encode($prestamo->toArray()) }}, {{ json_encode($prestamo->detalles->where('estado_devolucion', 'pendiente')->map(fn($d) => ['id' => $d->id, 'codigo' => $d->ejemplar->codigo_ejemplar, 'titulo' => $d->ejemplar->catalogo->titulo ?? ''])->values()) }})" class="btn-app btn-app-primary btn-sm">
                            <i data-lucide="rotate-ccw" class="icon"></i> Devolver
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-slate-500">No hay préstamos pendientes.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $prestamos->links() }}
</div>

@component('components.modal', ['id' => 'modalDevolucion', 'title' => 'Registrar Devolución', 'icon' => 'rotate-ccw', 'size' => 'lg'])
    <form id="formDevolucion" method="POST">
        @csrf
        <div class="form-group-app">
            <label class="form-label-app">Fecha de devolución *</label>
            <input type="date" name="fecha_devolucion" class="form-control-app" required value="{{ date('Y-m-d') }}">
        </div>
        <div id="contenedorDetallesDevolucion"></div>
        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('modalDevolucion')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">Registrar devolución</button>
        </div>
    </form>
@endcomponent

@push('scripts')
<script>
    function abrirDevolucion(prestamo, detalles) {
        document.getElementById('formDevolucion').action = '/biblioteca/devoluciones/' + prestamo.id;
        const contenedor = document.getElementById('contenedorDetallesDevolucion');
        contenedor.innerHTML = '';

        detalles.forEach(function (detalle) {
            const div = document.createElement('div');
            div.className = 'border rounded p-3 mb-3';
            div.innerHTML = `
                <p class="fw-medium mb-2">${detalle.codigo} - ${detalle.titulo}</p>
                <input type="hidden" name="detalles[${detalle.id}][id]" value="${detalle.id}">
                <div class="row g-3">
                    <div class="col-md-6 form-group-app mb-0">
                        <label class="form-label-app">Estado de retorno *</label>
                        <select name="detalles[${detalle.id}][estado_fisico_retorno]" class="form-select-app" required>
                            <option value="bueno">Bueno</option>
                            <option value="regular">Regular</option>
                            <option value="malo">Malo</option>
                            <option value="perdido">Perdido</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group-app mb-0">
                        <label class="form-label-app">Observaciones</label>
                        <input type="text" name="detalles[${detalle.id}][observaciones]" class="form-control-app">
                    </div>
                </div>
            `;
            contenedor.appendChild(div);
        });

        openModal('modalDevolucion');
    }
</script>
@endpush
@endsection
