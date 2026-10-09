@extends('layouts.app')

@section('title', $titulo ?? 'Reporte')
@section('page-title', $titulo ?? 'Reporte')

@section('content')
<div class="report-container animate-fade-in">
    <div class="report-header">
        <div class="report-header-content">
            <h1 class="report-heading">REPORTE DE {{ Str::upper($titulo ?? 'REPORTE') }}</h1>
            <p class="report-subheading">
                Año escolar: {{ $anioEscolar ?? 'Todos' }}
                @if(!empty($subtitulo))
                    <span class="report-subheading-separator">|</span> {{ $subtitulo }}
                @endif
            </p>
        </div>
        <div class="report-actions">
            <a href="{{ route('reportes.index') }}" class="btn-app btn-app-light btn-app-sm">
                <i data-lucide="arrow-left" class="icon"></i>
                <span>Volver</span>
            </a>
            <button type="button" class="btn-app btn-app-secondary btn-app-sm" onclick="window.print()">
                <i data-lucide="printer" class="icon"></i>
                <span>Imprimir</span>
            </button>
            <button type="button" class="btn-app btn-app-primary btn-app-sm" onclick="alert('Exportación próximamente')">
                <i data-lucide="download" class="icon"></i>
                <span>Exportar</span>
            </button>
        </div>
    </div>

    @hasSection('filtros')
        @yield('filtros')
    @endif

    <div class="report-table-wrapper">
        @yield('tabla')
    </div>

    @hasSection('paginacion')
        <div class="report-pagination">
            @yield('paginacion')
        </div>
    @endif
</div>
@endsection
