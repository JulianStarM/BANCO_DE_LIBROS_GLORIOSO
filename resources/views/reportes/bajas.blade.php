@extends('reportes._report_layout')

@section('filtros')
    @include('reportes._filtros_reporte', [
        'ruta' => route('reportes.bajas'),
        'campos' => ['anio', 'grado', 'nivel', 'buscar'],
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
                    <th>Total</th>
                    <th>Disponibles</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
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
                        <td>{{ number_format($dato->cantidad_total ?? 0) }}</td>
                        <td>{{ number_format($dato->cantidad_disponible ?? 0) }}</td>
                        <td>
                            <span class="badge {{ $dato->estado === 'descontinuado' ? 'bg-secondary-subtle text-secondary' : 'bg-danger-subtle text-danger' }}">
                                {{ ucfirst(str_replace('_', ' ', $dato->estado)) }}
                            </span>
                        </td>
                        <td>{{ $dato->observaciones ? Str::limit($dato->observaciones, 50) : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-5 text-slate-500">No hay libros dados de baja.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
