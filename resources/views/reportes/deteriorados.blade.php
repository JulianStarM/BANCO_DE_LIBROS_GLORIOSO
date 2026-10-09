@extends('reportes._report_layout')

@section('filtros')
    @include('reportes._filtros_reporte', [
        'ruta' => route('reportes.deteriorados'),
        'campos' => ['anio', 'grado', 'nivel', 'buscar'],
    ])
@endsection

@section('tabla')
    <div class="table-responsive-app">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Fecha devolución</th>
                    <th>Código devolución</th>
                    <th>Estudiante</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Código libro</th>
                    <th>Libro</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $dato)
                    <tr>
                        <td>{{ $dato->devolucion->fecha_devolucion->format('d/m/Y') }}</td>
                        <td>{{ $dato->devolucion->codigo_general }}</td>
                        <td>{{ $dato->devolucion->estudiante->nombre_completo ?? '-' }}</td>
                        <td>{{ $dato->devolucion->estudiante->grado ?? '-' }}</td>
                        <td>{{ $dato->devolucion->estudiante->seccion ?? '-' }}</td>
                        <td>{{ $dato->libro->codigo_libro ?? '-' }}</td>
                        <td>{{ $dato->libro->nombre ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $dato->estado_libro === 'deficiente' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning' }}">
                                {{ ucfirst($dato->estado_libro) }}
                            </span>
                        </td>
                        <td>{{ $dato->observaciones ? Str::limit($dato->observaciones, 50) : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-slate-500">No hay libros deteriorados registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
