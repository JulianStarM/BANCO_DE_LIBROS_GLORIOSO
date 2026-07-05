@extends('layouts.app')

@section('title', 'Nuevo Libro')
@section('page-title', 'Registrar Nuevo Libro')

@section('content')
<div class="mb-4">
    <div class="d-flex align-items-center gap-3 mb-2">
        <a href="{{ route('libros.index') }}" class="text-slate-500 text-decoration-none">
            <i data-lucide="arrow-left" class="icon" style="width: 1.25rem; height: 1.25rem;"></i>
        </a>
        <h2 class="h3 fw-bold text-slate-800 mb-0">Registrar Nuevo Material</h2>
    </div>
    <p class="text-sm text-slate-500 mb-0" style="margin-left: 2.25rem;">Ingresa los datos del nuevo libro o material educativo.</p>
</div>

<div class="card-app p-4">
    <form action="{{ route('libros.store') }}" method="POST">
        @csrf

        @include('libros.form')

        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <a href="{{ route('libros.index') }}" class="btn-app btn-app-secondary">
                Cancelar
            </a>
            <button type="submit" class="btn-app btn-app-primary">
                <i data-lucide="save" class="icon"></i>
                Guardar Libro
            </button>
        </div>
    </form>
</div>
@endsection
