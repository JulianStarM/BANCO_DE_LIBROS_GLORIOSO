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
        <div class="card-footer-base d-flex justify-content-center">
            {{ $estudiantes->links() }}
        </div>
    @endif
</div>

{{-- Modal Ver Estudiante --}}
@component('components.modal', ['id' => 'modalVerEstudiante', 'title' => 'Perfil del Estudiante', 'size' => 'md'])
    <div class="row g-3">
        <div class="col-md-6">
            <p class="small text-slate-500 mb-0">Código</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verCodigo">—</p>
        </div>
        <div class="col-md-6">
            <p class="small text-slate-500 mb-0">DNI</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verDni">—</p>
        </div>
        <div class="col-md-6">
            <p class="small text-slate-500 mb-0">Apellidos</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verApellidos">—</p>
        </div>
        <div class="col-md-6">
            <p class="small text-slate-500 mb-0">Nombres</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verNombres">—</p>
        </div>
        <div class="col-md-4">
            <p class="small text-slate-500 mb-0">Sexo</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verSexo">—</p>
        </div>
        <div class="col-md-4">
            <p class="small text-slate-500 mb-0">Estado</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verEstado">—</p>
        </div>
        <div class="col-md-4">
            <p class="small text-slate-500 mb-0">Nivel</p>
            <p class="fw-semibold text-slate-800 mb-0 text-capitalize" id="verNivel">—</p>
        </div>
        <div class="col-md-3">
            <p class="small text-slate-500 mb-0">Grado</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verGrado">—</p>
        </div>
        <div class="col-md-3">
            <p class="small text-slate-500 mb-0">Sección</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verSeccion">—</p>
        </div>
        <div class="col-md-3">
            <p class="small text-slate-500 mb-0">Turno</p>
            <p class="fw-semibold text-slate-800 mb-0 text-capitalize" id="verTurno">—</p>
        </div>
        <div class="col-md-6">
            <p class="small text-slate-500 mb-0">Dirección</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verDireccion">—</p>
        </div>
        <div class="col-md-6">
            <p class="small text-slate-500 mb-0">Teléfono</p>
            <p class="fw-semibold text-slate-800 mb-0" id="verTelefono">—</p>
        </div>
    </div>
@endcomponent

{{-- Modal Editar Estudiante --}}
@component('components.modal', ['id' => 'modalEditarEstudiante', 'title' => 'Editar Estudiante', 'size' => 'lg'])
    <form id="formEditarEstudiante" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label-app">Código</label>
                <input type="text" name="codigo_estudiante" id="editCodigoEstudiante" class="form-control-app">
            </div>
            <div class="col-md-6">
                <label class="form-label-app">DNI *</label>
                <input type="text" name="dni" id="editDni" class="form-control-app" required maxlength="15">
            </div>

            <div class="col-md-6">
                <label class="form-label-app">Nombres *</label>
                <input type="text" name="nombres" id="editNombres" class="form-control-app" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-app">Apellidos *</label>
                <input type="text" name="apellidos" id="editApellidos" class="form-control-app" required>
            </div>

            <div class="col-6 col-md-2">
                <label class="form-label-app">Sexo *</label>
                <select name="sexo" id="editSexo" class="form-select-app" required>
                    <option value="M">Masculino</option>
                    <option value="F">Femenino</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label-app">Nivel *</label>
                <select name="nivel" id="editNivel" class="form-select-app" required>
                    <option value="secundaria">Secundaria</option>
                    <option value="primaria">Primaria</option>
                    <option value="inicial">Inicial</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label-app">Grado *</label>
                <input type="number" name="grado" id="editGrado" class="form-control-app" required min="1" max="6">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label-app">Sección *</label>
                <input type="text" name="seccion" id="editSeccion" class="form-control-app" required maxlength="5">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label-app">Turno</label>
                <select name="turno" id="editTurno" class="form-select-app">
                    <option value="">--</option>
                    <option value="mañana">Mañana</option>
                    <option value="tarde">Tarde</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label-app">Dirección</label>
                <input type="text" name="direccion" id="editDireccion" class="form-control-app">
            </div>
            <div class="col-md-4">
                <label class="form-label-app">Teléfono</label>
                <input type="text" name="telefono" id="editTelefono" class="form-control-app">
            </div>
            <div class="col-md-4">
                <label class="form-label-app">Año Escolar (ID)</label>
                <input type="number" name="id_anio_escolar" id="editIdAnioEscolar" class="form-control-app">
            </div>

            <div class="col-12">
                <label class="form-check-app">
                    <input type="checkbox" name="estado" id="editEstado" value="1" class="form-check-input-app">
                    <span class="form-check-label-app">Estudiante activo</span>
                </label>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-3">
            <button type="button" onclick="closeModal('modalEditarEstudiante')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">
                <i data-lucide="save" class="icon"></i> Guardar cambios
            </button>
        </div>
    </form>
@endcomponent

{{-- Modal confirmación eliminar --}}
@component('components.modal', ['id' => 'modalEliminarEstudiante', 'title' => 'Confirmar eliminación', 'size' => 'md'])
    <p class="text-slate-700 mb-0">¿Está seguro de eliminar al estudiante <strong id="lblNombreEliminar" class="text-slate-900"></strong>?</p>
    <p class="small text-slate-500 mt-2 mb-0">Esta acción no se puede deshacer si el estudiante no tiene registros vinculados.</p>
    @slot('footer')
        <form id="formEliminarEstudiante" method="POST" class="d-flex gap-2 w-100 justify-content-end">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeModal('modalEliminarEstudiante')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-danger">
                <i data-lucide="trash-2" class="icon"></i> Sí, eliminar
            </button>
        </form>
    @endslot
@endcomponent

{{-- Modal forzar eliminación (cuando hay FK constraints) --}}
@component('components.modal', ['id' => 'modalForzarEliminar', 'title' => 'No se puede eliminar', 'size' => 'md'])
    <div class="alert alert-warning d-flex align-items-start gap-2 small">
        <i data-lucide="alert-triangle" class="icon flex-shrink-0 mt-1"></i>
        <div>
            <p class="fw-semibold mb-1">Este estudiante tiene registros vinculados</p>
            <p class="mb-0" id="lblErrorForzar"></p>
        </div>
    </div>
    <p class="small text-danger fw-semibold mt-2 mb-0">Si continúa, se eliminarán TODOS los registros relacionados de forma permanente.</p>
    @slot('footer')
        <form id="formForzarEliminar" method="POST" class="d-flex gap-2 w-100 justify-content-end">
            @csrf
            @method('DELETE')
            <input type="hidden" name="force" value="1">
            <button type="button" onclick="closeModal('modalForzarEliminar')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-danger">
                <i data-lucide="trash-2" class="icon"></i> Sí, eliminar todo
            </button>
        </form>
    @endslot
@endcomponent

@component('components.modal', ['id' => 'modalNuevoEstudiante', 'title' => 'Nuevo Estudiante', 'size' => 'lg'])
    <form id="formNuevoEstudiante" data-action="{{ route('estudiantes.api.store') }}">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label-app">Código</label>
                <input type="text" name="codigo_estudiante" class="form-control-app">
            </div>
            <div class="col-md-6">
                <label class="form-label-app">DNI *</label>
                <input type="text" name="dni" class="form-control-app" required maxlength="15">
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
        document.getElementById('verEstado').textContent = (est.estado === 'activo' || est.estado == 1) ? 'Activo' : (est.estado ? est.estado.charAt(0).toUpperCase() + est.estado.slice(1) : 'Inactivo');
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
        document.getElementById('lblNombreEliminar').textContent = nombre;
        document.getElementById('formEliminarEstudiante').action = `/estudiantes/${id}`;
        openModal('modalEliminarEstudiante');
    }

    @if(session('error') && session('estudiante_para_forzar'))
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('lblErrorForzar').innerText = @json(str_replace("\n", "\n", session('error')));
            document.getElementById('formForzarEliminar').action = `/estudiantes/{{ session('estudiante_para_forzar') }}`;
            openModal('modalForzarEliminar');
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
