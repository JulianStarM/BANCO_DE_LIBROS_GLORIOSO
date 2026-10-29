@extends('layouts.app')

@section('title', 'Editoriales')
@section('page-title', 'Biblioteca General')

@section('content')
@component('biblioteca.partials.crud-page', [
    'title' => 'Editoriales',
    'subtitle' => 'Administra las editoriales del catálogo bibliotecario.',
    'icon' => 'building',
    'createLabel' => 'Nueva editorial',
    'modalCreateId' => 'modalCrearEditorial',
    'modalEditId' => 'modalEditarEditorial',
    'routePrefix' => 'biblioteca.editoriales',
])
    @slot('filters')
        <div class="col-lg-6">
            <label class="form-label-app">Buscar</label>
            <div class="position-relative">
                <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400">
                    <i data-lucide="search" class="icon"></i>
                </span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control-app ps-5" placeholder="Nombre de editorial...">
            </div>
        </div>
    @endslot

    @slot('tableHead')
        <th>Nombre</th>
        <th>País</th>
        <th class="text-center">Estado</th>
    @endslot

    @slot('tableBody')
        @forelse($editoriales as $editorial)
            <tr>
                <td class="fw-medium text-slate-900">{{ $editorial->nombre }}</td>
                <td>{{ $editorial->pais ?: '—' }}</td>
                <td class="text-center">
                    @if($editorial->estado)
                        <span class="badge rounded-pill bg-success-subtle text-success">Activo</span>
                    @else
                        <span class="badge rounded-pill bg-secondary-subtle text-secondary">Inactivo</span>
                    @endif
                </td>
                <td class="text-end">
                    <button type="button" onclick="editarRegistromodalEditarEditorial('{{ route('biblioteca.editoriales.update', $editorial) }}', {nombre: '{{ $editorial->nombre }}', pais: '{{ $editorial->pais }}', estado: {{ $editorial->estado ? 1 : 0 }}})" class="btn-action btn-action-secondary" title="Editar">
                        <i data-lucide="pencil" class="icon"></i>
                    </button>
                    <form action="{{ route('biblioteca.editoriales.destroy', $editorial) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta editorial?')">
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
                <td colspan="4" class="text-center py-4 text-slate-500">No hay editoriales registradas.</td>
            </tr>
        @endforelse
    @endslot

    @slot('modalCreateBody')
        <div class="form-group-app">
            <label class="form-label-app">Nombre *</label>
            <input type="text" name="nombre" class="form-control-app" required>
        </div>
        <div class="form-group-app">
            <label class="form-label-app">País</label>
            <input type="text" name="pais" class="form-control-app">
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
            <label class="form-label-app">País</label>
            <input type="text" name="pais" class="form-control-app">
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
        {{ $editoriales->links() }}
    @endslot
@endcomponent
@endsection
