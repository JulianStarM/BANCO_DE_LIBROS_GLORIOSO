@extends('layouts.app')

@section('title', 'Multas de Biblioteca')
@section('page-title', 'Biblioteca General')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="h3 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-1">
            <span class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success" style="width: 2.5rem; height: 2.5rem; font-size: 1.125rem;">
                <i data-lucide="alert-circle" class="icon"></i>
            </span>
            Multas
        </h2>
        <p class="text-sm text-slate-500 mb-0">Control de multas por retrasos, daños o pérdidas.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button type="button" onclick="openModal('modalCrearMulta')" class="btn-app btn-app-primary">
            <i data-lucide="plus" class="icon"></i>
            Nueva multa
        </button>
    </div>
</div>

<div class="filter-bar mb-4">
    <form method="GET" class="row g-3 align-items-end w-100">
        <div class="col-lg-6">
            <label class="form-label-app">Buscar</label>
            <div class="position-relative">
                <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400"><i data-lucide="search" class="icon"></i></span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control-app ps-5" placeholder="Lector...">
            </div>
        </div>
        <div class="col-lg-3">
            <label class="form-label-app">Estado</label>
            <select name="estado" class="form-select-app">
                <option value="">Todos</option>
                <option value="pendiente" @selected(request('estado') === 'pendiente')>Pendiente</option>
                <option value="pagada" @selected(request('estado') === 'pagada')>Pagada</option>
                <option value="exonerada" @selected(request('estado') === 'exonerada')>Exonerada</option>
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
                <th>Lector</th>
                <th>Tipo</th>
                <th>Días atraso</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($multas as $multa)
                <tr>
                    <td class="fw-medium text-slate-900">{{ $multa->socio->nombre_completo ?? '—' }}</td>
                    <td class="text-capitalize">{{ $multa->tipo_multa }}</td>
                    <td>{{ $multa->dias_atraso ?? '—' }}</td>
                    <td>S/ {{ number_format($multa->monto, 2) }}</td>
                    <td class="text-center">
                        @if($multa->estado === 'pendiente')
                            <span class="badge rounded-pill bg-warning-subtle text-warning">Pendiente</span>
                        @elseif($multa->estado === 'pagada')
                            <span class="badge rounded-pill bg-success-subtle text-success">Pagada</span>
                        @else
                            <span class="badge rounded-pill bg-info-subtle text-info">Exonerada</span>
                        @endif
                    </td>
                    <td>{{ $multa->fecha_multa->format('d/m/Y') }}</td>
                    <td class="text-end">
                        <button type="button" onclick="editarMulta({{ json_encode($multa->toArray()) }})" class="btn-action btn-action-secondary" title="Editar">
                            <i data-lucide="pencil" class="icon"></i>
                        </button>
                        <form action="{{ route('biblioteca.multas.destroy', $multa) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta multa?')">
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
                    <td colspan="7" class="text-center py-4 text-slate-500">No hay multas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $multas->links() }}
</div>

@component('components.modal', ['id' => 'modalCrearMulta', 'title' => 'Registrar Multa', 'icon' => 'plus'])
    <form method="POST" action="{{ route('biblioteca.multas.store') }}">
        @csrf
        @include('biblioteca.multas.form', ['modo' => 'crear'])
        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('modalCrearMulta')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">Guardar</button>
        </div>
    </form>
@endcomponent

@component('components.modal', ['id' => 'modalEditarMulta', 'title' => 'Editar Multa', 'icon' => 'pencil'])
    <form id="formEditarMulta" method="POST">
        @csrf
        @method('PUT')
        @include('biblioteca.multas.form', ['modo' => 'editar'])
        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('modalEditarMulta')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">Guardar cambios</button>
        </div>
    </form>
@endcomponent

@push('scripts')
<script>
    function editarMulta(item) {
        document.getElementById('formEditarMulta').action = '/biblioteca/multas/' + item.id;
        document.getElementById('editTipoMulta').value = item.tipo_multa;
        document.getElementById('editDiasAtraso').value = item.dias_atraso ?? '';
        document.getElementById('editMonto').value = item.monto;
        document.getElementById('editEstadoMulta').value = item.estado;
        document.getElementById('editFechaPago').value = item.fecha_pago ?? '';
        document.getElementById('editObservacionesMulta').value = item.observaciones ?? '';
        openModal('modalEditarMulta');
    }
</script>
@endpush
@endsection
