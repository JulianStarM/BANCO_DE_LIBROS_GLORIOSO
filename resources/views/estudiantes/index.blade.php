@extends('layouts.app')

@section('title', 'Estudiantes')
@section('page-title', 'Gestión de estudiantes')

@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title d-flex align-items-center gap-2">
                <span class="student-avatar">
                    <i data-lucide="graduation-cap" class="icon"></i>
                </span>
                Estudiantes
            </h1>
            <p class="text-slate-500 small mt-1 mb-0">Administra el registro y datos de los estudiantes</p>
        </div>

        <button type="button" onclick="openModal('modalNuevoEstudiante')" class="btn-app btn-app-primary">
            <i data-lucide="plus" class="icon"></i>
            Nuevo estudiante
        </button>
    </div>
</div>

<!-- Filters -->
<div class="filter-bar">
    <form method="GET" class="row g-3 align-items-end w-100">
        <div class="col-md-4">
            <label class="form-label-app">Búsqueda</label>
            <div class="position-relative">
                <i data-lucide="search" class="icon position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400" style="pointer-events: none;"></i>
                <input type="text" name="q" value="{{ $q }}" class="form-control-app" style="padding-left: 2.25rem;" placeholder="Nombres, apellidos, DNI, código...">
            </div>
        </div>

        <div class="col-md-2">
            <label class="form-label-app">Nivel</label>
            <select name="nivel" class="form-select-app">
                <option value="">Todos</option>
                @foreach(['Inicial','Primaria','Secundaria'] as $n)
                    <option value="{{ strtolower($n) }}" @selected($nivel === strtolower($n) || $nivel === $n)>{{ $n }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label-app">Grado</label>
            <input type="text" name="grado" value="{{ $grado }}" class="form-control-app" placeholder="Ej. 4">
        </div>

        <div class="col-md-2">
            <label class="form-label-app">Sección</label>
            <input type="text" name="seccion" value="{{ $seccion }}" class="form-control-app" placeholder="Ej. A">
        </div>

        <div class="col-md-2">
            <button type="submit" class="btn-app btn-app-secondary w-100">
                <i data-lucide="filter" class="icon"></i>
                Filtrar
            </button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="data-card">
    <div class="table-responsive-app">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Código / DNI</th>
                    <th>Estudiante</th>
                    <th>Nivel</th>
                    <th class="text-center">G/S</th>
                    <th>Turno</th>
                    <th>Estado</th>
                    <th class="actions-cell">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($estudiantes as $e)
                    <tr>
                        <td>
                            <div class="student-name">{{ $e->codigo_estudiante ?? $e->codigo ?? '-' }}</div>
                            <div class="student-meta">{{ $e->dni ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="student-name">{{ $e->apellidos }}</div>
                            <div class="student-meta">{{ $e->nombres }}</div>
                        </td>
                        <td class="text-slate-600 text-capitalize">{{ $e->nivel }}</td>
                        <td class="text-slate-600 text-center fw-medium">{{ $e->grado }} "{{ $e->seccion }}"</td>
                        <td class="text-slate-600 text-capitalize">{{ $e->turno ?? '-' }}</td>
                        <td>
                            @if($e->estado === 'activo' || $e->estado == 1)
                                <span class="status-badge activo">Activo</span>
                            @else
                                @php
                                    $estadoClass = match($e->estado) {
                                        'retirado' => 'retirado',
                                        'trasladado' => 'trasladado',
                                        'egresado' => 'egresado',
                                        default => 'inactivo',
                                    };
                                @endphp
                                <span class="status-badge {{ $estadoClass }}">{{ ucfirst($e->estado) }}</span>
                            @endif
                        </td>
                        <td class="actions-cell">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('estudiantes.show', $e) }}" class="btn-action btn-action-primary" title="Ver perfil">
                                    <i data-lucide="eye" class="icon"></i>
                                </a>
                                <a href="{{ route('estudiantes.edit', $e) }}" class="btn-action btn-action-primary" title="Editar">
                                    <i data-lucide="pencil" class="icon"></i>
                                </a>
                                <form action="{{ route('estudiantes.destroy', $e) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este estudiante?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action btn-action-danger" title="Eliminar">
                                        <i data-lucide="trash-2" class="icon"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="table-empty">
                                <div class="student-avatar mx-auto mb-3">
                                    <i data-lucide="users" class="icon"></i>
                                </div>
                                <p class="mb-1 fw-semibold text-slate-900">No se encontraron estudiantes</p>
                                <p class="small text-slate-500 mb-0">Ajusta los filtros de búsqueda o registra un nuevo estudiante.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($estudiantes->hasPages())
        <div class="card-footer-base d-flex justify-content-center">
            {{ $estudiantes->links() }}
        </div>
    @endif
</div>

@component('components.modal', ['id' => 'modalNuevoEstudiante', 'title' => 'Nuevo Estudiante', 'size' => 'lg'])
    <form id="formNuevoEstudiante" data-action="{{ route('estudiantes.api.store') }}">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label-app">Código *</label>
                <input type="text" name="codigo_estudiante" class="form-control-app" required>
            </div>
            <div class="col-md-4">
                <label class="form-label-app">DNI *</label>
                <input type="text" name="dni" class="form-control-app" required maxlength="15">
            </div>
            <div class="col-md-4">
                <label class="form-label-app">Fecha nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control-app">
            </div>

            <div class="col-md-6">
                <label class="form-label-app">Nombres *</label>
                <input type="text" name="nombres" class="form-control-app" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-app">Apellidos *</label>
                <input type="text" name="apellidos" class="form-control-app" required>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label-app">Sexo *</label>
                <select name="sexo" class="form-select-app" required>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label-app">Nivel *</label>
                <select name="nivel" class="form-select-app" required>
                    <option value="secundaria">Secundaria</option>
                    <option value="primaria">Primaria</option>
                    <option value="inicial">Inicial</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label-app">Grado *</label>
                <input type="number" name="grado" class="form-control-app" required min="1" max="6">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label-app">Sección *</label>
                <input type="text" name="seccion" class="form-control-app" required maxlength="5" placeholder="A">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label-app">Turno</label>
                <select name="turno" class="form-select-app">
                    <option value="">--</option>
                    <option value="mañana">Mañana</option>
                    <option value="tarde">Tarde</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label-app">Dirección</label>
                <input type="text" name="direccion" class="form-control-app">
            </div>
            <div class="col-md-4">
                <label class="form-label-app">Teléfono</label>
                <input type="text" name="telefono" class="form-control-app">
            </div>
            <div class="col-md-4">
                <label class="form-label-app">Año Escolar (ID)</label>
                <input type="number" name="id_anio_escolar" class="form-control-app" value="1">
            </div>

            <div class="col-12">
                <label class="form-check-app">
                    <input type="checkbox" name="estado" value="1" checked class="form-check-input-app">
                    <span class="form-check-label-app">Estudiante activo</span>
                </label>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-3">
            <button type="button" onclick="closeModal('modalNuevoEstudiante')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="button" id="btnGuardarEstudiante" class="btn-app btn-app-primary btn-submit-modal">
                <i data-lucide="save" class="icon"></i> Guardar Estudiante
            </button>
        </div>
    </form>
@endcomponent
@endsection
