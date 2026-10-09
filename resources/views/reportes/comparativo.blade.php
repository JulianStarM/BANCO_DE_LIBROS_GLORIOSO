@extends('reportes._report_layout')

@section('filtros')
    <form method="GET" action="{{ route('reportes.comparativo') }}" class="filter-bar">
        <div class="filter-bar-title">
            <i data-lucide="filter" class="icon"></i>
            <span>Filtros</span>
        </div>
        <div class="filter-bar-content">
            <div class="form-group-app" style="min-width: 8rem;">
                <label class="form-label-app">Año 1</label>
                <select name="anio_uno" class="form-select-app">
                    <option value="">Seleccione</option>
                    @foreach($anios as $id => $anio)
                        <option value="{{ $id }}" {{ request('anio_uno') == $id ? 'selected' : '' }}>{{ $anio }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group-app" style="min-width: 8rem;">
                <label class="form-label-app">Año 2</label>
                <select name="anio_dos" class="form-select-app">
                    <option value="">Seleccione</option>
                    @foreach($anios as $id => $anio)
                        <option value="{{ $id }}" {{ request('anio_dos') == $id ? 'selected' : '' }}>{{ $anio }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-bar-actions">
                <button type="submit" class="btn-app btn-app-primary btn-app-sm">
                    <i data-lucide="search" class="icon"></i>
                    <span>Comparar</span>
                </button>
                <a href="{{ route('reportes.comparativo') }}" class="btn-app btn-app-light btn-app-sm">Limpiar</a>
            </div>
        </div>
    </form>
@endsection

@section('tabla')
    <div class="table-responsive-app">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Indicador</th>
                    <th class="text-end">Año 1</th>
                    <th class="text-end">Año 2</th>
                    <th class="text-end">Diferencia</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $indicadores = [
                        'total_estudiantes' => 'Total estudiantes',
                        'total_entregas' => 'Total entregas',
                        'total_libros_entregados' => 'Libros entregados',
                        'total_devoluciones' => 'Total devoluciones',
                        'total_libros_devueltos' => 'Libros devueltos',
                        'total_libros_perdidos' => 'Libros perdidos',
                        'total_libros_deficientes' => 'Libros deficientes',
                        'estudiantes_sin_entrega' => 'Estudiantes sin entrega',
                    ];
                @endphp
                @forelse($indicadores as $clave => $etiqueta)
                    @php
                        $valorUno = (int) ($datosAnioUno->$clave ?? 0);
                        $valorDos = (int) ($datosAnioDos->$clave ?? 0);
                        $diferencia = $valorDos - $valorUno;
                    @endphp
                    <tr>
                        <td>{{ $etiqueta }}</td>
                        <td class="text-end">{{ number_format($valorUno) }}</td>
                        <td class="text-end">{{ number_format($valorDos) }}</td>
                        <td class="text-end {{ $diferencia > 0 ? 'text-success' : ($diferencia < 0 ? 'text-danger' : 'text-slate-500') }}">
                            {{ $diferencia > 0 ? '+' : '' }}{{ number_format($diferencia) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-slate-500">No hay datos para comparar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
