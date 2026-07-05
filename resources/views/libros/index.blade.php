@extends('layouts.app')

@section('title', 'Libros')
@section('page-title', 'Gestión de Libros')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="h3 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-1">
            <span class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success" style="width: 2.5rem; height: 2.5rem; font-size: 1.125rem;">
                <i data-lucide="book" class="icon"></i>
            </span>
            Catálogo de Libros
        </h2>
        <p class="text-sm text-slate-500 mb-0">Administra el inventario de libros y materiales educativos.</p>
    </div>

    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <button type="button" onclick="openModal('modalNuevoLibro')" class="btn-app btn-app-primary">
            <i data-lucide="plus" class="icon"></i>
            Nuevo Libro
        </button>
    </div>
</div>

<!-- Filtros -->
<div class="card-app p-4 mb-4">
    <form method="GET" action="{{ route('libros.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label-app">Buscar</label>
                <div class="position-relative">
                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400">
                        <i data-lucide="search" class="icon"></i>
                    </span>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control-app ps-5" placeholder="Nombre, código o área...">
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label-app">Área</label>
                <select name="area" class="form-select-app">
                    <option value="">Todas las áreas</option>
                    @foreach($areas as $area)
                        <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>{{ $area }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label class="form-label-app">Grado</label>
                <select name="grado" class="form-select-app">
                    <option value="">Todos los grados</option>
                    @for($i=1; $i<=6; $i++)
                        <option value="{{ $i }}" {{ request('grado') == $i ? 'selected' : '' }}>{{ $i }}° Grado</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3 d-flex gap-2">
                <button type="submit" class="btn-app btn-app-light flex-fill">
                    Filtrar
                </button>
                @if(request()->anyFilled(['buscar', 'area', 'grado']))
                    <a href="{{ route('libros.index') }}" class="btn-app btn-app-icon btn-app-light" title="Limpiar filtros">
                        <i data-lucide="x" class="icon"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Tabla -->
<div class="table-responsive-app">
    <table class="table-app">
        <thead>
            <tr>
                <th>Código</th>
                <th>Libro</th>
                <th>Área / Grado</th>
                <th class="text-center">Disponibles</th>
                <th class="text-center">Estado</th>
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($libros as $libro)
                <tr>
                    <td class="fw-medium text-slate-900 text-nowrap">
                        {{ $libro->codigo_libro }}
                    </td>
                    <td>
                        <div class="book-title">{{ $libro->nombre }}</div>
                        <div class="book-meta text-capitalize">{{ str_replace('_', ' ', $libro->tipo_material) }}</div>
                    </td>
                    <td>
                        <span class="badge bg-slate-100 text-slate-700">{{ $libro->area }}</span>
                        <span class="text-xs text-slate-500 ms-2">{{ $libro->grado }}° {{ ucfirst($libro->nivel) }}</span>
                    </td>
                    <td class="text-center">
                        @if($libro->cantidad_disponible > 5)
                            <span class="stock-badge success">
                                {{ $libro->cantidad_disponible }} / {{ $libro->cantidad_total }}
                            </span>
                        @elseif($libro->cantidad_disponible > 0)
                            <span class="stock-badge warning">
                                {{ $libro->cantidad_disponible }} / {{ $libro->cantidad_total }}
                            </span>
                        @else
                            <span class="stock-badge danger">
                                Agotado
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        @switch($libro->estado)
                            @case('activo')
                                <span class="book-status activo">
                                    <span class="d-inline-block rounded-circle bg-success me-1" style="width: 0.375rem; height: 0.375rem;"></span> Activo
                                </span>
                                @break
                            @case('agotado')
                                <span class="book-status agotado">
                                    <span class="d-inline-block rounded-circle bg-warning me-1" style="width: 0.375rem; height: 0.375rem;"></span> Agotado
                                </span>
                                @break
                            @case('descontinuado')
                                <span class="book-status descontinuado">
                                    <span class="d-inline-block rounded-circle bg-secondary me-1" style="width: 0.375rem; height: 0.375rem;"></span> Descontinuado
                                </span>
                                @break
                            @case('en_revision')
                                <span class="book-status en_revision">
                                    <span class="d-inline-block rounded-circle bg-info me-1" style="width: 0.375rem; height: 0.375rem;"></span> Revisión
                                </span>
                                @break
                        @endswitch
                    </td>
                    <td class="actions-cell">
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <a href="{{ route('libros.show', $libro) }}" class="btn-action" title="Ver detalles">
                                <i data-lucide="eye" class="icon"></i>
                            </a>
                            <a href="{{ route('libros.edit', $libro) }}" class="btn-action btn-action-primary" title="Editar">
                                <i data-lucide="pencil" class="icon"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center justify-content-center py-4">
                            <span class="d-flex align-items-center justify-content-center rounded-circle bg-slate-100 text-slate-400 mb-3" style="width: 3rem; height: 3rem; font-size: 1.25rem;">
                                <i data-lucide="book-x" class="icon"></i>
                            </span>
                            <p class="fw-medium text-slate-900 mb-1">No se encontraron libros</p>
                            <p class="text-sm text-slate-500 mb-0">Ajusta los filtros o registra un nuevo material educativo.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($libros->hasPages())
    <div class="mt-3">
        {{ $libros->links('pagination::bootstrap-5') }}
    </div>
@endif

{{-- =============== MODAL NUEVO LIBRO =============== --}}
<x-modal id="modalNuevoLibro" title="Registrar Nuevo Material" size="xl">
    <form id="formNuevoLibro" data-action="{{ route('libros.api.store') }}">
        @include('libros.form')
    </form>

    <x-slot:footer>
        <button type="button" onclick="closeModal('modalNuevoLibro')" class="btn-app btn-app-secondary">
            Cancelar
        </button>
        <button type="button" id="btnGuardarLibro" class="btn-app btn-app-primary btn-submit-modal">
            <i data-lucide="save" class="icon"></i> Guardar Libro
        </button>
    </x-slot:footer>
</x-modal>
@endsection
