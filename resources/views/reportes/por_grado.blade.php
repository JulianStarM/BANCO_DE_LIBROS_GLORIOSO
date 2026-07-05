@extends('layouts.app')

@section('title', 'Reportes por Grado')
@section('page-title', 'Reportes por Grado')

@section('content')
<div class="card-app p-4">
    <h1 class="h3 fw-bold text-slate-900 mb-4">Reporte por grado</h1>

    <div class="table-responsive-app">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>Entregas</th>
                    <th>Devoluciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $dato)
                    <tr>
                        <td>{{ $dato->grado }}</td>
                        <td>{{ $dato->seccion }}</td>
                        <td>{{ $dato->entregas ?? 0 }}</td>
                        <td>{{ $dato->devoluciones ?? 0 }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-slate-500">No hay datos disponibles.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
