@extends('layouts.app')

@section('title', $estudiante->exists ? 'Editar estudiante' : 'Nuevo estudiante')
@section('page-title', $estudiante->exists ? 'Editar estudiante' : 'Registrar estudiante')

@section('content')
<div class="page-header">
    <div class="page-header-row">
        <h1 class="page-title d-flex align-items-center gap-2">
            <span class="student-avatar">
                <i data-lucide="person-vcard" class="icon"></i>
            </span>
            {{ $estudiante->exists ? 'Editar estudiante' : 'Nuevo estudiante' }}
        </h1>
        <a href="{{ route('estudiantes.index') }}" class="btn-app btn-app-secondary">
            <i data-lucide="arrow-left" class="icon"></i> Volver
        </a>
    </div>
</div>

<div class="card-app">
    <div class="card-body-base">
        <form method="POST" action="{{ $estudiante->exists ? route('estudiantes.update', $estudiante) : route('estudiantes.store') }}" class="row g-3" novalidate>
            @csrf
            @if($estudiante->exists) @method('PUT') @endif

            @if ($errors->any())
                <div class="col-12">
                    <div class="alert alert-danger mb-0">
                        <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                </div>
            @endif

            <div class="col-md-4">
                <label class="form-label-app">Codigo</label>
                <input type="text" name="codigo_estudiante" class="form-control-app" value="{{ old('codigo_estudiante', $estudiante->codigo_estudiante) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label-app">DNI</label>
                <input type="text" name="dni" id="dni" class="form-control-app" value="{{ old('dni', $estudiante->dni) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label-app">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control-app" value="{{ old('fecha_nacimiento', optional($estudiante->fecha_nacimiento)->format('Y-m-d')) }}">
            </div>

            <div class="col-md-6">
                <label class="form-label-app">Nombres *</label>
                <input type="text" name="nombres" id="nombres" class="form-control-app" value="{{ old('nombres', $estudiante->nombres) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label-app">Apellidos *</label>
                <input type="text" name="apellidos" id="apellidos" class="form-control-app" value="{{ old('apellidos', $estudiante->apellidos) }}" required>
            </div>

            <div class="col-md-3">
                <label class="form-label-app">Sexo</label>
                <select name="sexo" class="form-select-app">
                    <option value="">--</option>
                    <option value="M" @selected(old('sexo', $estudiante->sexo) === 'M')>Masculino</option>
                    <option value="F" @selected(old('sexo', $estudiante->sexo) === 'F')>Femenino</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label-app">Nivel *</label>
                <select name="nivel" class="form-select-app" required>
                    @foreach(['inicial','primaria','secundaria'] as $n)
                        <option value="{{ $n }}" @selected(old('nivel', $estudiante->nivel) === $n)>{{ ucfirst($n) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label-app">Grado *</label>
                <input type="text" name="grado" class="form-control-app" value="{{ old('grado', $estudiante->grado) }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label-app">Seccion *</label>
                <input type="text" name="seccion" class="form-control-app" value="{{ old('seccion', $estudiante->seccion) }}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label-app">Turno</label>
                <select name="turno" class="form-select-app">
                    <option value="">--</option>
                    @foreach(['mañana','tarde','noche'] as $t)
                        <option value="{{ $t }}" @selected(old('turno', $estudiante->turno) === $t)>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label-app">Direccion</label>
                <input type="text" name="direccion" class="form-control-app" value="{{ old('direccion', $estudiante->direccion) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-app">Telefono</label>
                <input type="text" name="telefono" class="form-control-app" value="{{ old('telefono', $estudiante->telefono) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label-app">Email</label>
                <input type="email" name="email" class="form-control-app" value="{{ old('email', $estudiante->email) }}">
            </div>

            <div class="col-md-3">
                <label class="form-label-app">Año escolar (ID)</label>
                <input type="number" name="id_anio_escolar" class="form-control-app" value="{{ old('id_anio_escolar', $estudiante->id_anio_escolar) }}">
            </div>

            <div class="col-12">
                <label class="form-check-app">
                    <input class="form-check-input-app" type="checkbox" name="estado" id="estado" value="1" @checked(old('estado', $estudiante->estado ?? true))>
                    <span class="form-check-label-app">Estudiante activo</span>
                </label>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="{{ route('estudiantes.index') }}" class="btn-app btn-app-secondary">Cancelar</a>
                <button class="btn-app btn-app-primary">
                    <i data-lucide="save" class="icon"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
