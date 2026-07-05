@extends('layouts.app')

@section('title', 'Detalle usuario')
@section('page-title', 'Detalle de usuario')

@section('content')
<div class="page-header">
    <div class="page-header-row">
        <h1 class="page-title d-flex align-items-center gap-2">
            <span class="student-avatar">
                <i data-lucide="person-badge" class="icon"></i>
            </span>
            {{ $usuario->nombre_completo }}
        </h1>
        <a href="{{ route('usuarios.index') }}" class="btn-app btn-app-secondary">
            <i data-lucide="arrow-left" class="icon"></i> Volver
        </a>
    </div>
</div>

<div class="card-app">
    <div class="card-body-base">
        <dl class="row mb-0">
            <dt class="col-sm-3">DNI</dt>           <dd class="col-sm-9">{{ $usuario->dni ?? '-' }}</dd>
            <dt class="col-sm-3">Email</dt>         <dd class="col-sm-9">{{ $usuario->email }}</dd>
            <dt class="col-sm-3">Telefono</dt>      <dd class="col-sm-9">{{ $usuario->telefono ?? '-' }}</dd>
            <dt class="col-sm-3">Rol</dt>           <dd class="col-sm-9">{{ $usuario->rol }}</dd>
            <dt class="col-sm-3">Estado</dt>        <dd class="col-sm-9">{{ $usuario->estado ? 'Activo' : 'Inactivo' }}</dd>
            <dt class="col-sm-3">Registrado</dt>    <dd class="col-sm-9">{{ optional($usuario->created_at)->format('d/m/Y H:i') }}</dd>
        </dl>
    </div>
</div>
@endsection
