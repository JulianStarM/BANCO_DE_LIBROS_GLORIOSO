@extends('reportes._report_layout')

@section('filtros')
    @include('reportes._filtros_reporte', [
        'ruta' => route('reportes.devoluciones'),
        'campos' => ['anio', 'grado', 'seccion', 'nivel', 'estado', 'desde', 'hasta', 'buscar'],
        'estados' => ['completada' => 'Completada', 'parcial' => 'Parcial', 'anulada' => 'Anulada'],
    ])
@endsection

@section('tabla')
    <div class="table-responsive-app">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Año</th>
                    <th>Estudiante</th>
                    <th>DNI</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Devueltos</th>
                    <th>No devueltos</th>
                    <th>Deficientes</th>
                    <th>Perdidos</th>
                    <th>Firmante</th>
                    <th>Estado</th>
                    <th>Registrado por</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $dato)
                    <tr>
                        <td>{{ $dato->codigo_general }}</td>
                        <td>{{ $dato->anio_escolar }}</td>
                        <td>{{ $dato->estudiante }}</td>
                        <td>{{ $dato->dni_estudiante }}</td>
                        <td>{{ $dato->grado }}</td>
                        <td>{{ $dato->seccion }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($dato->fecha_devolucion)->format('d/m/Y') }}</td>
                        <td>{{ $dato->hora_devolucion }}</td>
                        <td>{{ $dato->total_devueltos }}</td>
                        <td>{{ $dato->total_no_devueltos }}</td>
                        <td>{{ $dato->total_deficientes }}</td>
                        <td>{{ $dato->total_perdidos }}</td>
                        <td>{{ ucfirst($dato->tipo_firmante) }}</td>
                        <td>
                            <span class="badge {{ $dato->estado_devolucion === 'completada' ? 'bg-success-subtle text-success' : ($dato->estado_devolucion === 'parcial' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }}">
                                {{ ucfirst($dato->estado_devolucion) }}
                            </span>
                        </td>
                        <td>{{ $dato->registrado_por ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="15" class="text-center py-5 text-slate-500">No hay devoluciones registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
