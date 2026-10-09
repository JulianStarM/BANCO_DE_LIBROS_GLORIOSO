@extends('layouts.app')

@section('title', 'Estudiantes')
@section('page-title', 'Gestión de estudiantes')

@section('content')
@php
$v = $configEst['campos_visibles'] ?? [];
$o = $configEst['campos_obligatorios'] ?? [];
$req = fn($k) => ($o[$k] ?? false) ? ' *' : '';
$attrReq = fn($k) => ($o[$k] ?? false) ? ' required' : '';
@endphp
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
                @foreach($configEst['niveles'] as $n)
                    <option value="{{ $n }}" @selected($nivel === $n)>{{ ucfirst($n) }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label-app">Grado</label>
            <select name="grado" class="form-select-app">
                <option value="">Todos</option>
                @foreach($configEst['grados'] as $g)
                    <option value="{{ $g }}" @selected($grado == $g)>{{ $g }}°</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label-app">Sección</label>
            <select name="seccion" class="form-select-app">
                <option value="">Todos</option>
                @foreach($configEst['secciones'] as $s)
                    <option value="{{ $s }}" @selected($seccion === $s)>{{ $s }}</option>
                @endforeach
            </select>
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
                                <button type="button"
                                        class="btn-action btn-action-primary"
                                        title="Ver perfil"
                                        onclick="verEstudiante({{ json_encode([
                                            'id' => $e->id,
                                            'codigo_estudiante' => $e->codigo_estudiante,
                                            'dni' => $e->dni,
                                            'nombres' => $e->nombres,
                                            'apellidos' => $e->apellidos,
                                            'sexo' => $e->sexo,
                                            'fecha_nacimiento' => $e->fecha_nacimiento,
                                            'grado' => $e->grado,
                                            'seccion' => $e->seccion,
                                            'nivel' => $e->nivel,
                                            'turno' => $e->turno,
                                            'direccion' => $e->direccion,
                                            'telefono' => $e->telefono,
                                            'estado' => $e->estado,
                                            'id_anio_escolar' => $e->id_anio_escolar,
                                        ]) }})">
                                    <i data-lucide="eye" class="icon"></i>
                                </button>
                                <button type="button"
                                        class="btn-action btn-action-warning"
                                        title="Editar"
                                        onclick="editarEstudiante({{ json_encode([
                                            'id' => $e->id,
                                            'codigo_estudiante' => $e->codigo_estudiante,
                                            'dni' => $e->dni,
                                            'nombres' => $e->nombres,
                                            'apellidos' => $e->apellidos,
                                            'sexo' => $e->sexo,
                                            'fecha_nacimiento' => $e->fecha_nacimiento,
                                            'grado' => $e->grado,
                                            'seccion' => $e->seccion,
                                            'nivel' => $e->nivel,
                                            'turno' => $e->turno,
                                            'direccion' => $e->direccion,
                                            'telefono' => $e->telefono,
                                            'estado' => $e->estado,
                                            'id_anio_escolar' => $e->id_anio_escolar,
                                        ]) }})">
                                    <i data-lucide="pencil" class="icon"></i>
                                </button>
                                <button type="button"
                                        class="btn-action btn-action-danger"
                                        title="Eliminar"
                                        onclick="abrirModalEliminar({{ $e->id }}, '{{ $e->apellidos }}, {{ $e->nombres }}')">
                                    <i data-lucide="trash-2" class="icon"></i>
                                </button>
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
        <div class="mt-3">
            {{ $estudiantes->links('pagination::bootstrap-5') }}
        </div>
    @endif

{{-- Modal Ver Estudiante --}}
@component('components.modal', ['id' => 'modalVerEstudiante', 'title' => 'Perfil del Estudiante', 'subtitle' => 'Información personal y académica', 'icon' => 'user', 'size' => 'md'])
    <div class="detail-panel">
        <div class="detail-section">
            <h4 class="detail-section-title">Información personal</h4>
            <div class="detail-grid">
                <div class="detail-field">
                    <span class="detail-label">Código</span>
                    <span class="detail-value" id="verCodigo">—</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">DNI</span>
                    <span class="detail-value" id="verDni">—</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Apellidos</span>
                    <span class="detail-value" id="verApellidos">—</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Nombres</span>
                    <span class="detail-value" id="verNombres">—</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Sexo</span>
                    <span class="detail-value" id="verSexo">—</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Estado</span>
                    <span class="detail-value" id="verEstadoBadge"><span class="status-badge inactivo">—</span></span>
                </div>
            </div>
        </div>

        <div class="detail-section">
            <h4 class="detail-section-title">Información académica</h4>
            <div class="detail-grid detail-grid-3">
                <div class="detail-field">
                    <span class="detail-label">Nivel</span>
                    <span class="detail-value text-capitalize" id="verNivel">—</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Grado</span>
                    <span class="detail-value" id="verGrado">—</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Sección</span>
                    <span class="detail-value" id="verSeccion">—</span>
                </div>
            </div>
            <div class="detail-grid">
                <div class="detail-field">
                    <span class="detail-label">Turno</span>
                    <span class="detail-value text-capitalize" id="verTurno">—</span>
                </div>
                <div class="detail-field">
                    <span class="detail-label">Dirección</span>
                    <span class="detail-value" id="verDireccion">—</span>
                </div>
                <div class="detail-field full-width">
                    <span class="detail-label">Teléfono</span>
                    <span class="detail-value" id="verTelefono">—</span>
                </div>
            </div>
        </div>
    </div>

    @slot('footer')
        <button type="button" onclick="closeModal('modalVerEstudiante')" class="btn-app btn-app-secondary">Cerrar</button>
    @endslot
@endcomponent

{{-- Modal Editar Estudiante --}}
@component('components.modal', ['id' => 'modalEditarEstudiante', 'title' => 'Editar Estudiante', 'icon' => 'pencil', 'size' => 'lg'])
    <form id="formEditarEstudiante" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            @if($v['codigo_estudiante'] ?? true)
                <div class="col-md-6">
                    <label class="form-label-app">Código{{ $req('codigo_estudiante') }}</label>
                    <input type="text" name="codigo_estudiante" id="editCodigoEstudiante" class="form-control-app"{{ $attrReq('codigo_estudiante') }}>
                </div>
            @endif

            @if($v['dni'] ?? true)
                <div class="col-md-6">
                    <label class="form-label-app">DNI{{ $req('dni') }}</label>
                    <input type="text" name="dni" id="editDni" class="form-control-app" maxlength="15"{{ $attrReq('dni') }}>
                </div>
            @endif

            @if($v['nombres'] ?? true)
                <div class="col-md-6">
                    <label class="form-label-app">Nombres{{ $req('nombres') }}</label>
                    <input type="text" name="nombres" id="editNombres" class="form-control-app"{{ $attrReq('nombres') }}>
                </div>
            @endif

            @if($v['apellidos'] ?? true)
                <div class="col-md-6">
                    <label class="form-label-app">Apellidos{{ $req('apellidos') }}</label>
                    <input type="text" name="apellidos" id="editApellidos" class="form-control-app"{{ $attrReq('apellidos') }}>
                </div>
            @endif

            @if($v['sexo'] ?? true)
                <div class="col-md-4">
                    <label class="form-label-app">Sexo{{ $req('sexo') }}</label>
                    <select name="sexo" id="editSexo" class="form-select-app"{{ $attrReq('sexo') }}>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
            @endif

            @if($v['nivel'] ?? true)
                <div class="col-md-4">
                    <label class="form-label-app">Nivel{{ $req('nivel') }}</label>
                    <select name="nivel" id="editNivel" class="form-select-app"{{ $attrReq('nivel') }}>
                        @foreach($configEst['niveles'] as $n)
                            <option value="{{ $n }}">{{ ucfirst($n) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($v['grado'] ?? true)
                <div class="col-md-4">
                    <label class="form-label-app">Grado{{ $req('grado') }}</label>
                    <select name="grado" id="editGrado" class="form-select-app"{{ $attrReq('grado') }}>
                        @foreach($configEst['grados'] as $g)
                            <option value="{{ $g }}">{{ $g }}°</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($v['seccion'] ?? true)
                <div class="col-md-4">
                    <label class="form-label-app">Sección{{ $req('seccion') }}</label>
                    <select name="seccion" id="editSeccion" class="form-select-app"{{ $attrReq('seccion') }}>
                        @foreach($configEst['secciones'] as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($v['turno'] ?? true)
                <div class="col-md-4">
                    <label class="form-label-app">Turno{{ $req('turno') }}</label>
                    <select name="turno" id="editTurno" class="form-select-app"{{ $attrReq('turno') }}>
                        <option value="">--</option>
                        @foreach($configEst['turnos'] as $t)
                            <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($v['id_anio_escolar'] ?? true)
                <div class="col-md-4">
                    <label class="form-label-app">Año Escolar (ID){{ $req('id_anio_escolar') }}</label>
                    <input type="number" name="id_anio_escolar" id="editIdAnioEscolar" class="form-control-app"{{ $attrReq('id_anio_escolar') }}>
                </div>
            @endif

            @if($v['direccion'] ?? true)
                <div class="col-md-6">
                    <label class="form-label-app">Dirección{{ $req('direccion') }}</label>
                    <input type="text" name="direccion" id="editDireccion" class="form-control-app"{{ $attrReq('direccion') }}>
                </div>
            @endif

            @if($v['telefono'] ?? true)
                <div class="col-md-6">
                    <label class="form-label-app">Teléfono{{ $req('telefono') }}</label>
                    <input type="text" name="telefono" id="editTelefono" class="form-control-app"{{ $attrReq('telefono') }}>
                </div>
            @endif

            @if($v['estado'] ?? true)
                <div class="col-12">
                    <label class="form-check-app">
                        <input type="checkbox" name="estado" id="editEstado" value="1" checked class="form-check-input-app">
                        <span class="form-check-label-app">Estudiante activo</span>
                    </label>
                </div>
            @endif
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-3">
            <button type="button" onclick="closeModal('modalEditarEstudiante')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">
                <i data-lucide="save" class="icon"></i> Guardar cambios
            </button>
        </div>
    </form>
@endcomponent

{{-- Formularios ocultos para eliminación --}}
<form id="formEliminarEstudiante" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<form id="formForzarEliminar" method="POST" class="d-none">
    @csrf
    @method('DELETE')
    <input type="hidden" name="force" value="1">
</form>

@component('components.modal', ['id' => 'modalNuevoEstudiante', 'title' => 'Nuevo Estudiante', 'size' => 'lg'])
    <form id="formNuevoEstudiante" data-action="{{ route('estudiantes.api.store') }}">
        <div class="row g-3">
            @if($v['codigo_estudiante'] ?? true)
            <div class="col-md-6">
                <label class="form-label-app">Código{{ $req('codigo_estudiante') }}</label>
                <input type="text" name="codigo_estudiante" class="form-control-app"{{ $attrReq('codigo_estudiante') }}>
            </div>
            @endif

            @if($v['dni'] ?? true)
            <div class="col-md-6">
                <label class="form-label-app">DNI{{ $req('dni') }}</label>
                <input type="text" name="dni" class="form-control-app" maxlength="15"{{ $attrReq('dni') }}>
            </div>
            @endif

            @if($v['nombres'] ?? true)
            <div class="col-md-6">
                <label class="form-label-app">Nombres{{ $req('nombres') }}</label>
                <input type="text" name="nombres" class="form-control-app"{{ $attrReq('nombres') }}>
            </div>
            @endif

            @if($v['apellidos'] ?? true)
            <div class="col-md-6">
                <label class="form-label-app">Apellidos{{ $req('apellidos') }}</label>
                <input type="text" name="apellidos" class="form-control-app"{{ $attrReq('apellidos') }}>
            </div>
            @endif

            @if($v['sexo'] ?? true)
            <div class="col-6 col-md-2">
                <label class="form-label-app">Sexo{{ $req('sexo') }}</label>
                <select name="sexo" class="form-select-app"{{ $attrReq('sexo') }}>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>
            </div>
            @endif

            @if($v['nivel'] ?? true)
            <div class="col-6 col-md-2">
                <label class="form-label-app">Nivel{{ $req('nivel') }}</label>
                <select name="nivel" class="form-select-app"{{ $attrReq('nivel') }}>
                    @foreach($configEst['niveles'] as $n)
                        <option value="{{ $n }}">{{ ucfirst($n) }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($v['grado'] ?? true)
            <div class="col-6 col-md-2">
                <label class="form-label-app">Grado{{ $req('grado') }}</label>
                <select name="grado" class="form-select-app"{{ $attrReq('grado') }}>
                    @foreach($configEst['grados'] as $g)
                        <option value="{{ $g }}">{{ $g }}°</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($v['seccion'] ?? true)
            <div class="col-6 col-md-2">
                <label class="form-label-app">Sección{{ $req('seccion') }}</label>
                <select name="seccion" class="form-select-app"{{ $attrReq('seccion') }}>
                    @foreach($configEst['secciones'] as $s)
                        <option value="{{ $s }}">{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($v['turno'] ?? true)
            <div class="col-6 col-md-2">
                <label class="form-label-app">Turno{{ $req('turno') }}</label>
                <select name="turno" class="form-select-app"{{ $attrReq('turno') }}>
                    <option value="">--</option>
                    @foreach($configEst['turnos'] as $t)
                        <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            @if($v['direccion'] ?? true)
            <div class="col-md-4">
                <label class="form-label-app">Dirección{{ $req('direccion') }}</label>
                <input type="text" name="direccion" class="form-control-app"{{ $attrReq('direccion') }}>
            </div>
            @endif

            @if($v['telefono'] ?? true)
            <div class="col-md-4">
                <label class="form-label-app">Teléfono{{ $req('telefono') }}</label>
                <input type="text" name="telefono" class="form-control-app"{{ $attrReq('telefono') }}>
            </div>
            @endif

            @if($v['id_anio_escolar'] ?? true)
            <div class="col-md-4">
                <label class="form-label-app">Año Escolar (ID){{ $req('id_anio_escolar') }}</label>
                <input type="number" name="id_anio_escolar" class="form-control-app" value="1"{{ $attrReq('id_anio_escolar') }}>
            </div>
            @endif

            @if($v['estado'] ?? true)
            <div class="col-12">
                <label class="form-check-app">
                    <input type="checkbox" name="estado" value="1" checked class="form-check-input-app">
                    <span class="form-check-label-app">Estudiante activo</span>
                </label>
            </div>
            @endif
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-3">
            <button type="button" onclick="closeModal('modalNuevoEstudiante')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="button" id="btnGuardarEstudiante" onclick="guardarEstudianteInline()" class="btn-app btn-app-primary btn-submit-modal">
                <i data-lucide="save" class="icon"></i> Guardar Estudiante
            </button>
        </div>
    </form>
@endcomponent

@push('scripts')
<script>
    function verEstudiante(est) {
        document.getElementById('verCodigo').textContent = est.codigo_estudiante ?? '—';
        document.getElementById('verDni').textContent = est.dni ?? '—';
        document.getElementById('verApellidos').textContent = est.apellidos ?? '—';
        document.getElementById('verNombres').textContent = est.nombres ?? '—';
        document.getElementById('verSexo').textContent = est.sexo === 'M' ? 'Masculino' : (est.sexo === 'F' ? 'Femenino' : '—');

        const estadoBadge = document.getElementById('verEstadoBadge');
        const estadoText = (est.estado === 'activo' || est.estado == 1) ? 'Activo' : (est.estado ? est.estado.charAt(0).toUpperCase() + est.estado.slice(1) : 'Inactivo');
        let estadoClass = 'inactivo';
        if (est.estado === 'activo' || est.estado == 1) {
            estadoClass = 'activo';
        } else if (est.estado === 'retirado') {
            estadoClass = 'retirado';
        } else if (est.estado === 'trasladado') {
            estadoClass = 'trasladado';
        } else if (est.estado === 'egresado') {
            estadoClass = 'egresado';
        }
        estadoBadge.innerHTML = '<span class="status-badge ' + estadoClass + '">' + estadoText + '</span>';

        document.getElementById('verNivel').textContent = est.nivel ?? '—';
        document.getElementById('verGrado').textContent = est.grado ?? '—';
        document.getElementById('verSeccion').textContent = est.seccion ?? '—';
        document.getElementById('verTurno').textContent = est.turno ?? '—';
        document.getElementById('verDireccion').textContent = est.direccion ?? '—';
        document.getElementById('verTelefono').textContent = est.telefono ?? '—';
        openModal('modalVerEstudiante');
    }

    function editarEstudiante(est) {
        document.getElementById('formEditarEstudiante').action = `/estudiantes/${est.id}`;
        document.getElementById('editCodigoEstudiante').value = est.codigo_estudiante ?? '';
        document.getElementById('editDni').value = est.dni ?? '';
        document.getElementById('editNombres').value = est.nombres ?? '';
        document.getElementById('editApellidos').value = est.apellidos ?? '';
        document.getElementById('editSexo').value = est.sexo ?? 'M';
        document.getElementById('editGrado').value = est.grado ?? '';
        document.getElementById('editSeccion').value = est.seccion ?? '';
        document.getElementById('editNivel').value = est.nivel ?? 'secundaria';
        document.getElementById('editTurno').value = est.turno ?? '';
        document.getElementById('editDireccion').value = est.direccion ?? '';
        document.getElementById('editTelefono').value = est.telefono ?? '';
        document.getElementById('editIdAnioEscolar').value = est.id_anio_escolar ?? '1';
        document.getElementById('editEstado').checked = (est.estado === 'activo' || est.estado == 1);
        openModal('modalEditarEstudiante');
    }

    function abrirModalEliminar(id, nombre) {
        const form = document.getElementById('formEliminarEstudiante');
        form.action = `/estudiantes/${id}`;

        window.showConfirmModal({
            title: '¿Eliminar estudiante?',
            message: `¿Está seguro de eliminar al estudiante ${nombre}? Esta acción no se puede deshacer si no tiene registros vinculados.`,
            confirmText: 'Sí, eliminar',
            cancelText: 'Cancelar',
            onConfirm: () => form.submit(),
        });
    }

    @if(session('error') && session('estudiante_para_forzar'))
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formForzarEliminar');
            form.action = `/estudiantes/{{ session('estudiante_para_forzar') }}`;
            const mensaje = @json(str_replace("\n", "\n", session('error')));

            window.showFeedbackModal({
                type: 'warning',
                title: 'No se puede eliminar',
                message: `Este estudiante tiene registros vinculados. ${mensaje} Si continúa, se eliminarán TODOS los registros relacionados de forma permanente.`,
                confirmText: 'Sí, eliminar todo',
                cancelText: 'Cancelar',
                onConfirm: () => form.submit(),
            });
        });
    @endif

    function guardarEstudianteInline() {
        console.log('[DEBUG] === INICIO guardarEstudianteInline ===');
        try {
            const form = document.getElementById('formNuevoEstudiante');
            const btn = document.getElementById('btnGuardarEstudiante');
            const url = form?.getAttribute('data-action');

            console.log('[DEBUG] form:', form);
            console.log('[DEBUG] url:', url);
            console.log('[DEBUG] submitModalForm exists:', typeof window.submitModalForm);

            if (!form || !url) {
                window.showToast && window.showToast('Error: no se encontró el formulario o la URL.', 'error');
                console.error('[DEBUG] Form o URL faltante');
                return;
            }

            const formData = new FormData(form);
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="icon spin"></i> Guardando...';
            window.initLucide && window.initLucide();

            form.querySelectorAll('.field-error').forEach(e => e.remove());
            form.querySelectorAll('.is-invalid').forEach(e => e.classList.remove('is-invalid'));

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            console.log('[DEBUG] CSRF token:', csrfToken ? 'OK' : 'VACIO');

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            })
            .then(r => {
                console.log('[DEBUG] response status:', r.status);
                return r.text().then(text => {
                    console.log('[DEBUG] raw response:', text.substring(0, 500));
                    try { return JSON.parse(text); }
                    catch(e) { throw new Error('Respuesta no es JSON: ' + text.substring(0,200)); }
                });
            })
            .then(data => {
                console.log('[DEBUG] parsed data:', data);
                if (data.success) {
                    window.showToast && window.showToast(data.message, 'success');
                    window.closeModal && window.closeModal('modalNuevoEstudiante');
                    form.reset();
                    // Persistir toast para que se muestre después del reload
                    localStorage.setItem('app_toast', JSON.stringify({message: data.message, type: 'success'}));
                    setTimeout(() => location.reload(), 800);
                } else {
                    if (data.errors) {
                        Object.entries(data.errors).forEach(([field, messages]) => {
                            let input = form.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                const err = document.createElement('p');
                                err.className = 'field-error form-error-app';
                                err.textContent = messages[0];
                                input.parentElement.appendChild(err);
                            }
                        });
                    }
                    window.showToast && window.showToast(data.message || 'Error al procesar.', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    window.initLucide && window.initLucide();
                }
            })
            .catch(err => {
                console.error('[DEBUG] fetch/parsing error:', err);
                window.showToast && window.showToast('Error: ' + (err.message || 'de conexión'), 'error');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                window.initLucide && window.initLucide();
            });
        } catch (e) {
            console.error('[DEBUG] Excepción síncrona:', e);
            const btn = document.getElementById('btnGuardarEstudiante');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i data-lucide="save" class="icon"></i> Guardar Estudiante';
                window.initLucide && window.initLucide();
            }
            window.showToast && window.showToast('Error inesperado: ' + e.message, 'error');
        }
    }
</script>
@endpush
@endsection
