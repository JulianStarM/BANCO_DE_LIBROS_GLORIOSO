@extends('layouts.app')

@section('title', 'Detalle estudiante')
@section('page-title', 'Detalle del estudiante')

@section('content')
<div class="page-header">
    <div class="page-header-row">
        <h1 class="page-title d-flex align-items-center gap-2">
            <span class="student-avatar">
                <i data-lucide="person-vcard" class="icon"></i>
            </span>
            {{ $estudiante->nombre_completo }}
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('estudiantes.edit', $estudiante) }}" class="btn-app btn-app-primary">
                <i data-lucide="pencil" class="icon"></i> Editar
            </a>
            <a href="{{ route('estudiantes.index') }}" class="btn-app btn-app-secondary">
                <i data-lucide="arrow-left" class="icon"></i> Volver
            </a>
        </div>
    </div>
</div>

<div class="card-app">
    <div class="card-body-base">
        <dl class="row mb-0">
            <dt class="col-sm-3">Codigo</dt>             <dd class="col-sm-9">{{ $estudiante->codigo ?? '-' }}</dd>
            <dt class="col-sm-3">DNI</dt>                <dd class="col-sm-9">{{ $estudiante->dni ?? '-' }}</dd>
            <dt class="col-sm-3">Fecha nacimiento</dt>   <dd class="col-sm-9">{{ optional($estudiante->fecha_nacimiento)->format('d/m/Y') ?? '-' }}</dd>
            <dt class="col-sm-3">Sexo</dt>               <dd class="col-sm-9">{{ $estudiante->sexo ?? '-' }}</dd>
            <dt class="col-sm-3">Nivel / Grado</dt>      <dd class="col-sm-9">{{ $estudiante->nivel }} - {{ $estudiante->grado }} "{{ $estudiante->seccion }}"</dd>
            <dt class="col-sm-3">Turno</dt>              <dd class="col-sm-9">{{ $estudiante->turno ?? '-' }}</dd>
            <dt class="col-sm-3">Direccion</dt>          <dd class="col-sm-9">{{ $estudiante->direccion ?? '-' }}</dd>
            <dt class="col-sm-3">Telefono</dt>           <dd class="col-sm-9">{{ $estudiante->telefono ?? '-' }}</dd>
            <dt class="col-sm-3">Email</dt>              <dd class="col-sm-9">{{ $estudiante->email ?? '-' }}</dd>
            <dt class="col-sm-3">Estado</dt>             <dd class="col-sm-9">{{ $estudiante->estado ? 'Activo' : 'Inactivo' }}</dd>
        </dl>
    </div>
</div>
@endsection
