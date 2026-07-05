@extends('layouts.app')

@section('title', 'Stock de Libros')
@section('page-title', 'Stock de Libros')

@section('content')
<div class="card-app p-4">
    <h1 class="h3 fw-bold text-slate-900 mb-4">Stock de libros</h1>

    <div class="table-responsive-app">
        <table class="table-app">
            <thead>
                <tr>
                    <th>Grado</th>
                    <th>Nivel</th>
                    <th>Área</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($datos as $dato)
                    <tr>
                        <td>{{ $dato->grado }}</td>
                        <td>{{ $dato->nivel }}</td>
                        <td>{{ $dato->area }}</td>
                        <td>{{ $dato->total ?? 0 }}</td>
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
