@extends('layouts.app')

@section('title', 'Dashboard de Biblioteca')
@section('page-title', 'Biblioteca General')

@section('content')
<div class="dashboard-header">
    <div class="dashboard-subtitle">Biblioteca General</div>
    <h1 class="dashboard-title">Panel de Biblioteca</h1>

    <div class="dashboard-hero" style="background: linear-gradient(135deg, #0f2c59 0%, #1e4a8a 100%);">
        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
            <div>
                <div class="dashboard-subtitle mb-0" style="color: rgba(255,255,255,0.85);">Resumen bibliotecario</div>
                <p class="mt-2 mb-0 fw-medium" style="color: #fff;">I.E. Glorioso San Carlos de Puno.</p>
            </div>
            <div class="dashboard-hero-icon" style="background: rgba(255,255,255,0.15); color: #fff;">
                <i data-lucide="library" class="icon"></i>
            </div>
        </div>
    </div>
</div>

@php
    $stats = [
        ['label' => 'Títulos', 'value' => $totalTitulos, 'href' => route('biblioteca.catalogo.index'), 'icon' => 'book', 'color' => 'emerald'],
        ['label' => 'Ejemplares', 'value' => $totalEjemplares, 'href' => route('biblioteca.ejemplares.index'), 'icon' => 'book-copy', 'color' => 'sky'],
        ['label' => 'Disponibles', 'value' => $ejemplaresDisponibles, 'href' => route('biblioteca.ejemplares.index'), 'icon' => 'check-circle', 'color' => 'emerald'],
        ['label' => 'Prestados', 'value' => $ejemplaresPrestados, 'href' => route('biblioteca.prestamos.index'), 'icon' => 'clock', 'color' => 'amber'],
        ['label' => 'Préstamos vencidos', 'value' => $prestamosVencidos, 'href' => route('biblioteca.prestamos.index'), 'icon' => 'alert-triangle', 'color' => 'rose'],
        ['label' => 'Lectores', 'value' => $totalSocios, 'href' => route('biblioteca.socios.index'), 'icon' => 'users', 'color' => 'violet'],
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
                        <p class="dashboard-card-title">Actividad de hoy</p>
                        <p class="dashboard-card-text">Préstamos y devoluciones registradas hoy.</p>
                    </div>
                </div>
                <div class="d-flex flex-column gap-2 mt-4">
                    <div class="dashboard-stock-item">
                        <div class="dashboard-stock-item-info">
                            <p class="dashboard-stock-item-title">Préstamos</p>
                        </div>
                        <span class="dashboard-stock-badge">{{ $prestamosHoy }}</span>
                    </div>
                    <div class="dashboard-stock-item">
                        <div class="dashboard-stock-item-info">
                            <p class="dashboard-stock-item-title">Devoluciones</p>
                        </div>
                        <span class="dashboard-stock-badge">{{ $devolucionesHoy }}</span>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="dashboard-card-title">Préstamos recientes</p>
                    </div>
                </div>
                @if($prestamosRecientes->isEmpty())
                    <div class="dashboard-alert-empty">Sin préstamos recientes.</div>
                @else
                    <div class="d-flex flex-column gap-2 mt-4">
                        @foreach($prestamosRecientes as $prestamo)
                            <div class="dashboard-stock-item">
                                <div class="dashboard-stock-item-info">
                                    <p class="dashboard-stock-item-title">{{ $prestamo->codigo_prestamo }}</p>
                                    <p class="dashboard-stock-item-meta">{{ $prestamo->socio->nombre_completo }}</p>
                                </div>
                                <span class="dashboard-stock-badge">{{ $prestamo->detalles->count() }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
