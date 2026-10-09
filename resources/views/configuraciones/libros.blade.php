@extends('layouts.app')

@section('title', 'Configuración de Libros')

@section('content')
    <div class="mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3">
            <div>
                <h2 class="h3 fw-bold text-slate-800 mb-1">Configuración de libros</h2>
                <p class="text-sm text-slate-500 mb-0">Administra áreas, editoriales y tipos de material.</p>
            </div>
            <a href="{{ route('configuraciones.index') }}" class="btn-app btn-app-secondary">
                <i data-lucide="arrow-left" class="icon"></i> Volver
            </a>
        </div>
    </div>

    @php
        $tarjetas = [
            'areas' => ['titulo' => 'Áreas', 'icono' => 'book-open', 'color' => 'primary'],
            'editoriales' => ['titulo' => 'Editoriales', 'icono' => 'building-2', 'color' => 'success'],
            'tipos_material' => ['titulo' => 'Tipos de material', 'icono' => 'layers', 'color' => 'warning'],
            'niveles' => ['titulo' => 'Niveles', 'icono' => 'graduation-cap', 'color' => 'info'],
            'grados' => ['titulo' => 'Grados', 'icono' => 'list-ordered', 'color' => 'danger'],
        ];

        function etiquetaTipoMaterial(string $valor): string {
            return ucfirst(str_replace('_', ' ', $valor));
        }
    @endphp

    <div class="row g-4">
        @foreach($tarjetas as $categoria => $info)
            <div class="col-md-6 col-lg-4">
                <div class="card-app">
                    <div class="card-header-base d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-{{ $info['color'] }}-subtle text-{{ $info['color'] }}"
                                 style="width: 2.5rem; height: 2.5rem;">
                                <i data-lucide="{{ $info['icono'] }}" class="icon"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-slate-800 mb-0">{{ $info['titulo'] }}</h5>
                                <p class="text-xs text-slate-500 mb-0">{{ count($config[$categoria]) }} configurados</p>
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
                                    <span class="badge bg-{{ $info['color'] }}-subtle text-{{ $info['color'] }}">
                                        {{ $categoria === 'tipos_material' ? etiquetaTipoMaterial($item['nombre']) : $item['nombre'] }}
                                    </span>
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
                                            <td class="fw-medium text-slate-800">
                                                {{ $categoria === 'tipos_material' ? etiquetaTipoMaterial($item['nombre']) : $item['nombre'] }}
                                            </td>
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

                                                <form action="{{ route('configuraciones.libros.toggle', [$categoria, $item['id']]) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                            class="btn-action {{ $item['estado'] === 'activo' ? 'btn-action-secondary' : 'btn-action-success' }}"
                                                            title="{{ $item['estado'] === 'activo' ? 'Desactivar' : 'Activar' }}">
                                                        <i data-lucide="{{ $item['estado'] === 'activo' ? 'eye-off' : 'eye' }}" class="icon" style="width: 0.875rem; height: 0.875rem;"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('configuraciones.libros.eliminar', [$categoria, $item['id']]) }}"
                                                      method="POST" class="d-inline"
                                                      data-confirm="true"
                                                      data-confirm-title="¿Eliminar valor?"
                                                      data-confirm-message="El valor {{ $categoria === 'tipos_material' ? etiquetaTipoMaterial($item['nombre']) : $item['nombre'] }} será eliminado permanentemente."
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

    @component('components.modal', ['id' => 'modalAgregarItem', 'title' => 'Agregar', 'size' => 'md'])
        <form id="formAgregarItem" method="POST">
            @csrf
            <input type="hidden" id="agregarCategoria" name="categoria">
            <div class="form-group-app">
                <label class="form-label-app">Nombre</label>
                <input type="text" name="nombre" class="form-control-app" required autofocus>
                <p class="form-text-app mt-2 mb-0" id="agregarAyuda"></p>
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
                <p class="form-text-app mt-2 mb-0" id="editarAyuda"></p>
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
            areas: 'Nueva área',
            editoriales: 'Nueva editorial',
            tipos_material: 'Nuevo tipo de material',
            niveles: 'Nuevo nivel',
            grados: 'Nuevo grado',
        };

        const ayudas = {
            tipos_material: 'Use el mismo valor interno del sistema (ej: libro, cuaderno_trabajo).',
            grados: 'Use solo el número (ej: 1, 2, 3).',
        };

        function abrirModalAgregar(categoria) {
            const form = document.getElementById('formAgregarItem');
            form.action = `/configuraciones/libros/${categoria}/agregar`;
            document.getElementById('agregarCategoria').value = categoria;
            document.getElementById('modalAgregarItem-title').textContent = titulos[categoria] || 'Agregar';
            document.getElementById('agregarAyuda').textContent = ayudas[categoria] || '';
            form.reset();
            openModal('modalAgregarItem');
        }

        function abrirModalEditar(categoria, id, nombre) {
            const form = document.getElementById('formEditarItem');
            form.action = `/configuraciones/libros/${categoria}/${id}/actualizar`;
            document.getElementById('editarCategoria').value = categoria;
            document.getElementById('modalEditarItem-title').textContent = 'Editar ' + (titulos[categoria] || 'valor').replace('Nuevo ', '').replace('Nueva ', '').toLowerCase();
            document.getElementById('editarAyuda').textContent = ayudas[categoria] || '';
            document.getElementById('editarNombre').value = nombre;
            openModal('modalEditarItem');
        }
    </script>
@endpush
