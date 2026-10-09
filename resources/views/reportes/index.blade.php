@extends('layouts.app')

@section('title', 'Reportes')
@section('page-title', 'Reportes')

@section('content')
<div class="report-container animate-fade-in">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-slate-900 mb-1">Centro de reportes</h1>
        <p class="text-sm text-slate-500 mb-0">Consulta informes de entregas, devoluciones, inventario e incidencias.</p>
    </div>

    <form method="GET" action="{{ route('reportes.index') }}" class="filter-bar mb-4">
        <div class="filter-bar-title">
            <i data-lucide="calendar" class="icon"></i>
            <span>Año escolar</span>
        </div>
        <div class="filter-bar-content">
            <div class="form-group-app" style="max-width: 16rem;">
                <select name="anio" class="form-select-app" onchange="this.form.submit()">
                    <option value="">Todos los años</option>
                    @foreach($anios as $id => $anio)
                        <option value="{{ $id }}" {{ request('anio') == $id ? 'selected' : '' }}>{{ $anio }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <div class="report-dashboard-grid mb-4">
        <div class="report-stat-card">
            <span class="report-stat-icon navy"><i data-lucide="library" class="icon"></i></span>
            <div class="report-stat-value">{{ number_format($resumen['totalLibros']) }}</div>
            <div class="report-stat-label">Total libros</div>
        </div>
        <div class="report-stat-card">
            <span class="report-stat-icon primary"><i data-lucide="book-open" class="icon"></i></span>
            <div class="report-stat-value">{{ number_format($resumen['entregados']) }}</div>
            <div class="report-stat-label">Entregados</div>
        </div>
        <div class="report-stat-card">
            <span class="report-stat-icon success"><i data-lucide="check-circle" class="icon"></i></span>
            <div class="report-stat-value">{{ number_format($resumen['devueltos']) }}</div>
            <div class="report-stat-label">Devueltos</div>
        </div>
        <div class="report-stat-card">
            <span class="report-stat-icon warning"><i data-lucide="clock" class="icon"></i></span>
            <div class="report-stat-value">{{ number_format($resumen['pendientes']) }}</div>
            <div class="report-stat-label">Pendientes</div>
        </div>
        <div class="report-stat-card">
            <span class="report-stat-icon danger"><i data-lucide="alert-circle" class="icon"></i></span>
            <div class="report-stat-value">{{ number_format($resumen['perdidos']) }}</div>
            <div class="report-stat-label">Perdidos</div>
        </div>
        <div class="report-stat-card">
            <span class="report-stat-icon warning"><i data-lucide="alert-triangle" class="icon"></i></span>
            <div class="report-stat-value">{{ number_format($resumen['deteriorados']) }}</div>
            <div class="report-stat-label">Deteriorados</div>
        </div>
        <div class="report-stat-card">
            <span class="report-stat-icon info"><i data-lucide="package" class="icon"></i></span>
            <div class="report-stat-value">{{ number_format($resumen['disponibles']) }}</div>
            <div class="report-stat-label">Disponibles</div>
        </div>
    </div>

    <div class="d-flex flex-column gap-4">
        <section class="report-category-section">
            <h2 class="report-category-title"><i data-lucide="package" class="icon"></i> Entregas y devoluciones</h2>
            <div class="report-category-grid">
                <a href="{{ route('reportes.por_grado') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="bar-chart-2" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Por grado y sección</h3>
                        <p class="report-desc">Entregas y devoluciones agrupadas por grado y sección.</p>
                    </div>
                </a>
                <a href="{{ route('reportes.por_estudiante') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="users" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Por estudiante</h3>
                        <p class="report-desc">Resumen de entregas por cada estudiante.</p>
                    </div>
                </a>
                <a href="{{ route('reportes.por_libro') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="book" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Por libro</h3>
                        <p class="report-desc">Cantidad de ejemplares entregados por libro.</p>
                    </div>
                </a>
                <a href="{{ route('reportes.entregas') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="arrow-right-circle" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Entregas realizadas</h3>
                        <p class="report-desc">Listado detallado de todas las entregas.</p>
                    </div>
                </a>
                <a href="{{ route('reportes.devoluciones') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="arrow-left-circle" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Devoluciones realizadas</h3>
                        <p class="report-desc">Listado detallado de todas las devoluciones.</p>
                    </div>
                </a>
                <a href="{{ route('reportes.pendientes') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="clock" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Pendientes</h3>
                        <p class="report-desc">Estudiantes con libros aún no devueltos.</p>
                    </div>
                </a>
            </div>
        </section>

        <section class="report-category-section">
            <h2 class="report-category-title"><i data-lucide="book-marked" class="icon"></i> Inventario</h2>
            <div class="report-category-grid">
                <a href="{{ route('reportes.stock') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="package" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Stock de libros</h3>
                        <p class="report-desc">Existencias y disponibilidad por libro.</p>
                    </div>
                </a>
                <a href="{{ route('reportes.inventario') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="clipboard-list" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Inventario general</h3>
                        <p class="report-desc">Inventario completo de todos los materiales.</p>
                    </div>
                </a>
                <a href="{{ route('reportes.stock_critico') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="alert-triangle" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Stock crítico</h3>
                        <p class="report-desc">Libros con existencias bajas.</p>
                    </div>
                </a>
            </div>
        </section>

        <section class="report-category-section">
            <h2 class="report-category-title"><i data-lucide="graduation-cap" class="icon"></i> Estudiantes</h2>
            <div class="report-category-grid">
                <a href="{{ route('reportes.situacion_estudiantes') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="user-check" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Situación de estudiantes</h3>
                        <p class="report-desc">Estado académico y movimientos de cada estudiante.</p>
                    </div>
                </a>
                <a href="{{ route('reportes.pendientes') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="user-x" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Estudiantes con pendientes</h3>
                        <p class="report-desc">Listado de estudiantes con deuda de libros.</p>
                    </div>
                </a>
                <a href="{{ route('reportes.historial_estudiante') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="history" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Historial por estudiante</h3>
                        <p class="report-desc">Entregas y devoluciones de un estudiante seleccionado.</p>
                    </div>
                </a>
            </div>
        </section>

        <section class="report-category-section">
            <h2 class="report-category-title"><i data-lucide="shield-alert" class="icon"></i> Incidencias</h2>
            <div class="report-category-grid">
                <a href="{{ route('reportes.perdidos') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="x-circle" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Libros perdidos</h3>
                        <p class="report-desc">Registro de libros reportados como perdidos.</p>
                    </div>
                </a>
                <a href="{{ route('reportes.deteriorados') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="alert-triangle" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Libros deteriorados</h3>
                        <p class="report-desc">Registro de libros en mal estado.</p>
                    </div>
                </a>
                <a href="{{ route('reportes.bajas') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="trash-2" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Libros dados de baja</h3>
                        <p class="report-desc">Materiales descontinuados o dados de baja.</p>
                    </div>
                </a>
            </div>
        </section>

        <section class="report-category-section">
            <h2 class="report-category-title"><i data-lucide="calendar-days" class="icon"></i> Históricos</h2>
            <div class="report-category-grid">
                <a href="{{ route('reportes.por_anio') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="calendar" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Por año escolar</h3>
                        <p class="report-desc">Resumen consolidado por cada año escolar.</p>
                    </div>
                </a>
                <a href="{{ route('reportes.comparativo') }}" class="report-card">
                    <span class="report-icon"><i data-lucide="bar-chart" class="icon"></i></span>
                    <div>
                        <h3 class="report-title">Comparativo entre años</h3>
                        <p class="report-desc">Comparación de indicadores entre dos años.</p>
                    </div>
                </a>
            </div>
        </section>
    </div>
</div>
@endsection
