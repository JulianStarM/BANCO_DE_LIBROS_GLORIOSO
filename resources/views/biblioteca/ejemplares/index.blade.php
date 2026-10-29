@extends('layouts.app')

@section('title', 'Inventario de Ejemplares')
@section('page-title', 'Biblioteca General')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="h3 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-1">
            <span class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success" style="width: 2.5rem; height: 2.5rem; font-size: 1.125rem;">
                <i data-lucide="book-copy" class="icon"></i>
            </span>
            Inventario de Ejemplares
        </h2>
        <p class="text-sm text-slate-500 mb-0">Controla cada copia física del acervo.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button type="button" onclick="openModal('modalCrearEjemplar')" class="btn-app btn-app-primary">
            <i data-lucide="plus" class="icon"></i>
            Nuevo ejemplar
        </button>
    </div>
</div>

<div class="filter-bar mb-4">
    <form method="GET" class="row g-3 align-items-end w-100">
        <div class="col-lg-4">
            <label class="form-label-app">Buscar</label>
            <div class="position-relative">
                <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400"><i data-lucide="search" class="icon"></i></span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control-app ps-5" placeholder="Código o título...">
            </div>
        </div>
        <div class="col-lg-3">
            <label class="form-label-app">Disponibilidad</label>
            <select name="estado_disponibilidad" class="form-select-app">
                <option value="">Todos</option>
                <option value="disponible" @selected(request('estado_disponibilidad') === 'disponible')>Disponible</option>
                <option value="prestado" @selected(request('estado_disponibilidad') === 'prestado')>Prestado</option>
                <option value="reservado" @selected(request('estado_disponibilidad') === 'reservado')>Reservado</option>
                <option value="baja" @selected(request('estado_disponibilidad') === 'baja')>Baja</option>
            </select>
        </div>
        <div class="col-lg-3">
            <label class="form-label-app">Estante</label>
            <select name="id_estante" class="form-select-app">
                <option value="">Todos</option>
                @foreach($estantes as $estante)
                    <option value="{{ $estante->id }}" @selected(request('id_estante') == $estante->id)>{{ $estante->codigo_estante }} - {{ $estante->nombre }}</option>
                @endforeach
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
                <th>Título</th>
                <th>Estado físico</th>
                <th>Disponibilidad</th>
                <th>Ubicación</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ejemplares as $ejemplar)
                <tr>
                    <td class="fw-medium text-slate-900">{{ $ejemplar->codigo_ejemplar }}</td>
                    <td>
                        <div class="book-title">{{ $ejemplar->catalogo->titulo ?? '—' }}</div>
                        <div class="book-meta">N° registro: {{ $ejemplar->numero_registro }}</div>
                    </td>
                    <td class="text-capitalize">{{ $ejemplar->estado_fisico }}</td>
                    <td class="text-center">
                        @if($ejemplar->estado_disponibilidad === 'disponible')
                            <span class="badge rounded-pill bg-success-subtle text-success">Disponible</span>
                        @elseif($ejemplar->estado_disponibilidad === 'prestado')
                            <span class="badge rounded-pill bg-warning-subtle text-warning">Prestado</span>
                        @elseif($ejemplar->estado_disponibilidad === 'reservado')
                            <span class="badge rounded-pill bg-info-subtle text-info">Reservado</span>
                        @else
                            <span class="badge rounded-pill bg-secondary-subtle text-secondary">Baja</span>
                        @endif
                    </td>
                    <td>
                        {{ $ejemplar->estante->codigo_estante ?? '—' }}
                        <div class="text-xs text-slate-500">{{ $ejemplar->estante->ubicacion->nombre ?? '' }}</div>
                    </td>
                    <td class="text-end">
                        <button type="button" onclick="editarEjemplar({{ json_encode($ejemplar->toArray()) }})" class="btn-action btn-action-secondary" title="Editar">
                            <i data-lucide="pencil" class="icon"></i>
                        </button>
                        <form action="{{ route('biblioteca.ejemplares.destroy', $ejemplar) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este ejemplar?')">
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
                    <td colspan="6" class="text-center py-4 text-slate-500">No hay ejemplares registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $ejemplares->links() }}
</div>

@component('components.modal', ['id' => 'modalCrearEjemplar', 'title' => 'Registrar Ejemplar', 'icon' => 'plus'])
    <form method="POST" action="{{ route('biblioteca.ejemplares.store') }}">
        @csrf
        @include('biblioteca.ejemplares.form', ['modo' => 'crear'])
        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('modalCrearEjemplar')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">Guardar</button>
        </div>
    </form>
@endcomponent

@component('components.modal', ['id' => 'modalEditarEjemplar', 'title' => 'Editar Ejemplar', 'icon' => 'pencil'])
    <form id="formEditarEjemplar" method="POST">
        @csrf
        @method('PUT')
        @include('biblioteca.ejemplares.form', ['modo' => 'editar'])
        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('modalEditarEjemplar')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">Guardar cambios</button>
        </div>
    </form>
@endcomponent

@push('scripts')
<script>
    function editarEjemplar(item) {
        document.getElementById('formEditarEjemplar').action = '/biblioteca/ejemplares/' + item.id;
        document.getElementById('editIdCatalogo').value = item.id_catalogo;
        document.getElementById('editIdEstante').value = item.id_estante ?? '';
        document.getElementById('editCodigoEjemplar').value = item.codigo_ejemplar;
        document.getElementById('editNumeroRegistro').value = item.numero_registro;
        document.getElementById('editFechaIngreso').value = item.fecha_ingreso;
        document.getElementById('editProcedencia').value = item.procedencia ?? '';
        document.getElementById('editEstadoFisico').value = item.estado_fisico;
        document.getElementById('editEstadoDisponibilidad').value = item.estado_disponibilidad;
        document.getElementById('editPrecioAdquisicion').value = item.precio_adquisicion ?? '';
        document.getElementById('editObservacionesEjemplar').value = item.observaciones ?? '';
        openModal('modalEditarEjemplar');
    }
</script>
@endpush
@endsection
