@extends('layouts.app')

@section('title', 'Configuración de Estudiantes')

@section('content')
    <div class="mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
            <div>
                <h2 class="h3 fw-bold text-slate-800 mb-1">Configuración de estudiantes</h2>
                <p class="text-sm text-slate-500 mb-0">Administra los valores utilizados por el módulo Estudiantes.</p>
            </div>
            <a href="{{ route('configuraciones.index') }}" class="btn-app btn-app-secondary">
                <i data-lucide="arrow-left" class="icon"></i> Volver
            </a>
        </div>
    </div>

    @php
        $tarjetas = [
            'niveles' => ['titulo' => 'Niveles', 'icono' => 'graduation-cap', 'color' => 'primary'],
            'grados' => ['titulo' => 'Grados', 'icono' => 'layers', 'color' => 'success'],
            'secciones' => ['titulo' => 'Secciones', 'icono' => 'users', 'color' => 'warning'],
            'turnos' => ['titulo' => 'Turnos', 'icono' => 'clock', 'color' => 'info'],
        ];
    @endphp

    <div class="row g-4">
        @foreach($tarjetas as $categoria => $info)
            <div class="col-md-6">
                <div class="card-app">
                    <div class="card-header-base d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-{{ $info['color'] }}-subtle text-{{ $info['color'] }}"
                                 style="width: 2.5rem; height: 2.5rem;">
                                <i data-lucide="{{ $info['icono'] }}" class="icon"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-slate-800 mb-0">{{ $info['titulo'] }}</h5>
                                <p class="text-xs text-slate-500 mb-0">{{ count($config[$categoria]) }} {{ $categoria === 'grados' || $categoria === 'secciones' ? 'configurados' : 'configurados' }}</p>
                            </div>
                        </div>
                        <button type="button" class="btn-app btn-app-primary btn-sm" onclick="abrirModalAgregar('{{ $categoria }}')">
                            <i data-lucide="plus" class="icon"></i> Agregar
                        </button>
                    </div>

                    <div class="p-3">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @forelse($config[$categoria] as $item)
                                @if($item['estado'] === 'activo')
                                    <span class="badge bg-{{ $info['color'] }}-subtle text-{{ $info['color'] }}">{{ $item['nombre'] }}</span>
                                @endif
                            @empty
                                <span class="text-sm text-slate-400">Sin valores configurados</span>
                            @endforelse
                        </div>

                        <div class="table-responsive-app">
                            <table class="table table-app table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th class="text-center" style="width: 100px;">Estado</th>
                                        <th class="text-end" style="width: 140px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($config[$categoria] as $item)
                                        <tr>
                                            <td class="fw-medium text-slate-800">{{ $item['nombre'] }}</td>
                                            <td class="text-center">
                                                @if($item['estado'] === 'activo')
                                                    <span class="badge bg-success-subtle text-success">Activo</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">Inactivo</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button type="button"
                                                        class="btn-action btn-action-secondary"
                                                        onclick="abrirModalEditar('{{ $categoria }}', '{{ $item['id'] }}', '{{ $item['nombre'] }}')"
                                                        title="Editar">
                                                    <i data-lucide="pencil" class="icon" style="width: 0.875rem; height: 0.875rem;"></i>
                                                </button>

                                                <form action="{{ route('configuraciones.estudiantes.toggle', [$categoria, $item['id']]) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                            class="btn-action {{ $item['estado'] === 'activo' ? 'btn-action-secondary' : 'btn-action-success' }}"
                                                            title="{{ $item['estado'] === 'activo' ? 'Desactivar' : 'Activar' }}">
                                                        <i data-lucide="{{ $item['estado'] === 'activo' ? 'eye-off' : 'eye' }}" class="icon" style="width: 0.875rem; height: 0.875rem;"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('configuraciones.estudiantes.eliminar', [$categoria, $item['id']]) }}"
                                                      method="POST" class="d-inline"
                                                      data-confirm="true"
                                                      data-confirm-title="¿Eliminar valor?"
                                                      data-confirm-message="El valor {{ $item['nombre'] }} será eliminado permanentemente."
                                                      data-confirm-text="Eliminar">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action btn-action-danger" title="Eliminar">
                                                        <i data-lucide="trash-2" class="icon" style="width: 0.875rem; height: 0.875rem;"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-slate-400 py-4">
                                                No hay valores configurados
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Formulario de visibles/obligatorios --}}
    <form action="{{ route('configuraciones.estudiantes.update') }}" method="POST" class="mt-4">
        @csrf
        @method('PUT')

        <div class="card-app">
            <div class="card-header-base">
                <div class="d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary"
                         style="width: 2.25rem; height: 2.25rem;">
                        <i data-lucide="form-input" class="icon" style="width: 1.125rem; height: 1.125rem;"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-slate-800 mb-0" style="font-size: 1rem;">Formulario de estudiantes</h5>
                        <p class="text-xs text-slate-500 mb-0">Campos visibles y obligatorios del formulario.</p>
                    </div>
                </div>
            </div>

            <div class="table-responsive-app">
                <table class="table table-app table-sm mb-0">
                    <thead>
                        <tr>
                            <th class="text-sm">Campo</th>
                            <th class="text-sm text-center" style="width: 90px;">Visible</th>
                            <th class="text-sm text-center" style="width: 90px;">Obligatorio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $campos = [
                                'codigo_estudiante' => 'Código de estudiante',
                                'dni' => 'DNI',
                                'nombres' => 'Nombres',
                                'apellidos' => 'Apellidos',
                                'sexo' => 'Sexo',
                                'nivel' => 'Nivel',
                                'grado' => 'Grado',
                                'seccion' => 'Sección',
                                'turno' => 'Turno',
                                'direccion' => 'Dirección',
                                'telefono' => 'Teléfono',
                                'id_anio_escolar' => 'Año Escolar (ID)',
                                'estado' => 'Estado (activo)',
                            ];
                        @endphp
                        @foreach($campos as $key => $label)
                            <tr>
                                <td class="text-sm text-slate-800">{{ $label }}</td>
                                <td class="text-center">
                                    <input type="hidden" name="campos_visibles[{{ $key }}]" value="0">
                                    <input type="checkbox" name="campos_visibles[{{ $key }}]" value="1"
                                        class="cfg-check"
                                        {{ ($config['campos_visibles'][$key] ?? true) ? 'checked' : '' }}
                                        onchange="onVisibleChange('{{ $key }}')">
                                </td>
                                <td class="text-center">
                                    <input type="hidden" name="campos_obligatorios[{{ $key }}]" value="0">
                                    <input type="checkbox" name="campos_obligatorios[{{ $key }}]" value="1"
                                        id="obligatorio-{{ $key }}"
                                        class="cfg-check"
                                        {{ ($config['campos_obligatorios'][$key] ?? false) ? 'checked' : '' }}
                                        {{ ($config['campos_visibles'][$key] ?? true) ? '' : 'disabled' }}>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-top d-flex justify-content-end">
                <button type="submit" class="btn-app btn-app-primary btn-sm">
                    <i data-lucide="save" class="icon"></i> Guardar configuración
                </button>
            </div>
        </div>
    </form>

    @push('styles')
        <style>
            .cfg-check {
                width: 1rem;
                height: 1rem;
                accent-color: var(--color-primary);
                cursor: pointer;
                vertical-align: middle;
            }
            .cfg-check:disabled {
                cursor: not-allowed;
                opacity: 0.5;
            }
        </style>
    @endpush

    @component('components.modal', ['id' => 'modalAgregarItem', 'title' => 'Agregar', 'size' => 'md'])
        <form id="formAgregarItem" method="POST">
            @csrf
            <input type="hidden" id="agregarCategoria" name="categoria">
            <div class="form-group-app">
                <label class="form-label-app">Nombre</label>
                <input type="text" name="nombre" class="form-control-app" required autofocus>
            </div>
            <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn-app btn-app-secondary" onclick="closeModal('modalAgregarItem')">Cancelar</button>
                <button type="submit" class="btn-app btn-app-primary">
                    <i data-lucide="save" class="icon"></i> Guardar
                </button>
            </div>
        </form>
    @endcomponent

    @component('components.modal', ['id' => 'modalEditarItem', 'title' => 'Editar', 'size' => 'md'])
        <form id="formEditarItem" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="editarCategoria" name="categoria">
            <div class="form-group-app">
                <label class="form-label-app">Nombre</label>
                <input type="text" id="editarNombre" name="nombre" class="form-control-app" required autofocus>
            </div>
            <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
                <button type="button" class="btn-app btn-app-secondary" onclick="closeModal('modalEditarItem')">Cancelar</button>
                <button type="submit" class="btn-app btn-app-primary">
                    <i data-lucide="save" class="icon"></i> Guardar
                </button>
            </div>
        </form>
    @endcomponent
@endsection

@push('scripts')
    <script>
        const titulos = {
            niveles: 'Nuevo nivel',
            grados: 'Nuevo grado',
            secciones: 'Nueva sección',
            turnos: 'Nuevo turno',
        };

        function abrirModalAgregar(categoria) {
            const form = document.getElementById('formAgregarItem');
            form.action = `/configuraciones/estudiantes/${categoria}/agregar`;
            document.getElementById('agregarCategoria').value = categoria;
            document.getElementById('modalAgregarItem-title').textContent = titulos[categoria] || 'Agregar';
            form.reset();
            openModal('modalAgregarItem');
        }

        function abrirModalEditar(categoria, id, nombre) {
            const form = document.getElementById('formEditarItem');
            form.action = `/configuraciones/estudiantes/${categoria}/${id}/actualizar`;
            document.getElementById('editarCategoria').value = categoria;
            document.getElementById('modalEditarItem-title').textContent = 'Editar ' + (titulos[categoria] || 'valor').replace('Nuevo ', '').toLowerCase();
            document.getElementById('editarNombre').value = nombre;
            openModal('modalEditarItem');
        }

        function onVisibleChange(key) {
            const visibleCheckbox = document.querySelector(`input[name="campos_visibles[${key}]"]`);
            const obligatorioCheckbox = document.getElementById('obligatorio-' + key);
            obligatorioCheckbox.disabled = !visibleCheckbox.checked;
            if (!visibleCheckbox.checked) {
                obligatorioCheckbox.checked = false;
            }
        }
    </script>
@endpush
