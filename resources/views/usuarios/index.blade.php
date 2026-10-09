@extends('layouts.app')

@section('title', 'Usuarios')
@section('page-title', 'Gestion de usuarios')

@section('content')
<div class="page-header">
    <div class="page-header-row">
        <h1 class="page-title d-flex align-items-center gap-2">
            <span class="student-avatar">
                <i data-lucide="users" class="icon"></i>
            </span>
            Usuarios del sistema
        </h1>
        <a href="{{ route('usuarios.create') }}" class="btn-app btn-app-primary">
            <i data-lucide="plus" class="icon"></i> Nuevo usuario
        </a>
    </div>
</div>

<div class="card-app mb-3">
    <div class="card-body-base">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-6">
                <input type="text" name="q" value="{{ $q }}" class="form-control-app" placeholder="Buscar por nombres, apellidos, email o DNI...">
            </div>
            <div class="col-md-3">
                <button class="btn-app btn-app-secondary w-100">
                    <i data-lucide="search" class="icon"></i> Buscar
                </button>
            </div>
            <div class="col-md-3">
                <a href="{{ route('usuarios.index') }}" class="btn-app btn-app-light w-100">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive-app">
    <table class="table-app">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombres y apellidos</th>
                        <th>DNI</th>
                        <th>Email</th>
                        <th>Telefono</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="actions-cell">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $u)
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td>
                                <div class="student-name">{{ $u->apellidos }}, {{ $u->nombres }}</div>
                            </td>
                            <td>{{ $u->dni ?? '-' }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->telefono ?? '-' }}</td>
                            <td>
                                <span class="badge-pill" style="background-color: var(--color-primary-light); color: var(--color-primary);">{{ $u->rol }}</span>
                            </td>
                            <td>
                                @if($u->estado)
                                    <span class="status-badge activo">Activo</span>
                                @else
                                    <span class="status-badge inactivo">Inactivo</span>
                                @endif
                            </td>
                            <td class="actions-cell">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('usuarios.edit', $u) }}" class="btn-action btn-action-primary" title="Editar">
                                        <i data-lucide="pencil" class="icon"></i>
                                    </a>
                                    <form action="{{ route('usuarios.destroy', $u) }}" method="POST" class="d-inline form-delete"
                                          data-confirm="true"
                                          data-confirm-title="¿Eliminar usuario?"
                                          data-confirm-message="El usuario {{ $u->nombre }} será eliminado permanentemente."
                                          data-confirm-text="Eliminar">
                                        @csrf @method('DELETE')
                                        <button class="btn-action btn-action-danger" title="Eliminar">
                                            <i data-lucide="trash-2" class="icon"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="table-empty">
                                    No hay usuarios registrados.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
</div>

<div class="mt-3">
    {{ $usuarios->links('pagination::bootstrap-5') }}
</div>
@endsection

@push('scripts')<script src="{{ asset('js/usuarios.js') }}"></script>@endpush
