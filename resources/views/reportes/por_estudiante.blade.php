@extends('reportes._report_layout')

@section('filtros')
    @include('reportes._filtros_reporte', [
        'ruta' => route('reportes.por_estudiante'),
        'campos' => ['anio', 'grado', 'seccion', 'nivel', 'buscar'],
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
                    <th>Entregas</th>
                    <th>Libros entregados</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $dato)
                    <tr>
                        <td>{{ $dato->codigo_estudiante ?? '-' }}</td>
                        <td>{{ $dato->dni_estudiante ?? '-' }}</td>
                        <td>{{ $dato->estudiante }}</td>
                        <td>{{ $dato->grado }}</td>
                        <td>{{ $dato->seccion }}</td>
                        <td>{{ ucfirst($dato->nivel) }}</td>
                        <td>{{ ucfirst($dato->turno) }}</td>
                        <td>{{ number_format($dato->total_entregas ?? 0) }}</td>
                        <td>{{ number_format($dato->libros_entregados ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-slate-500">No hay datos disponibles.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
