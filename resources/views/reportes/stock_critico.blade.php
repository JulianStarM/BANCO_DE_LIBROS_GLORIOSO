@extends('reportes._report_layout')

@section('filtros')
    @include('reportes._filtros_reporte', [
        'ruta' => route('reportes.stock_critico'),
        'campos' => ['anio', 'grado', 'nivel', 'umbral'],
    ])
@endsection

@section('tabla')
    <div class="table-responsive-app">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Área</th>
                    <th>Grado</th>
                    <th>Nivel</th>
                    <th>Total</th>
                    <th>Disponibles</th>
                    <th>Entregados</th>
                    <th>Estado</th>
                    <th>Nivel stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $dato)
                    <tr>
                        <td>{{ $dato->codigo_libro }}</td>
                        <td>{{ $dato->nombre }}</td>
                        <td>{{ $dato->area }}</td>
                        <td>{{ $dato->grado }}</td>
                        <td>{{ ucfirst($dato->nivel) }}</td>
                        <td>{{ number_format($dato->cantidad_total ?? 0) }}</td>
                        <td class="fw-bold {{ ($dato->cantidad_disponible ?? 0) == 0 ? 'text-danger' : 'text-warning' }}">
                            {{ number_format($dato->cantidad_disponible ?? 0) }}
                        </td>
                        <td>{{ number_format($dato->cantidad_entregada ?? 0) }}</td>
                        <td>
                            <span class="badge {{ $dato->estado === 'activo' ? 'bg-success-subtle text-success' : ($dato->estado === 'agotado' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }}">
                                {{ ucfirst(str_replace('_', ' ', $dato->estado)) }}
                            </span>
                        </td>
                        <td>
                            @if(($dato->cantidad_disponible ?? 0) == 0)
                                <span class="badge bg-danger-subtle text-danger">Agotado</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning">Crítico</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-slate-500">No hay libros en stock crítico.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
