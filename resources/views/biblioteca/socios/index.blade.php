@extends('layouts.app')

@section('title', 'Lectores')
@section('page-title', 'Biblioteca General')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="h3 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-1">
            <span class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success" style="width: 2.5rem; height: 2.5rem; font-size: 1.125rem;">
                <i data-lucide="users" class="icon"></i>
            </span>
            Lectores
        </h2>
        <p class="text-sm text-slate-500 mb-0">Administra los socios de la biblioteca.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button type="button" onclick="openModal('modalCrearSocio')" class="btn-app btn-app-primary">
            <i data-lucide="plus" class="icon"></i>
            Nuevo lector
        </button>
    </div>
</div>

<div class="filter-bar mb-4">
    <form method="GET" class="row g-3 align-items-end w-100">
        <div class="col-lg-6">
            <label class="form-label-app">Buscar</label>
            <div class="position-relative">
                <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400"><i data-lucide="search" class="icon"></i></span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control-app ps-5" placeholder="Nombre, código o DNI...">
            </div>
        </div>
        <div class="col-lg-3">
            <label class="form-label-app">Tipo</label>
            <select name="tipo_socio" class="form-select-app">
                <option value="">Todos</option>
                <option value="estudiante" @selected(request('tipo_socio') === 'estudiante')>Estudiante</option>
                <option value="docente" @selected(request('tipo_socio') === 'docente')>Docente</option>
                <option value="administrativo" @selected(request('tipo_socio') === 'administrativo')>Administrativo</option>
                <option value="externo" @selected(request('tipo_socio') === 'externo')>Externo</option>
            </select>
        </div>
        <div class="col-lg-2">
            <button type="submit" class="btn-app btn-app-secondary w-100">Filtrar</button>
        </div>
        <div class="col-lg-1">
            <a href="{{ route('biblioteca.socios.index') }}" class="btn-app btn-app-light w-100">Limpiar</a>
        </div>
    </form>
</div>

<div class="table-responsive-app">
    <table class="table-app">
        <thead>
            <tr>
                <th>Código</th>
                <th>Lector</th>
                <th>Tipo</th>
                <th>Contacto</th>
                <th class="text-center">Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($socios as $socio)
                <tr>
                    <td class="fw-medium text-slate-900">{{ $socio->codigo_socio }}</td>
                    <td>
                        <div class="book-title">{{ $socio->nombre_completo }}</div>
                        <div class="book-meta">DNI: {{ $socio->dni }}</div>
                    </td>
                    <td class="text-capitalize">{{ $socio->tipo_socio }}</td>
                    <td>
                        {{ $socio->telefono ?: '—' }}
                        @if($socio->grado)
                            <div class="text-xs text-slate-500">{{ $socio->grado }}° {{ $socio->seccion }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($socio->estado === 'activo')
                            <span class="badge rounded-pill bg-success-subtle text-success">Activo</span>
                        @elseif($socio->estado === 'suspendido')
                            <span class="badge rounded-pill bg-warning-subtle text-warning">Suspendido</span>
                        @else
                            <span class="badge rounded-pill bg-secondary-subtle text-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <button type="button" onclick="editarSocio({{ json_encode($socio->toArray()) }})" class="btn-action btn-action-secondary" title="Editar">
                            <i data-lucide="pencil" class="icon"></i>
                        </button>
                        <form action="{{ route('biblioteca.socios.destroy', $socio) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este lector?')">
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
                    <td colspan="6" class="text-center py-4 text-slate-500">No hay lectores registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $socios->links() }}
</div>

@component('components.modal', ['id' => 'modalCrearSocio', 'title' => 'Registrar Lector', 'icon' => 'plus'])
    <form method="POST" action="{{ route('biblioteca.socios.store') }}">
        @csrf
        @include('biblioteca.socios.form', ['modo' => 'crear'])
        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('modalCrearSocio')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">Guardar</button>
        </div>
    </form>
@endcomponent

@component('components.modal', ['id' => 'modalEditarSocio', 'title' => 'Editar Lector', 'icon' => 'pencil'])
    <form id="formEditarSocio" method="POST">
        @csrf
        @method('PUT')
        @include('biblioteca.socios.form', ['modo' => 'editar'])
        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('modalEditarSocio')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">Guardar cambios</button>
        </div>
    </form>
@endcomponent

@push('scripts')
<script>
    function editarSocio(item) {
        document.getElementById('formEditarSocio').action = '/biblioteca/socios/' + item.id;
        document.getElementById('editIdEstudiante').value = item.id_estudiante ?? '';
        document.getElementById('editTipoSocio').value = item.tipo_socio;
        document.getElementById('editCodigoSocio').value = item.codigo_socio;
        document.getElementById('editDni').value = item.dni;
        document.getElementById('editNombres').value = item.nombres;
        document.getElementById('editApellidos').value = item.apellidos;
        document.getElementById('editGrado').value = item.grado ?? '';
        document.getElementById('editSeccion').value = item.seccion ?? '';
        document.getElementById('editTelefono').value = item.telefono ?? '';
        document.getElementById('editEstadoSocio').value = item.estado;
        document.getElementById('editFechaInscripcion').value = item.fecha_inscripcion;
        document.getElementById('editObservacionesSocio').value = item.observaciones ?? '';
        openModal('modalEditarSocio');
    }
</script>
@endpush
@endsection
