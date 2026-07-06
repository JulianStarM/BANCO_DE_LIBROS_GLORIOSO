@extends('layouts.app')

@section('title', 'Editar Devolución')
@section('page-title', 'Editar Devolución')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-4">
        <div>
            <h2 class="h4 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-0">
                <i data-lucide="rotate-ccw" class="icon"></i>
                Editar Devolución: {{ $devolucion->codigo_general }}
            </h2>
            <p class="small text-slate-500 mt-1 mb-0">
                Estudiante: {{ $devolucion->estudiante->apellidos ?? '' }}, {{ $devolucion->estudiante->nombres ?? '' }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('devoluciones.show', $devolucion) }}" class="btn-app btn-app-secondary">
                <i data-lucide="arrow-left" class="icon"></i> Volver al detalle
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-app p-4">
                <h3 class="section-title mb-4">Información de la Devolución</h3>
                <form method="POST" action="{{ route('devoluciones.update', $devolucion) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-app">Estado</label>
                            <select name="estado" class="form-select-app" required>
                                <option value="completada" @selected($devolucion->estado === 'completada')>Completada</option>
                                <option value="parcial" @selected($devolucion->estado === 'parcial')>Parcial</option>
                                <option value="anulada" @selected($devolucion->estado === 'anulada')>Anulada</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-app">Fecha de devolución</label>
                            <input type="text" class="form-control-app" value="{{ optional($devolucion->fecha_devolucion)->format('d/m/Y') }} {{ $devolucion->hora_devolucion }}" disabled>
                        </div>
                        <div class="col-12">
                            <label class="form-label-app">Observaciones</label>
                            <textarea name="observaciones" rows="4" class="form-control-app">{{ old('observaciones', $devolucion->observaciones) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('devoluciones.show', $devolucion) }}" class="btn-app btn-app-secondary">Cancelar</a>
                        <button type="submit" class="btn-app btn-app-primary">
                            <i data-lucide="save" class="icon"></i> Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-app p-4">
                <h3 class="section-title">Resumen</h3>
                <div class="d-flex flex-column gap-3 small">
                    <div class="d-flex justify-content-between">
                        <span class="text-slate-500">Código</span>
                        <span class="fw-semibold text-slate-800">{{ $devolucion->codigo_general }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-slate-500">Devueltos</span>
                        <span class="fw-semibold text-slate-800">{{ $devolucion->total_devueltos ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-slate-500">Año escolar</span>
                        <span class="fw-semibold text-slate-800">{{ $devolucion->anioEscolar->anio ?? '—' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-slate-500">Responsable</span>
                        <span class="fw-semibold text-slate-800">{{ $devolucion->usuarioRegistro->nombres ?? '—' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
