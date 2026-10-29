@extends('layouts.app')

@section('title', 'Reportes de Biblioteca')
@section('page-title', 'Biblioteca General')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="h3 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-1">
            <span class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success" style="width: 2.5rem; height: 2.5rem; font-size: 1.125rem;">
                <i data-lucide="file-text" class="icon"></i>
            </span>
            Reportes de Biblioteca
        </h2>
        <p class="text-sm text-slate-500 mb-0">Resumen estadístico del módulo bibliotecario.</p>
    </div>
</div>

@php
    $stats = [
        ['label' => 'Títulos', 'value' => $totalTitulos, 'icon' => 'book', 'color' => 'emerald'],
        ['label' => 'Ejemplares', 'value' => $totalEjemplares, 'icon' => 'book-copy', 'color' => 'sky'],
        ['label' => 'Disponibles', 'value' => $ejemplaresDisponibles, 'icon' => 'check-circle', 'color' => 'emerald'],
        ['label' => 'Prestados', 'value' => $ejemplaresPrestados, 'icon' => 'clock', 'color' => 'amber'],
        ['label' => 'Bajas', 'value' => $ejemplaresBaja, 'icon' => 'x-circle', 'color' => 'rose'],
        ['label' => 'Lectores', 'value' => $totalSocios, 'icon' => 'users', 'color' => 'violet'],
        ['label' => 'Préstamos del mes', 'value' => $prestamosMes, 'icon' => 'clipboard-list', 'color' => 'indigo'],
        ['label' => 'Préstamos vencidos', 'value' => $prestamosVencidos, 'icon' => 'alert-triangle', 'color' => 'rose'],
        ['label' => 'Multas pendientes', 'value' => $multasPendientes, 'icon' => 'alert-circle', 'color' => 'orange'],
        ['label' => 'Visitas del mes', 'value' => $visitasMes, 'icon' => 'door-open', 'color' => 'teal'],
    ];
@endphp

<div class="row g-4">
    @foreach($stats as $stat)
        <div class="col-6 col-md-4 col-xl-3">
            <div class="stat-card h-100">
                <div class="d-flex align-items-start justify-content-between gap-3">
                    <div>
                        <p class="stat-card-label">{{ $stat['label'] }}</p>
                        <p class="stat-card-value">{{ $stat['value'] }}</p>
                    </div>
                    <div class="stat-card-icon {{ $stat['color'] }}">
                        <i data-lucide="{{ $stat['icon'] }}" class="icon" style="width: 1.75rem; height: 1.75rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4 mt-2">
    <div class="col-12 col-md-6">
        <div class="card-app p-4">
            <h3 class="h5 fw-bold text-slate-800 mb-3">Préstamos por estado</h3>
            <div class="table-responsive-app">
                <table class="table-app">
                    <tbody>
                        @foreach($prestamosPorEstado as $estado => $cantidad)
                            <tr>
                                <td class="text-capitalize">{{ $estado }}</td>
                                <td class="text-end fw-bold">{{ $cantidad }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
