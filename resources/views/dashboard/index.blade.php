@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="dashboard-header">
    <div class="dashboard-subtitle">Sistema de Banco de Libros</div>
    <h1 class="dashboard-title">Panel de Control</h1>

    <div class="dashboard-hero">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <div>
                <div class="dashboard-subtitle mb-0">Resumen institucional</div>
                <p class="mt-2 mb-0 text-slate-700 fw-medium">Banco de Libros — I.E. Glorioso San Carlos de Puno.</p>
            </div>
            <div class="dashboard-hero-icon">
                <i data-lucide="building-2" class="icon"></i>
            </div>
        </div>
    </div>
</div>

@php
    $stats = [
        ['label' => 'Estudiantes', 'value' => $totalEstudiantes ?? 0, 'href' => route('estudiantes.index'), 'icon' => 'graduation-cap', 'color' => 'sky'],
        ['label' => 'Títulos', 'value' => $totalLibros ?? 0, 'href' => route('libros.index'), 'icon' => 'book-open', 'color' => 'emerald'],
        ['label' => 'Stock disponible', 'value' => $totalEjemplares ?? 0, 'href' => route('libros.index'), 'icon' => 'package', 'color' => 'amber'],
        ['label' => 'Entregas', 'value' => $totalEntregas ?? 0, 'href' => route('entregas.index'), 'icon' => 'clipboard-list', 'color' => 'violet'],
        ['label' => 'Libros entregados', 'value' => $librosEntregados ?? 0, 'href' => route('entregas.index'), 'icon' => 'check-circle', 'color' => 'emerald'],
        ['label' => 'Estudiantes pendientes', 'value' => $estudiantesPendientes ?? 0, 'href' => route('entregas.index'), 'icon' => 'alert-triangle', 'color' => 'rose'],
    ];
@endphp

<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="dashboard-stats-grid">
            @foreach($stats as $stat)
                <a href="{{ $stat['href'] }}" class="stat-card">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div>
                            <p class="stat-card-label">{{ $stat['label'] }}</p>
                            <p class="stat-card-value">{{ $stat['value'] }}</p>
                        </div>
                        <div class="stat-card-icon {{ $stat['color'] }}">
                            <i data-lucide="{{ $stat['icon'] }}" class="icon" style="width: 1.75rem; height: 1.75rem;"></i>
                        </div>
                    </div>
                    <div class="stat-card-footer">
                        Ver detalle
                        <i data-lucide="arrow-right" class="icon"></i>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="dashboard-sidebar-cards">
            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="dashboard-card-title">Avance de entrega</p>
                        <p class="dashboard-card-text">{{ $estudiantesConEntrega ?? 0 }} de {{ $totalEstudiantes ?? 0 }} estudiantes han recibido sus libros.</p>
                    </div>
                    <span class="badge-pill" style="background-color: var(--color-success-light); color: #047857;">{{ $avance ?? 0 }}%</span>
                </div>
                <div class="dashboard-progress progress-app">
                    <div class="progress-bar-app" style="width: {{ $avance ?? 0 }}%"></div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="dashboard-card-title">Alertas de stock</p>
                        <p class="dashboard-card-text">Títulos con stock disponible bajo. Total: {{ $stockTotal ?? 0 }}</p>
                    </div>
                    <span class="badge-pill" style="background-color: var(--color-danger-light); color: #be123c;">{{ $stockBajo->count() ?? 0 }}</span>
                </div>
                @if(($stockBajo->count() ?? 0) === 0)
                    <div class="dashboard-alert-empty">Sin alertas de stock bajo.</div>
                @else
                    <div class="d-flex flex-column gap-2 mt-4">
                        @foreach($stockBajo->take(5) as $libro)
                            <div class="dashboard-stock-item">
                                <div class="dashboard-stock-item-info">
                                    <p class="dashboard-stock-item-title">{{ $libro->titulo ?? $libro->codigo }}</p>
                                    <p class="dashboard-stock-item-meta">{{ $libro->area ?? 'Sin área' }} · Grado {{ $libro->grado ?? '-' }}</p>
                                </div>
                                <span class="dashboard-stock-badge">{{ $libro->cantidad_disponible ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
