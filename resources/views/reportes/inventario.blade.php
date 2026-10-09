@extends('reportes._report_layout')

@section('filtros')
    @include('reportes._filtros_reporte', [
        'ruta' => route('reportes.inventario'),
        'campos' => ['anio', 'grado', 'nivel', 'area', 'estado'],
        'estados' => ['activo' => 'Activo', 'agotado' => 'Agotado', 'descontinuado' => 'Descontinuado', 'en_revision' => 'En revisión'],
    ])
@endsection

@section('tabla')
    <div class="table-responsive-app">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Área</th>
                    <th>Grado</th>
                    <th>Nivel</th>
                    <th>Editorial</th>
                    <th>Año edición</th>
                    <th>Total</th>
                    <th>Disponibles</th>
                    <th>Entregados</th>
                    <th>Devueltos</th>
                    <th>Perdidos</th>
                    <th>Deficientes</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $dato)
                    <tr>
                        <td>{{ $dato->codigo_libro }}</td>
                        <td>{{ $dato->nombre }}</td>
                        <td>{{ str_replace('_', ' ', $dato->tipo_material) }}</td>
                        <td>{{ $dato->area }}</td>
                        <td>{{ $dato->grado }}</td>
                        <td>{{ ucfirst($dato->nivel) }}</td>
                        <td>{{ $dato->editorial ?? '-' }}</td>
                        <td>{{ $dato->anio_edicion ?? '-' }}</td>
                        <td>{{ number_format($dato->cantidad_total ?? 0) }}</td>
                        <td>{{ number_format($dato->cantidad_disponible ?? 0) }}</td>
                        <td>{{ number_format($dato->cantidad_entregada ?? 0) }}</td>
                        <td>{{ number_format($dato->cantidad_devuelta ?? 0) }}</td>
                        <td>{{ number_format($dato->cantidad_perdida ?? 0) }}</td>
                        <td>{{ number_format($dato->cantidad_deficiente ?? 0) }}</td>
                        <td>
                            <span class="badge {{ $dato->estado === 'activo' ? 'bg-success-subtle text-success' : ($dato->estado === 'agotado' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }}">
                                {{ ucfirst(str_replace('_', ' ', $dato->estado)) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="15" class="text-center py-5 text-slate-500">No hay datos disponibles.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
