@extends('layouts.app')

@section('title', $usuario->exists ? 'Editar usuario' : 'Nuevo usuario')
@section('page-title', $usuario->exists ? 'Editar usuario' : 'Registrar usuario')

@section('content')
<div class="page-header">
    <div class="page-header-row">
        <h1 class="page-title d-flex align-items-center gap-2">
            <span class="student-avatar">
                <i data-lucide="person-gear" class="icon"></i>
            </span>
            {{ $usuario->exists ? 'Editar usuario' : 'Nuevo usuario' }}
        </h1>
        <a href="{{ route('usuarios.index') }}" class="btn-app btn-app-secondary">
            <i data-lucide="arrow-left" class="icon"></i> Volver
        </a>
    </div>
</div>

<div class="card-app">
    <div class="card-body-base">
        <form method="POST" action="{{ $usuario->exists ? route('usuarios.update', $usuario) : route('usuarios.store') }}" class="row g-3" novalidate>
            @csrf
            @if($usuario->exists) @method('PUT') @endif

            @if ($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger mb-0">
                        <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                </div>
            @endif

            <div class="col-md-6">
                <label class="form-label-app">Nombres *</label>
                <input type="text" name="nombres" class="form-control-app" value="{{ old('nombres', $usuario->nombres) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-app">Apellidos *</label>
                <input type="text" name="apellidos" class="form-control-app" value="{{ old('apellidos', $usuario->apellidos) }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label-app">DNI</label>
                <input type="text" name="dni" class="form-control-app" value="{{ old('dni', $usuario->dni) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label-app">Telefono</label>
                <input type="text" name="telefono" class="form-control-app" value="{{ old('telefono', $usuario->telefono) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label-app">Rol *</label>
                <select name="rol" class="form-select-app" required>
                    @foreach(['Administrador','Bibliotecario'] as $r)
                        <option value="{{ $r }}" @selected(old('rol', $usuario->rol) === $r)>{{ $r }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label-app">Correo electronico *</label>
                <input type="email" name="email" class="form-control-app" value="{{ old('email', $usuario->email) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-app">Contraseña {{ $usuario->exists ? '(dejar vacio para no cambiar)' : '*' }}</label>
                <input type="password" name="password" class="form-control-app" {{ $usuario->exists ? '' : 'required' }}>
            </div>

            <div class="col-12">
                <label class="form-check-app">
                    <input class="form-check-input-app" type="checkbox" name="estado" id="estado" value="1" @checked(old('estado', $usuario->estado ?? true))>
                    <span class="form-check-label-app">Usuario activo</span>
                </label>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('usuarios.index') }}" class="btn-app btn-app-secondary">Cancelar</a>
                <button class="btn-app btn-app-primary">
                    <i data-lucide="save" class="icon"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
