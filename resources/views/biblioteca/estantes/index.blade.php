@extends('layouts.app')

@section('title', 'Estantes')
@section('page-title', 'Biblioteca General')

@section('content')
@component('biblioteca.partials.crud-page', [
    'title' => 'Estantes',
    'subtitle' => 'Administra los estantes y su ubicación física.',
    'icon' => 'align-justify',
    'createLabel' => 'Nuevo estante',
    'modalCreateId' => 'modalCrearEstante',
    'modalEditId' => 'modalEditarEstante',
    'routePrefix' => 'biblioteca.estantes',
])
    @slot('filters')
        <div class="col-lg-6">
            <label class="form-label-app">Buscar</label>
            <div class="position-relative">
                <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400">
                    <i data-lucide="search" class="icon"></i>
                </span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control-app ps-5" placeholder="Código o nombre...">
            </div>
        </div>
    @endslot

    @slot('tableHead')
        <th>Código</th>
        <th>Nombre</th>
        <th>Ubicación</th>
        <th>Capacidad</th>
        <th class="text-center">Estado</th>
    @endslot

    @slot('tableBody')
        @forelse($estantes as $estante)
            <tr>
                <td class="fw-medium text-slate-900">{{ $estante->codigo_estante }}</td>
                <td>{{ $estante->nombre }}</td>
                <td>{{ $estante->ubicacion->nombre ?? '—' }}</td>
                <td>{{ $estante->capacidad_maxima ?: '—' }}</td>
                <td class="text-center">
                    @if($estante->estado)
                        <span class="badge rounded-pill bg-success-subtle text-success">Activo</span>
                    @else
                        <span class="badge rounded-pill bg-secondary-subtle text-secondary">Inactivo</span>
                    @endif
                </td>
                <td class="text-end">
                    <button type="button" onclick="editarRegistromodalEditarEstante('{{ route('biblioteca.estantes.update', $estante) }}', {id_ubicacion: '{{ $estante->id_ubicacion }}', codigo_estante: '{{ $estante->codigo_estante }}', nombre: '{{ $estante->nombre }}', capacidad_maxima: '{{ $estante->capacidad_maxima }}', estado: {{ $estante->estado ? 1 : 0 }}})" class="btn-action btn-action-secondary" title="Editar">
                        <i data-lucide="pencil" class="icon"></i>
                    </button>
                    <form action="{{ route('biblioteca.estantes.destroy', $estante) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este estante?')">
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
                <td colspan="6" class="text-center py-4 text-slate-500">No hay estantes registrados.</td>
            </tr>
        @endforelse
    @endslot

    @slot('modalCreateBody')
        <div class="form-group-app">
            <label class="form-label-app">Ubicación *</label>
            <select name="id_ubicacion" class="form-select-app" required>
                <option value="">Seleccione...</option>
                @foreach($ubicaciones as $ubicacion)
                    <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="row g-3">
            <div class="col-6 form-group-app">
                <label class="form-label-app">Código *</label>
                <input type="text" name="codigo_estante" class="form-control-app" required>
            </div>
            <div class="col-6 form-group-app">
                <label class="form-label-app">Nombre *</label>
                <input type="text" name="nombre" class="form-control-app" required>
            </div>
        </div>
        <div class="form-group-app">
            <label class="form-label-app">Capacidad máxima</label>
            <input type="number" name="capacidad_maxima" class="form-control-app" min="0">
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
            <label class="form-label-app">Ubicación *</label>
            <select name="id_ubicacion" class="form-select-app" required>
                <option value="">Seleccione...</option>
                @foreach($ubicaciones as $ubicacion)
                    <option value="{{ $ubicacion->id }}">{{ $ubicacion->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="row g-3">
            <div class="col-6 form-group-app">
                <label class="form-label-app">Código *</label>
                <input type="text" name="codigo_estante" class="form-control-app" required>
            </div>
            <div class="col-6 form-group-app">
                <label class="form-label-app">Nombre *</label>
                <input type="text" name="nombre" class="form-control-app" required>
            </div>
        </div>
        <div class="form-group-app">
            <label class="form-label-app">Capacidad máxima</label>
            <input type="number" name="capacidad_maxima" class="form-control-app" min="0">
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
        {{ $estantes->links() }}
    @endslot
@endcomponent
@endsection
