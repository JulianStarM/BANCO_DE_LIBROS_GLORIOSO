@extends('layouts.app')

@section('title', 'Reportes')
@section('page-title', 'Reportes')

@section('content')
<div class="card-app p-4">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-slate-900 mb-1">Reportes</h1>
        <p class="text-sm text-slate-500 mb-0">Accede a los informes de entregas, devoluciones y stock de libros.</p>
    </div>

    <div class="report-grid">
        <a href="{{ route('reportes.por_grado') }}" class="report-card">
            <span class="report-icon">
                <i data-lucide="bar-chart-2" class="icon" style="width: 1.25rem; height: 1.25rem;"></i>
            </span>
            <div>
                <h2 class="report-title">Por grado</h2>
                <p class="report-desc">Informe de entregas y devoluciones por grado y sección.</p>
            </div>
        </a>
        <a href="{{ route('reportes.stock') }}" class="report-card">
            <span class="report-icon">
                <i data-lucide="package" class="icon" style="width: 1.25rem; height: 1.25rem;"></i>
            </span>
            <div>
                <h2 class="report-title">Stock de libros</h2>
                <p class="report-desc">Visualiza el inventario y existencias de cada libro por grado.</p>
            </div>
        </a>
        <a href="{{ route('reportes.pendientes') }}" class="report-card">
            <span class="report-icon">
                <i data-lucide="clock" class="icon" style="width: 1.25rem; height: 1.25rem;"></i>
            </span>
            <div>
                <h2 class="report-title">Pendientes</h2>
                <p class="report-desc">Lista de estudiantes con devoluciones pendientes.</p>
            </div>
        </a>
    </div>
</div>
@endsection
