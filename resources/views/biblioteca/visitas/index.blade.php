@extends('layouts.app')

@section('title', 'Visitas a Sala de Lectura')
@section('page-title', 'Biblioteca General')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="h3 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-1">
            <span class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success" style="width: 2.5rem; height: 2.5rem; font-size: 1.125rem;">
                <i data-lucide="door-open" class="icon"></i>
            </span>
            Visitas a Sala de Lectura
        </h2>
        <p class="text-sm text-slate-500 mb-0">Registro de ingreso a la sala de lectura.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button type="button" onclick="openModal('modalCrearVisita')" class="btn-app btn-app-primary">
            <i data-lucide="plus" class="icon"></i>
            Registrar visita
        </button>
    </div>
</div>

<div class="filter-bar mb-4">
    <form method="GET" class="row g-3 align-items-end w-100">
        <div class="col-lg-4">
            <label class="form-label-app">Buscar</label>
            <div class="position-relative">
                <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400"><i data-lucide="search" class="icon"></i></span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control-app ps-5" placeholder="Nombre...">
            </div>
        </div>
        <div class="col-lg-3">
            <label class="form-label-app">Fecha</label>
            <input type="date" name="fecha" value="{{ request('fecha') }}" class="form-control-app">
        </div>
        <div class="col-lg-2">
            <button type="submit" class="btn-app btn-app-secondary w-100">Filtrar</button>
        </div>
        <div class="col-lg-2">
            <a href="{{ route('biblioteca.visitas.index') }}" class="btn-app btn-app-light w-100">Limpiar</a>
        </div>
    </form>
</div>

<div class="table-responsive-app">
    <table class="table-app">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Visitante</th>
                <th>Tipo</th>
                <th>Motivo</th>
                <th>Entrada</th>
                <th>Salida</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($visitas as $visita)
                <tr>
                    <td>{{ $visita->fecha->format('d/m/Y') }}</td>
                    <td class="fw-medium text-slate-900">
                        @if($visita->socio)
                            {{ $visita->socio->nombre_completo }}
                        @else
                            {{ $visita->nombres ?: '—' }}
                        @endif
                    </td>
                    <td class="text-capitalize">{{ $visita->tipo_visitante }}</td>
                    <td class="text-capitalize">{{ str_replace('_', ' ', $visita->motivo) }}</td>
                    <td>{{ $visita->hora_entrada }}</td>
                    <td>{{ $visita->hora_salida ?: '—' }}</td>
                    <td class="text-end">
                        <button type="button" onclick="editarVisita({{ json_encode($visita->toArray()) }})" class="btn-action btn-action-secondary" title="Editar">
                            <i data-lucide="pencil" class="icon"></i>
                        </button>
                        <form action="{{ route('biblioteca.visitas.destroy', $visita) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta visita?')">
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
                    <td colspan="7" class="text-center py-4 text-slate-500">No hay visitas registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $visitas->links() }}
</div>

@component('components.modal', ['id' => 'modalCrearVisita', 'title' => 'Registrar Visita', 'icon' => 'plus'])
    <form method="POST" action="{{ route('biblioteca.visitas.store') }}">
        @csrf
        @include('biblioteca.visitas.form', ['modo' => 'crear'])
        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('modalCrearVisita')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">Guardar</button>
        </div>
    </form>
@endcomponent

@component('components.modal', ['id' => 'modalEditarVisita', 'title' => 'Editar Visita', 'icon' => 'pencil'])
    <form id="formEditarVisita" method="POST">
        @csrf
        @method('PUT')
        @include('biblioteca.visitas.form', ['modo' => 'editar'])
        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('modalEditarVisita')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">Guardar cambios</button>
        </div>
    </form>
@endcomponent

@push('scripts')
<script>
    function editarVisita(item) {
        document.getElementById('formEditarVisita').action = '/biblioteca/visitas/' + item.id;
        document.getElementById('editIdSocioVisita').value = item.id_socio ?? '';
        document.getElementById('editTipoVisitante').value = item.tipo_visitante;
        document.getElementById('editNombresVisita').value = item.nombres ?? '';
        document.getElementById('editGradoVisita').value = item.grado ?? '';
        document.getElementById('editSeccionVisita').value = item.seccion ?? '';
        document.getElementById('editMotivoVisita').value = item.motivo;
        document.getElementById('editFechaVisita').value = item.fecha;
        document.getElementById('editHoraEntrada').value = item.hora_entrada;
        document.getElementById('editHoraSalida').value = item.hora_salida ?? '';
        openModal('modalEditarVisita');
    }
</script>
@endpush
@endsection
