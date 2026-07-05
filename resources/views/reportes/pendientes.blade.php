@extends('layouts.app')

@section('title', 'Pendientes de Devolución')
@section('page-title', 'Pendientes de Devolución')

@section('content')
<div class="card-app p-4">
    <h1 class="h3 fw-bold text-slate-900 mb-4">Estudiantes pendientes de devolución</h1>

    <div class="table-responsive-app">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Grado</th>
                    <th>Sección</th>
                    <th>DNI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $dato)
                    <tr>
                        <td>{{ $dato->nombre_completo ?? ($dato->apellidos . ', ' . $dato->nombres) }}</td>
                        <td>{{ $dato->grado }}</td>
                        <td>{{ $dato->seccion }}</td>
                        <td>{{ $dato->dni ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-slate-500">No hay estudiantes pendientes.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
