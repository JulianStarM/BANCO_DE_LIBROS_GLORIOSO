@extends('layouts.app')

@section('title', 'Categorías y Áreas Temáticas')
@section('page-title', 'Biblioteca General')

@section('content')
@component('biblioteca.partials.crud-page', [
    'title' => 'Áreas Temáticas',
    'subtitle' => 'Administra las áreas temáticas del catálogo bibliotecario.',
    'icon' => 'tag',
    'createLabel' => 'Nueva área',
    'modalCreateId' => 'modalCrearArea',
    'modalEditId' => 'modalEditarArea',
    'routePrefix' => 'biblioteca.areas',
])
    @slot('filters')
        <div class="col-lg-6">
            <label class="form-label-app">Buscar</label>
            <div class="position-relative">
                <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400">
                    <i data-lucide="search" class="icon"></i>
                </span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control-app ps-5" placeholder="Nombre de área...">
            </div>
        </div>
    @endslot

    @slot('tableHead')
        <th>Nombre</th>
        <th>Código</th>
        <th>Descripción</th>
        <th class="text-center">Estado</th>
    @endslot

    @slot('tableBody')
        @forelse($areas as $area)
            <tr>
                <td class="fw-medium text-slate-900">{{ $area->nombre }}</td>
                <td>{{ $area->codigo ?: '—' }}</td>
                <td>{{ $area->descripcion ?: '—' }}</td>
                <td class="text-center">
                    @if($area->estado)
                        <span class="badge rounded-pill bg-success-subtle text-success">Activo</span>
                    @else
                        <span class="badge rounded-pill bg-secondary-subtle text-secondary">Inactivo</span>
                    @endif
                </td>
                <td class="text-end">
                    <button type="button" onclick="editarRegistromodalEditarArea('{{ route('biblioteca.areas.update', $area) }}', {nombre: '{{ $area->nombre }}', codigo: '{{ $area->codigo }}', descripcion: '{{ $area->descripcion }}', estado: {{ $area->estado ? 1 : 0 }}})" class="btn-action btn-action-secondary" title="Editar">
                        <i data-lucide="pencil" class="icon"></i>
                    </button>
                    <form action="{{ route('biblioteca.areas.destroy', $area) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta área?')">
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
                <td colspan="5" class="text-center py-4 text-slate-500">No hay áreas registradas.</td>
            </tr>
        @endforelse
    @endslot

    @slot('modalCreateBody')
        <div class="form-group-app">
            <label class="form-label-app">Nombre *</label>
            <input type="text" name="nombre" class="form-control-app" required>
        </div>
        <div class="form-group-app">
            <label class="form-label-app">Código</label>
            <input type="text" name="codigo" class="form-control-app">
        </div>
        <div class="form-group-app">
            <label class="form-label-app">Descripción</label>
            <textarea name="descripcion" rows="2" class="form-control-app"></textarea>
        </div>
        <input type="hidden" name="estado" value="0">
        <div class="form-group-app">
            <label class="form-check-app d-flex align-items-center gap-2">
                <input type="checkbox" name="estado" value="1" checked class="form-check-input">
                <span>Activo</span>
            </label>
        </div>
    @endslot

    @slot('modalEditBody')
        <div class="form-group-app">
            <label class="form-label-app">Nombre *</label>
            <input type="text" name="nombre" class="form-control-app" required>
        </div>
        <div class="form-group-app">
            <label class="form-label-app">Código</label>
            <input type="text" name="codigo" class="form-control-app">
        </div>
        <div class="form-group-app">
            <label class="form-label-app">Descripción</label>
            <textarea name="descripcion" rows="2" class="form-control-app"></textarea>
        </div>
        <input type="hidden" name="estado" value="0">
        <div class="form-group-app">
            <label class="form-check-app d-flex align-items-center gap-2">
                <input type="checkbox" name="estado" value="1" class="form-check-input">
                <span>Activo</span>
            </label>
        </div>
    @endslot

    @slot('pagination')
        {{ $areas->links() }}
    @endslot
@endcomponent
@endsection
