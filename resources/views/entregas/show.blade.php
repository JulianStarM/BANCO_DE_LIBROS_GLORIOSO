@extends('layouts.app')

@section('title', 'Detalle de Entrega')
@section('page-title', 'Detalle de Entrega')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-0">
                <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 2rem; height: 2rem; background-color: var(--color-primary-light); color: var(--color-primary);">
                    <i data-lucide="file-check" class="icon"></i>
                </div>
                Entrega: {{ $entrega->codigo_general }}
            </h2>
            <p class="small text-slate-500 mt-1 mb-0">Registrada el
                {{ \Carbon\Carbon::parse($entrega->fecha_entrega)->format('d/m/Y') }} a las {{ $entrega->hora_entrega }}</p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('entregas.constancia', $entrega) }}"
                class="btn-app btn-app-success">
                <i data-lucide="file-text" class="icon"></i>
                Generar Constancia de Entrega
            </a>
            <button type="button"
                class="btn-app btn-app-secondary"
                onclick="window.print()">
                <i data-lucide="printer" class="icon"></i>
                Imprimir página
            </button>
            <a href="{{ route('entregas.index') }}"
                class="btn-app btn-app-primary">
                <i data-lucide="list" class="icon"></i>
                Volver a Entregas
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Información del Estudiante -->
        <div class="col-md-4 d-flex flex-column gap-4">
            <div class="card-app p-4">
                <h3 class="section-title">Datos del Estudiante</h3>

                <div class="d-flex flex-column gap-3">
                    <div>
                        <p class="small text-slate-500 mb-0">Nombres y Apellidos</p>
                        <p class="fw-medium text-slate-800 mb-0">{{ $entrega->estudiante->apellidos ?? '' }},
                            {{ $entrega->estudiante->nombres ?? '' }}</p>
                    </div>
                    <div>
                        <p class="small text-slate-500 mb-0">DNI</p>
                        <p class="fw-medium text-slate-800 mb-0">{{ $entrega->estudiante->dni ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="small text-slate-500 mb-0">Nivel y Grado</p>
                        <p class="fw-medium text-slate-800 mb-0">{{ $entrega->estudiante->grado ?? '' }}°
                            {{ ucfirst($entrega->estudiante->nivel ?? '') }}</p>
                    </div>
                </div>
            </div>

            <div class="card-app p-4">
                <h3 class="section-title">Recepción</h3>

                @if($entrega->tipo_firmante === 'apoderado')
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 2.5rem; height: 2.5rem; background-color: var(--color-primary-light); color: var(--color-primary);">
                            <i data-lucide="users" class="icon"></i>
                        </div>
                        <div>
                            <p class="fw-bold text-slate-800 mb-0">Entregado al Apoderado</p>
                            <p class="small text-slate-500 mb-0">Padre, madre o tutor</p>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <p class="small text-slate-500 mb-0">Nombres y Apellidos</p>
                            <p class="fw-medium text-slate-800 mb-0">{{ $entrega->padre->nombre_completo ?? '' }}</p>
                        </div>
                        <div>
                            <p class="small text-slate-500 mb-0">DNI</p>
                            <p class="fw-medium text-slate-800 mb-0">{{ $entrega->padre->dni ?? '-' }}</p>
                        </div>
                        @if($entrega->padre->telefono ?? false)
                            <div>
                                <p class="small text-slate-500 mb-0">Teléfono</p>
                                <p class="fw-medium text-slate-800 mb-0">{{ $entrega->padre->telefono }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 2.5rem; height: 2.5rem; background-color: var(--color-success-light); color: var(--color-success);">
                            <i data-lucide="user" class="icon"></i>
                        </div>
                        <div>
                            <p class="fw-bold text-slate-800 mb-0">Entregado al Estudiante</p>
                            <p class="small text-slate-500 mb-0">Recibió personalmente</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Lista de Libros -->
        <div class="col-md-8">
            <div class="card-app">
                <div class="card-header-base d-flex justify-content-between align-items-center">
                    <h3 class="section-title mb-0">Libros Entregados</h3>
                    <span class="badge bg-primary-subtle text-primary">{{ $entrega->total_libros }}
                        Libros</span>
                </div>

                <div class="table-responsive-app">
                    <table class="table table-app">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Código</th>
                                <th>Libro</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entrega->detalles as $index => $detalle)
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td class="fw-medium">
                                        {{ $detalle->libro->codigo_libro ?? '-' }}</td>
                                    <td>
                                        <div class="fw-semibold text-slate-800 small">
                                            {{ $detalle->libro->nombre ?? 'Libro Eliminado' }}</div>
                                        <div class="small text-slate-500">{{ $detalle->libro->area ?? '' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">
                                            {{ ucfirst($detalle->entregas ?? 'entregado') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($entrega->observaciones)
                    <div class="p-4 border-top bg-warning-subtle">
                        <p class="small fw-bold text-warning-emphasis text-uppercase mb-1">Observaciones:</p>
                        <p class="small text-warning-emphasis mb-0">{{ $entrega->observaciones }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
