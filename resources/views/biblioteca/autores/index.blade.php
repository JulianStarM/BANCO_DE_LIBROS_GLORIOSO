@extends('layouts.app')

@section('title', 'Autores')
@section('page-title', 'Biblioteca General')

@section('content')
@component('biblioteca.partials.crud-page', [
    'title' => 'Autores',
    'subtitle' => 'Administra los autores de los materiales bibliográficos.',
    'icon' => 'pen-tool',
    'createLabel' => 'Nuevo autor',
    'modalCreateId' => 'modalCrearAutor',
    'modalEditId' => 'modalEditarAutor',
    'routePrefix' => 'biblioteca.autores',
])
    @slot('filters')
        <div class="col-lg-6">
            <label class="form-label-app">Buscar</label>
            <div class="position-relative">
                <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400">
                    <i data-lucide="search" class="icon"></i>
                </span>
                <input type="text" name="buscar" value="{{ request('buscar') }}" class="form-control-app ps-5" placeholder="Nombre o apellido...">
            </div>
        </div>
    @endslot

    @slot('tableHead')
        <th>Apellidos</th>
        <th>Nombres</th>
        <th>Seudónimo</th>
        <th>Nacionalidad</th>
    @endslot

    @slot('tableBody')
        @forelse($autores as $autor)
            <tr>
                <td class="fw-medium text-slate-900">{{ $autor->apellidos }}</td>
                <td>{{ $autor->nombres }}</td>
                <td>{{ $autor->seudonimo ?: '—' }}</td>
                <td>{{ $autor->nacionalidad ?: '—' }}</td>
                <td class="text-end">
                    <button type="button" onclick="editarRegistromodalEditarAutor('{{ route('biblioteca.autores.update', $autor) }}', {nombres: '{{ $autor->nombres }}', apellidos: '{{ $autor->apellidos }}', seudonimo: '{{ $autor->seudonimo }}', nacionalidad: '{{ $autor->nacionalidad }}'})" class="btn-action btn-action-secondary" title="Editar">
                        <i data-lucide="pencil" class="icon"></i>
                    </button>
                    <form action="{{ route('biblioteca.autores.destroy', $autor) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este autor?')">
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
                <td colspan="5" class="text-center py-4 text-slate-500">No hay autores registrados.</td>
            </tr>
        @endforelse
    @endslot

    @slot('modalCreateBody')
        <div class="row g-3">
            <div class="col-6 form-group-app">
                <label class="form-label-app">Nombres *</label>
                <input type="text" name="nombres" class="form-control-app" required>
            </div>
            <div class="col-6 form-group-app">
                <label class="form-label-app">Apellidos *</label>
                <input type="text" name="apellidos" class="form-control-app" required>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-6 form-group-app">
                <label class="form-label-app">Seudónimo</label>
                <input type="text" name="seudonimo" class="form-control-app">
            </div>
            <div class="col-6 form-group-app">
                <label class="form-label-app">Nacionalidad</label>
                <input type="text" name="nacionalidad" class="form-control-app">
            </div>
        </div>
    @endslot

    @slot('modalEditBody')
        <div class="row g-3">
            <div class="col-6 form-group-app">
                <label class="form-label-app">Nombres *</label>
                <input type="text" name="nombres" class="form-control-app" required>
            </div>
            <div class="col-6 form-group-app">
                <label class="form-label-app">Apellidos *</label>
                <input type="text" name="apellidos" class="form-control-app" required>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-6 form-group-app">
                <label class="form-label-app">Seudónimo</label>
                <input type="text" name="seudonimo" class="form-control-app">
            </div>
            <div class="col-6 form-group-app">
                <label class="form-label-app">Nacionalidad</label>
                <input type="text" name="nacionalidad" class="form-control-app">
            </div>
        </div>
    @endslot

    @slot('pagination')
        {{ $autores->links() }}
    @endslot
@endcomponent
@endsection
