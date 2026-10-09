@extends('reportes._report_layout')

@section('tabla')
    <div class="table-responsive-app">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Año escolar</th>
                    <th>Estudiantes</th>
                    <th>Entregas</th>
                    <th>Libros entregados</th>
                    <th>Devoluciones</th>
                    <th>Libros devueltos</th>
                    <th>Perdidos</th>
                    <th>Deficientes</th>
                    <th>Sin entrega</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $dato)
                    <tr>
                        <td class="fw-bold">{{ $dato->anio_escolar }}</td>
                        <td>{{ number_format($dato->total_estudiantes ?? 0) }}</td>
                        <td>{{ number_format($dato->total_entregas ?? 0) }}</td>
                        <td>{{ number_format($dato->total_libros_entregados ?? 0) }}</td>
                        <td>{{ number_format($dato->total_devoluciones ?? 0) }}</td>
                        <td>{{ number_format($dato->total_libros_devueltos ?? 0) }}</td>
                        <td>{{ number_format($dato->total_libros_perdidos ?? 0) }}</td>
                        <td>{{ number_format($dato->total_libros_deficientes ?? 0) }}</td>
                        <td>{{ number_format($dato->estudiantes_sin_entrega ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-slate-500">No hay datos históricos disponibles.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
