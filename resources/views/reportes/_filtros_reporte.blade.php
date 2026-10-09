@php
$camposDisponibles = $campos ?? ['anio', 'grado', 'seccion', 'nivel'];
@endphp

<form method="GET" action="{{ $ruta }}" class="filter-bar">
    <div class="filter-bar-title">
        <i data-lucide="filter" class="icon"></i>
        <span>Filtros</span>
    </div>
    <div class="filter-bar-content">
        @if(in_array('anio', $camposDisponibles))
            <div class="form-group-app" style="min-width: 8rem;">
                <label class="form-label-app">Año escolar</label>
                <select name="anio" class="form-select-app">
                    <option value="">Todos</option>
                    @foreach($anios as $id => $anio)
                        <option value="{{ $id }}" {{ request('anio') == $id ? 'selected' : '' }}>{{ $anio }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if(in_array('grado', $camposDisponibles))
            <div class="form-group-app" style="min-width: 6rem;">
                <label class="form-label-app">Grado</label>
                <select name="grado" class="form-select-app">
                    <option value="">Todos</option>
                    @foreach($grados as $grado)
                        <option value="{{ $grado }}" {{ request('grado') == $grado ? 'selected' : '' }}>{{ $grado }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if(in_array('seccion', $camposDisponibles))
            <div class="form-group-app" style="min-width: 6rem;">
                <label class="form-label-app">Sección</label>
                <select name="seccion" class="form-select-app">
                    <option value="">Todas</option>
                    @foreach($secciones ?? [] as $seccion)
                        <option value="{{ $seccion }}" {{ request('seccion') == $seccion ? 'selected' : '' }}>{{ $seccion }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if(in_array('nivel', $camposDisponibles))
            <div class="form-group-app" style="min-width: 8rem;">
                <label class="form-label-app">Nivel</label>
                <select name="nivel" class="form-select-app">
                    <option value="">Todos</option>
                    @foreach($niveles as $nivel)
                        <option value="{{ $nivel }}" {{ request('nivel') == $nivel ? 'selected' : '' }}>{{ ucfirst($nivel) }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if(in_array('estado', $camposDisponibles))
            <div class="form-group-app" style="min-width: 8rem;">
                <label class="form-label-app">Estado</label>
                <select name="estado" class="form-select-app">
                    <option value="">Todos</option>
                    @foreach($estados ?? [] as $value => $label)
                        <option value="{{ $value }}" {{ request('estado') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if(in_array('area', $camposDisponibles))
            <div class="form-group-app" style="min-width: 10rem;">
                <label class="form-label-app">Área</label>
                <input type="text" name="area" class="form-control-app" placeholder="Ej. Matemática" value="{{ request('area') }}">
            </div>
        @endif

        @if(in_array('desde', $camposDisponibles))
            <div class="form-group-app" style="min-width: 8rem;">
                <label class="form-label-app">Desde</label>
                <input type="date" name="desde" class="form-control-app" value="{{ request('desde') }}">
            </div>
        @endif

        @if(in_array('hasta', $camposDisponibles))
            <div class="form-group-app" style="min-width: 8rem;">
                <label class="form-label-app">Hasta</label>
                <input type="date" name="hasta" class="form-control-app" value="{{ request('hasta') }}">
            </div>
        @endif

        @if(in_array('umbral', $camposDisponibles))
            <div class="form-group-app" style="min-width: 7rem;">
                <label class="form-label-app">Umbral</label>
                <input type="number" name="umbral" class="form-control-app" min="0" value="{{ request('umbral', 5) }}">
            </div>
        @endif

        @if(in_array('buscar', $camposDisponibles))
            <div class="form-group-app" style="min-width: 12rem;">
                <label class="form-label-app">Buscar</label>
                <input type="text" name="buscar" class="form-control-app" placeholder="Nombre, DNI, código..." value="{{ request('buscar') }}">
            </div>
        @endif

        <div class="filter-bar-actions">
            <button type="submit" class="btn-app btn-app-primary btn-app-sm">
                <i data-lucide="search" class="icon"></i>
                <span>Filtrar</span>
            </button>
        </div>
    </div>
</form>
