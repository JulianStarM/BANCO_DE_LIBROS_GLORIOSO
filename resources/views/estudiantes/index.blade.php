@extends('layouts.app')

@section('title', 'Estudiantes')
@section('page-title', 'Gestión de estudiantes')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
            <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                <i data-lucide="graduation-cap" class="h-5 w-5"></i>
            </div>
            Estudiantes
        </h2>
        <p class="text-sm text-slate-500 mt-1">Administra el registro y datos de los estudiantes</p>
    </div>
    
    <button type="button" onclick="openModal('modalNuevoEstudiante')" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <i data-lucide="plus" class="h-4 w-4"></i>
        Nuevo estudiante
    </button>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] mb-6 p-4">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
        <div class="md:col-span-4">
            <label class="block text-xs font-medium text-slate-500 mb-1">Búsqueda</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="h-4 w-4 text-slate-400"></i>
                </div>
                <input type="text" name="q" value="{{ $q }}" class="block w-full pl-10 pr-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors" placeholder="Nombres, apellidos, DNI, código...">
            </div>
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Nivel</label>
            <select name="nivel" class="block w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                <option value="">Todos</option>
                @foreach(['Inicial','Primaria','Secundaria'] as $n)
                    <option value="{{ strtolower($n) }}" @selected($nivel === strtolower($n) || $nivel === $n)>{{ $n }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Grado</label>
            <input type="text" name="grado" value="{{ $grado }}" class="block w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors" placeholder="Ej. 4">
        </div>
        
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Sección</label>
            <input type="text" name="seccion" value="{{ $seccion }}" class="block w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors" placeholder="Ej. A">
        </div>
        
        <div class="md:col-span-2">
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-lg shadow-sm transition-colors focus:ring-2 focus:ring-slate-200">
                <i data-lucide="filter" class="h-4 w-4"></i>
                Filtrar
            </button>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] overflow-hidden">
    <div class="w-full overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-100">
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Código / DNI</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Estudiante</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Nivel</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-center">G/S</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Turno</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($estudiantes as $e)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-3">
                            <div class="text-sm font-medium text-slate-700">{{ $e->codigo_estudiante ?? $e->codigo ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $e->dni ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-3">
                            <div class="text-sm font-semibold text-slate-800">{{ $e->apellidos }}</div>
                            <div class="text-xs text-slate-500">{{ $e->nombres }}</div>
                        </td>
                        <td class="px-6 py-3 text-sm text-slate-600 capitalize">
                            {{ $e->nivel }}
                        </td>
                        <td class="px-6 py-3 text-sm text-slate-600 text-center font-medium">
                            {{ $e->grado }} "{{ $e->seccion }}"
                        </td>
                        <td class="px-6 py-3 text-sm text-slate-600 capitalize">
                            {{ $e->turno ?? '-' }}
                        </td>
                        <td class="px-6 py-3">
                            @if($e->estado === 'activo' || $e->estado == 1)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Activo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 ring-1 ring-inset ring-rose-600/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                    {{ ucfirst($e->estado) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('estudiantes.show', $e) }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Ver perfil">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </a>
                                <a href="{{ route('estudiantes.edit', $e) }}" class="p-1.5 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Editar">
                                    <i data-lucide="pencil" class="h-4 w-4"></i>
                                </a>
                                <form action="{{ route('estudiantes.destroy', $e) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este estudiante?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Eliminar">
                                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center mb-3">
                                    <i data-lucide="users" class="h-6 w-6 text-slate-400"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-900">No se encontraron estudiantes</p>
                                <p class="text-xs text-slate-500 mt-1">Ajusta los filtros de búsqueda o registra un nuevo estudiante.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($estudiantes->hasPages())
    <div class="border-t border-slate-100 px-6 py-4 bg-slate-50/50">
        {{ $estudiantes->links('pagination::tailwind') }}
    </div>
    @endif
</div>

{{-- =============== MODAL NUEVO ESTUDIANTE =============== --}}
<div id="modalNuevoEstudiante" class="fixed inset-0 z-[999] hidden" role="dialog" aria-modal="true">
    <div class="modal-overlay fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeModal('modalNuevoEstudiante')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="modal-panel pointer-events-auto w-full max-w-3xl bg-white rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="graduation-cap" class="h-5 w-5 text-blue-600"></i>
                    Nuevo Estudiante
                </h3>
                <button type="button" onclick="closeModal('modalNuevoEstudiante')" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <form id="formNuevoEstudiante" class="space-y-4" data-action="{{ route('estudiantes.api.store') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div><label class="block text-xs font-medium text-slate-600 mb-1">Código *</label><input type="text" name="codigo_estudiante" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500" required></div>
                        <div><label class="block text-xs font-medium text-slate-600 mb-1">DNI *</label><input type="text" name="dni" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500" required maxlength="15"></div>
                        <div><label class="block text-xs font-medium text-slate-600 mb-1">Fecha nacimiento</label><input type="date" name="fecha_nacimiento" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-xs font-medium text-slate-600 mb-1">Nombres *</label><input type="text" name="nombres" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500" required></div>
                        <div><label class="block text-xs font-medium text-slate-600 mb-1">Apellidos *</label><input type="text" name="apellidos" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500" required></div>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div><label class="block text-xs font-medium text-slate-600 mb-1">Sexo *</label>
                            <select name="sexo" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required><option value="M">Masculino</option><option value="F">Femenino</option></select>
                        </div>
                        <div><label class="block text-xs font-medium text-slate-600 mb-1">Nivel *</label>
                            <select name="nivel" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required><option value="secundaria">Secundaria</option><option value="primaria">Primaria</option><option value="inicial">Inicial</option></select>
                        </div>
                        <div><label class="block text-xs font-medium text-slate-600 mb-1">Grado *</label><input type="number" name="grado" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required min="1" max="6"></div>
                        <div><label class="block text-xs font-medium text-slate-600 mb-1">Sección *</label><input type="text" name="seccion" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" required maxlength="5" placeholder="A"></div>
                        <div><label class="block text-xs font-medium text-slate-600 mb-1">Turno</label>
                            <select name="turno" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"><option value="">--</option><option value="mañana">Mañana</option><option value="tarde">Tarde</option></select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div><label class="block text-xs font-medium text-slate-600 mb-1">Dirección</label><input type="text" name="direccion" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
                        <div><label class="block text-xs font-medium text-slate-600 mb-1">Teléfono</label><input type="text" name="telefono" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></div>
                        <div><label class="block text-xs font-medium text-slate-600 mb-1">Año Escolar (ID)</label><input type="number" name="id_anio_escolar" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" value="1"></div>
                    </div>
                    <div><label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="estado" value="1" checked class="rounded border-slate-300 text-blue-600"> Estudiante activo</label></div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 shrink-0 flex justify-end gap-3">
                <button type="button" onclick="window.closeModal('modalNuevoEstudiante')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50">Cancelar</button>
                <button type="button" id="btnGuardarEstudiante" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 flex items-center gap-2 btn-submit-modal">
                    <i data-lucide="save" class="h-4 w-4"></i> Guardar Estudiante
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
