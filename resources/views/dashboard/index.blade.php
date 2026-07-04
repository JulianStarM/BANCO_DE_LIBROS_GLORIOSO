@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
        <div class="space-y-3">
            <p class="text-sm uppercase tracking-[0.3em] font-semibold text-slate-400">Sistema de Banco de Libros</p>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Panel de Control</h1>
        </div>
    </div>

    <div class="mt-8 rounded-3xl border border-slate-200/80 bg-gradient-to-r from-blue-50/60 via-white to-emerald-50/40 p-7 shadow-sm">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] font-semibold text-slate-400">Resumen institucional</p>
                <p class="mt-3 text-lg text-slate-700 font-medium">Banco de Libros — I.E. Glorioso San Carlos de Puno.</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 shrink-0">
                <i data-lucide="building-2" class="h-6 w-6"></i>
            </div>
        </div>
    </div>
</div>

@php
    $stats = [
        ['label' => 'Estudiantes', 'value' => $totalEstudiantes ?? 0, 'href' => route('estudiantes.index'), 'icon' => 'graduation-cap', 'color' => 'bg-sky-50 text-sky-600', 'border' => 'hover:border-sky-300'],
        ['label' => 'Títulos', 'value' => $totalLibros ?? 0, 'href' => route('libros.index'), 'icon' => 'book-open', 'color' => 'bg-emerald-50 text-emerald-600', 'border' => 'hover:border-emerald-300'],
        ['label' => 'Stock disponible', 'value' => $totalEjemplares ?? 0, 'href' => route('libros.index'), 'icon' => 'package', 'color' => 'bg-amber-50 text-amber-600', 'border' => 'hover:border-amber-300'],
        ['label' => 'Entregas', 'value' => $totalEntregas ?? 0, 'href' => route('entregas.index'), 'icon' => 'clipboard-list', 'color' => 'bg-violet-50 text-violet-600', 'border' => 'hover:border-violet-300'],
        ['label' => 'Libros entregados', 'value' => $librosEntregados ?? 0, 'href' => route('entregas.index'), 'icon' => 'check-circle', 'color' => 'bg-emerald-50 text-emerald-600', 'border' => 'hover:border-emerald-300'],
        ['label' => 'Estudiantes pendientes', 'value' => $estudiantesPendientes ?? 0, 'href' => route('entregas.index'), 'icon' => 'alert-triangle', 'color' => 'bg-rose-50 text-rose-600', 'border' => 'hover:border-rose-300'],
    ];
@endphp

<div class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @foreach($stats as $stat)
            <a href="{{ $stat['href'] }}" class="group block overflow-hidden rounded-3xl border border-slate-200 bg-white p-7 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl {{ $stat['border'] }}">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.25em] font-semibold text-slate-400">{{ $stat['label'] }}</p>
                        <p class="mt-4 text-5xl font-bold text-slate-900 tracking-tight">{{ $stat['value'] }}</p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $stat['color'] }} transition-transform duration-300 group-hover:scale-110">
                        <i data-lucide="{{ $stat['icon'] }}" class="h-7 w-7"></i>
                    </div>
                </div>
                <div class="mt-5 flex items-center gap-2 text-sm font-semibold text-slate-400 transition-colors duration-300 group-hover:text-slate-900">
                    Ver detalle
                    <i data-lucide="arrow-right" class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"></i>
                </div>
            </a>
        @endforeach
    </div>

    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-lg font-bold text-slate-900">Avance de entrega</p>
                    <p class="mt-2 text-base text-slate-500">{{ $estudiantesConEntrega ?? 0 }} de {{ $totalEstudiantes ?? 0 }} estudiantes han recibido sus libros.</p>
                </div>
                <span class="rounded-full bg-emerald-50 px-4 py-2 text-lg font-bold text-emerald-700">{{ $avance ?? 0 }}%</span>
            </div>
            <div class="mt-6 h-4 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-emerald-400 transition-all duration-700 ease-out" style="width: {{ $avance ?? 0 }}%"></div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-lg font-bold text-slate-900">Alertas de stock</p>
                    <p class="mt-2 text-base text-slate-500">Títulos con stock disponible bajo. Total: {{ $stockTotal ?? 0 }}</p>
                </div>
                <span class="rounded-full bg-rose-50 px-4 py-2 text-lg font-bold text-rose-700">{{ $stockBajo->count() ?? 0 }}</span>
            </div>
            @if(($stockBajo->count() ?? 0) === 0)
                <div class="mt-6 rounded-3xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-base text-slate-500">Sin alertas de stock bajo.</div>
            @else
                <div class="mt-6 grid gap-3">
                    @foreach($stockBajo->take(5) as $libro)
                        <div class="flex items-center justify-between rounded-2xl bg-slate-50 p-4 text-base text-slate-700 transition hover:bg-slate-100">
                            <div>
                                <p class="font-semibold">{{ $libro->titulo ?? $libro->codigo }}</p>
                                <p class="text-sm text-slate-500 mt-1">{{ $libro->area ?? 'Sin área' }} · Grado {{ $libro->grado ?? '-' }}</p>
                            </div>
                            <span class="rounded-full bg-white px-4 py-1.5 text-sm font-bold text-slate-700 shadow-sm border border-slate-200">{{ $libro->cantidad_disponible ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

