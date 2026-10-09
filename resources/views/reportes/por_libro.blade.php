@extends('reportes._report_layout')

@section('filtros')
    @include('reportes._filtros_reporte', [
        'ruta' => route('reportes.por_libro'),
        'campos' => ['anio', 'grado', 'nivel', 'area', 'buscar'],
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
                    <th>Entregas</th>
                    <th>Ejemplares entregados</th>
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
                        <td>{{ number_format($dato->total_entregas ?? 0) }}</td>
                        <td>{{ number_format($dato->total_entregado ?? 0) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-slate-500">No hay datos disponibles.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
