@extends('reportes._report_layout')

@section('filtros')
    @include('reportes._filtros_reporte', [
        'ruta' => route('reportes.pendientes'),
        'campos' => ['anio', 'grado', 'seccion', 'nivel', 'buscar'],
    ])
@endsection

@section('tabla')
    <div class="table-responsive-app">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Estudiante</th>
                    <th>DNI</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Nivel</th>
                    <th>Código entrega</th>
                    <th>Fecha entrega</th>
                    <th>Libros</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $dato)
                    <tr>
                        <td>{{ $dato->codigo_estudiante ?? '-' }}</td>
                        <td>{{ $dato->estudiante ?? ($dato->apellidos . ', ' . $dato->nombres) }}</td>
                        <td>{{ $dato->dni ?? $dato->dni_estudiante ?? '-' }}</td>
                        <td>{{ $dato->grado }}</td>
                        <td>{{ $dato->seccion }}</td>
                        <td>{{ ucfirst($dato->nivel) }}</td>
                        <td>{{ $dato->codigo_entrega }}</td>
                        <td>{{ $dato->fecha_entrega ? \Illuminate\Support\Carbon::parse($dato->fecha_entrega)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $dato->total_libros ?? 0 }}</td>
                        <td>
                            <span class="badge bg-warning-subtle text-warning">Pendiente</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-slate-500">No hay estudiantes pendientes.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
