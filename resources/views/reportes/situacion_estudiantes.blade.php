@extends('reportes._report_layout')

@section('filtros')
    @include('reportes._filtros_reporte', [
        'ruta' => route('reportes.situacion_estudiantes'),
        'campos' => ['anio', 'grado', 'seccion', 'nivel', 'estado', 'buscar'],
        'estados' => ['activo' => 'Activo', 'retirado' => 'Retirado', 'trasladado' => 'Trasladado', 'egresado' => 'Egresado'],
    ])
@endsection

@section('tabla')
    <div class="table-responsive-app">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>DNI</th>
                    <th>Estudiante</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Nivel</th>
                    <th>Turno</th>
                    <th>Estado</th>
                    <th>Entregas</th>
                    <th>Devoluciones</th>
                    <th>Libros entregados</th>
                    <th>Pendientes estimados</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $dato)
                    <tr>
                        <td>{{ $dato->codigo_estudiante ?? '-' }}</td>
                        <td>{{ $dato->dni }}</td>
                        <td>{{ $dato->apellidos }}, {{ $dato->nombres }}</td>
                        <td>{{ $dato->grado }}</td>
                        <td>{{ $dato->seccion }}</td>
                        <td>{{ ucfirst($dato->nivel) }}</td>
                        <td>{{ ucfirst($dato->turno) }}</td>
                        <td>
                            <span class="badge {{ $dato->estado === 'activo' ? 'bg-success-subtle text-success' : ($dato->estado === 'egresado' ? 'bg-info-subtle text-info' : 'bg-warning-subtle text-warning') }}">
                                {{ ucfirst($dato->estado) }}
                            </span>
                        </td>
                        <td>{{ number_format($dato->total_entregas ?? 0) }}</td>
                        <td>{{ number_format($dato->total_devoluciones ?? 0) }}</td>
                        <td>{{ number_format($dato->libros_entregados ?? 0) }}</td>
                        <td>
                            @php
                                $pendientes = max(0, ($dato->total_entregas ?? 0) - ($dato->total_devoluciones ?? 0));
                            @endphp
                            @if($pendientes > 0)
                                <span class="badge bg-warning-subtle text-warning">{{ $pendientes }}</span>
                            @else
                                <span class="badge bg-success-subtle text-success">0</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="text-center py-5 text-slate-500">No hay datos disponibles.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
