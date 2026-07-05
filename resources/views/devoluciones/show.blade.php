@extends('layouts.app')

@section('title', 'Devolución ' . $devolucion->codigo_general)
@section('page-title', 'Detalle de Devolución')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-0">
                <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 2rem; height: 2rem; background-color: var(--color-danger-light); color: var(--color-danger);">
                    <i data-lucide="rotate-ccw" class="icon"></i>
                </div>
                Devolución: {{ $devolucion->codigo_general }}
            </h2>
            <p class="small text-slate-500 mt-1 mb-0">
                Registrada el {{ \Carbon\Carbon::parse($devolucion->fecha_devolucion)->format('d/m/Y') }}
                a las {{ $devolucion->hora_devolucion }}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('devoluciones.constancia', $devolucion) }}"
               class="btn-app btn-app-danger">
                <i data-lucide="file-text" class="icon"></i>
                Generar Constancia de Devolución
            </a>
            <a href="{{ route('entregas.show', $devolucion->entrega) }}"
               class="btn-app btn-app-secondary">
                <i data-lucide="arrow-left" class="icon"></i>
                Ver Entrega Original
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Columna izquierda: info --}}
        <div class="col-md-4 d-flex flex-column gap-4">

            {{-- Datos del estudiante --}}
            <div class="card-app p-4">
                <h3 class="section-title">Datos del Estudiante</h3>
                <div class="d-flex flex-column gap-3">
                    <div>
                        <p class="small text-slate-500 mb-0">Nombres y Apellidos</p>
                        <p class="fw-medium text-slate-800 mb-0">
                            {{ $devolucion->estudiante->apellidos ?? '' }}, {{ $devolucion->estudiante->nombres ?? '' }}
                        </p>
                    </div>
                    <div>
                        <p class="small text-slate-500 mb-0">DNI</p>
                        <p class="fw-medium text-slate-800 mb-0">{{ $devolucion->estudiante->dni ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="small text-slate-500 mb-0">Nivel y Grado</p>
                        <p class="fw-medium text-slate-800 mb-0">
                            {{ $devolucion->estudiante->grado ?? '' }}°
                            {{ ucfirst($devolucion->estudiante->nivel ?? '') }}
                            — Sección {{ strtoupper($devolucion->estudiante->seccion ?? '') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Datos de la devolución --}}
            <div class="card-app p-4">
                <h3 class="section-title">Datos de la Devolución</h3>
                <div class="d-flex flex-column gap-3 small">
                    <div class="d-flex justify-content-between">
                        <span class="text-slate-500">Código</span>
                        <span class="fw-semibold text-slate-800">{{ $devolucion->codigo_general }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-slate-500">Fecha</span>
                        <span class="fw-medium text-slate-800">{{ \Carbon\Carbon::parse($devolucion->fecha_devolucion)->format('d/m/Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-slate-500">Hora</span>
                        <span class="fw-medium text-slate-800">{{ $devolucion->hora_devolucion }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-slate-500">Registrado por</span>
                        <span class="fw-medium text-slate-800">{{ $devolucion->usuarioRegistro->nombre ?? '-' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-slate-500">Entrega origen</span>
                        <a href="{{ route('entregas.show', $devolucion->entrega) }}"
                           class="fw-medium text-decoration-none">
                            {{ $devolucion->entrega->codigo_general ?? '-' }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Resumen numérico --}}
            <div class="card-app p-4">
                <h3 class="section-title">Resumen</h3>
                <div class="d-flex flex-column gap-2 small">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-slate-500">Devueltos</span>
                        <span class="fw-bold text-success fs-5">{{ $devolucion->total_devueltos ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-slate-500">Pendientes</span>
                        <span class="fw-bold text-danger fs-5">{{ $devolucion->total_no_devueltos ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-slate-500">Deficientes</span>
                        <span class="fw-bold text-warning fs-5">{{ $devolucion->total_deficientes ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-slate-500">Extraviados</span>
                        <span class="fw-bold text-danger-emphasis fs-5">{{ $devolucion->total_perdidos ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Columna derecha: tabla de libros --}}
        <div class="col-md-8">
            <div class="card-app">
                <div class="card-header-base d-flex justify-content-between align-items-center">
                    <h3 class="section-title mb-0">Libros Devueltos</h3>
                    <span class="badge bg-danger-subtle text-danger">
                        {{ $devolucion->detalles->count() }} Libros
                    </span>
                </div>

                <div class="table-responsive-app">
                    <table class="table table-app">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Código</th>
                                <th>Libro</th>
                                <th class="text-center">Estado Final</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($devolucion->detalles as $index => $detalle)
                                @php
                                    $estado = $detalle->estado_libro ?? 'bueno';
                                    [$badgeClass, $badgeText] = match($estado) {
                                        'bueno'       => ['bg-success-subtle text-success', 'Bueno'],
                                        'deteriorado' => ['bg-warning-subtle text-warning', 'Deteriorado'],
                                        'deficiente'  => ['bg-info-subtle text-info', 'Deficiente'],
                                        'perdido'     => ['bg-danger-subtle text-danger', 'Extraviado'],
                                        'no_devuelto' => ['bg-danger-subtle text-danger', 'No Devuelto'],
                                        default       => ['bg-light text-dark border', ucfirst($estado)],
                                    };
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td class="fw-medium">
                                        {{ $detalle->libro->codigo_libro ?? '-' }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-slate-800 small">
                                            {{ $detalle->libro->nombre ?? 'Libro no encontrado' }}
                                        </div>
                                        <div class="small text-slate-500">{{ $detalle->libro->area ?? '' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $badgeClass }}">
                                            {{ $badgeText }}
                                        </span>
                                    </td>
                                    <td class="text-muted" style="white-space: normal; max-width: 180px;">
                                        {{ $detalle->observaciones ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($devolucion->observaciones)
                    <div class="p-4 border-top bg-warning-subtle">
                        <p class="small fw-bold text-warning-emphasis text-uppercase mb-1">Observaciones generales:</p>
                        <p class="small text-warning-emphasis mb-0">{{ $devolucion->observaciones }}</p>
                    </div>
                @endif
            </div>

            {{-- Botón de constancia destacado al final --}}
            <div class="mt-4 p-4 card-app bg-danger-subtle border-danger" style="border: 1px solid #fecdd3;">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <p class="small fw-semibold text-danger-emphasis mb-0">¿Necesitas el comprobante oficial?</p>
                        <p class="small text-danger mt-1 mb-0">Descarga la constancia en PDF lista para imprimir y firmar.</p>
                    </div>
                    <a href="{{ route('devoluciones.constancia', $devolucion) }}"
                       class="btn-app btn-app-danger flex-shrink-0">
                        <i data-lucide="download" class="icon"></i>
                        Generar Constancia PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
