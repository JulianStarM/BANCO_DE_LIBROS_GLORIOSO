@extends('layouts.app')

@section('title', 'Catálogo de Libros')
@section('page-title', 'Biblioteca General')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="h3 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-1">
            <span class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success" style="width: 2.5rem; height: 2.5rem; font-size: 1.125rem;">
                <i data-lucide="book" class="icon"></i>
            </span>
            Catálogo de Libros
        </h2>
        <p class="text-sm text-slate-500 mb-0">Administra los títulos bibliográficos del acervo.</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button type="button" onclick="openModal('modalCrearCatalogo')" class="btn-app btn-app-primary">
            <i data-lucide="plus" class="icon"></i>
            Nuevo título
        </button>
    </div>
</div>

<div class="filter-bar mb-4">
    <form method="GET" class="row g-3 align-items-end w-100">
        <div class="col-lg-3">
            <label class="form-label-app">Buscar</label>
            <div class="position-relative">
                <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400"><i data-lucide="search" class="icon"></i></span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control-app ps-5" placeholder="Título o ISBN...">
            </div>
        </div>
        <div class="col-lg-2">
            <label class="form-label-app">Tipo</label>
            <select name="tipo_material" class="form-select-app">
                <option value="">Todos</option>
                @foreach($tipos as $tipo)
                    <option value="{{ $tipo->id }}" @selected(request('tipo_material') == $tipo->id)>{{ $tipo->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2">
            <label class="form-label-app">Área</label>
            <select name="area" class="form-select-app">
                <option value="">Todas</option>
                @foreach($areas as $area)
                    <option value="{{ $area->id }}" @selected(request('area') == $area->id)>{{ $area->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2">
            <label class="form-label-app">Editorial</label>
            <select name="editorial" class="form-select-app">
                <option value="">Todas</option>
                @foreach($editoriales as $editorial)
                    <option value="{{ $editorial->id }}" @selected(request('editorial') == $editorial->id)>{{ $editorial->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2">
            <button type="submit" class="btn-app btn-app-secondary w-100">Filtrar</button>
        </div>
        <div class="col-lg-1">
            <a href="{{ route('biblioteca.catalogo.index') }}" class="btn-app btn-app-light w-100">Limpiar</a>
        </div>
    </form>
</div>

<div class="table-responsive-app">
    <table class="table-app">
        <thead>
            <tr>
                <th>Título</th>
                <th>Autores</th>
                <th>Tipo / Área</th>
                <th>Editorial</th>
                <th class="text-center">Ejemplares</th>
                <th class="text-center">Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($catalogo as $item)
                <tr>
                    <td>
                        <div class="book-title">{{ $item->titulo }}</div>
                        <div class="book-meta">{{ $item->isbn ?: 'Sin ISBN' }}</div>
                    </td>
                    <td>
                        @if($item->autores->isEmpty())
                            <span class="text-slate-400">—</span>
                        @else
                            {{ $item->autores->pluck('nombre_completo')->implode('; ') }}
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-slate-100 text-slate-700">{{ $item->tipoMaterial->nombre ?? '—' }}</span>
                        <div class="text-xs text-slate-500 mt-1">{{ $item->area->nombre ?? '—' }}</div>
                    </td>
                    <td>{{ $item->editorial->nombre ?? '—' }}</td>
                    <td class="text-center">
                        <span class="stock-badge success">{{ $item->ejemplaresDisponibles->count() }} / {{ $item->ejemplares->count() }}</span>
                    </td>
                    <td class="text-center">
                        @if($item->estado)
                            <span class="badge rounded-pill bg-success-subtle text-success">Activo</span>
                        @else
                            <span class="badge rounded-pill bg-secondary-subtle text-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <button type="button" onclick="editarCatalogo({{ json_encode($item->toArray()) }}, {{ json_encode($item->autores->pluck('id')) }})" class="btn-action btn-action-secondary" title="Editar">
                            <i data-lucide="pencil" class="icon"></i>
                        </button>
                        <form action="{{ route('biblioteca.catalogo.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este título?')">
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
                    <td colspan="7" class="text-center py-4 text-slate-500">No hay títulos registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $catalogo->links() }}
</div>

@component('components.modal', ['id' => 'modalCrearCatalogo', 'title' => 'Registrar Título', 'icon' => 'plus', 'size' => 'xl'])
    <form method="POST" action="{{ route('biblioteca.catalogo.store') }}">
        @csrf
        @include('biblioteca.catalogo.form', ['modo' => 'crear'])
        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('modalCrearCatalogo')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">Guardar</button>
        </div>
    </form>
@endcomponent

@component('components.modal', ['id' => 'modalEditarCatalogo', 'title' => 'Editar Título', 'icon' => 'pencil', 'size' => 'xl'])
    <form id="formEditarCatalogo" method="POST">
        @csrf
        @method('PUT')
        @include('biblioteca.catalogo.form', ['modo' => 'editar'])
        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('modalEditarCatalogo')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">Guardar cambios</button>
        </div>
    </form>
@endcomponent

@push('scripts')
<script>
    function editarCatalogo(item, autoresIds) {
        document.getElementById('formEditarCatalogo').action = '/biblioteca/catalogo/' + item.id;
        document.getElementById('editTitulo').value = item.titulo;
        document.getElementById('editSubtitulo').value = item.subtitulo ?? '';
        document.getElementById('editIsbn').value = item.isbn ?? '';
        document.getElementById('editEdicion').value = item.edicion ?? '';
        document.getElementById('editAnioPublicacion').value = item.anio_publicacion ?? '';
        document.getElementById('editNumeroPaginas').value = item.numero_paginas ?? '';
        document.getElementById('editIdioma').value = item.idioma ?? '';
        document.getElementById('editDescripcion').value = item.descripcion ?? '';
        document.getElementById('editIdTipoMaterial').value = item.id_tipo_material;
        document.getElementById('editIdArea').value = item.id_area ?? '';
        document.getElementById('editIdEditorial').value = item.id_editorial ?? '';
        document.getElementById('editEstadoCatalogo').checked = item.estado == 1;

        document.querySelectorAll('.checkbox-autor-edit').forEach(cb => {
            cb.checked = autoresIds.includes(parseInt(cb.value));
        });

        openModal('modalEditarCatalogo');
    }
</script>
@endpush
@endsection
