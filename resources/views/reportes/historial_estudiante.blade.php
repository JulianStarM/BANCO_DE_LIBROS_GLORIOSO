@extends('reportes._report_layout')

@section('filtros')
    <form method="GET" action="{{ route('reportes.historial_estudiante') }}" class="filter-bar">
        <div class="filter-bar-title">
            <i data-lucide="filter" class="icon"></i>
            <span>Filtros</span>
        </div>
        <div class="filter-bar-content">
            <div class="form-group-app" style="min-width: 8rem;">
                <label class="form-label-app">Año escolar</label>
                <select name="anio" class="form-select-app">
                    <option value="">Todos</option>
                    @foreach($anios as $id => $anio)
                        <option value="{{ $id }}" {{ request('anio') == $id ? 'selected' : '' }}>{{ $anio }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group-app" style="min-width: 16rem;">
                <label class="form-label-app">Estudiante</label>
                <select name="estudiante_id" class="form-select-app">
                    <option value="">Seleccione un estudiante</option>
                    @foreach($estudiantes as $item)
                        <option value="{{ $item->id }}" {{ request('estudiante_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->apellidos }}, {{ $item->nombres }} - {{ $item->grado }}° "{{ $item->seccion }}" ({{ $item->codigo_estudiante ?? 'Sin código' }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-bar-actions">
                <button type="submit" class="btn-app btn-app-primary btn-app-sm">
                    <i data-lucide="search" class="icon"></i>
                    <span>Consultar</span>
                </button>
                <a href="{{ route('reportes.historial_estudiante') }}" class="btn-app btn-app-light btn-app-sm">Limpiar</a>
            </div>
        </div>
    </form>
@endsection

@section('tabla')
    @if(!$estudiante)
        <div class="table-empty">Seleccione un estudiante para visualizar su historial.</div>
    @else
        <div class="report-table-wrapper p-3 mb-3">
            <h3 class="h6 fw-bold text-slate-900 mb-3">Entregas</h3>
            <div class="table-responsive-app">
                <table class="table-app">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Libros</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($datos as $entrega)
                            <tr>
                                <td>{{ $entrega->codigo_general }}</td>
                                <td>{{ $entrega->fecha_entrega->format('d/m/Y') }}</td>
                                <td>{{ $entrega->hora_entrega }}</td>
                                <td>{{ $entrega->total_libros }}</td>
                                <td>
                                    <span class="badge {{ $entrega->estado === 'completada' ? 'bg-success-subtle text-success' : ($entrega->estado === 'parcial' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }}">
                                        {{ ucfirst($entrega->estado) }}
                                    </span>
                                </td>
                                <td>{{ $entrega->observaciones ? Str::limit($entrega->observaciones, 50) : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-slate-500">Sin entregas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="report-table-wrapper p-3">
            <h3 class="h6 fw-bold text-slate-900 mb-3">Devoluciones</h3>
            <div class="table-responsive-app">
                <table class="table-app">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Devueltos</th>
                            <th>No devueltos</th>
                            <th>Deficientes</th>
                            <th>Perdidos</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devoluciones as $devolucion)
                            <tr>
                                <td>{{ $devolucion->codigo_general }}</td>
                                <td>{{ $devolucion->fecha_devolucion->format('d/m/Y') }}</td>
                                <td>{{ $devolucion->hora_devolucion }}</td>
                                <td>{{ $devolucion->total_devueltos }}</td>
                                <td>{{ $devolucion->total_no_devueltos }}</td>
                                <td>{{ $devolucion->total_deficientes }}</td>
                                <td>{{ $devolucion->total_perdidos }}</td>
                                <td>
                                    <span class="badge {{ $devolucion->estado === 'completada' ? 'bg-success-subtle text-success' : ($devolucion->estado === 'parcial' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }}">
                                        {{ ucfirst($devolucion->estado) }}
                                    </span>
                                </td>
                                <td>{{ $devolucion->observaciones ? Str::limit($devolucion->observaciones, 50) : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-slate-500">Sin devoluciones registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
