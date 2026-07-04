@extends('layouts.app')

@section('title', 'Libros')
@section('page-title', 'Gestión de Libros')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
            <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                <i data-lucide="book" class="h-5 w-5"></i>
            </div>
            Catálogo de Libros
        </h2>
        <p class="text-sm text-slate-500 mt-1">Administra el inventario de libros y materiales educativos.</p>
    </div>
    
    <button type="button" onclick="openModal('modalNuevoLibro')" class="inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <i data-lucide="plus" class="h-4 w-4"></i>
        Nuevo Libro
    </button>
</div>

<!-- Filtros -->
<div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm mb-6">
    <form method="GET" action="{{ route('libros.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Buscar</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <i data-lucide="search" class="h-4 w-4"></i>
                </div>
                <input type="text" name="buscar" value="{{ request('buscar') }}" class="block w-full rounded-md border-0 py-1.5 pl-10 pr-3 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-slate-600 sm:text-sm sm:leading-6" placeholder="Nombre, código o área...">
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Área</label>
            <select name="area" class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-slate-600 sm:text-sm sm:leading-6">
                <option value="">Todas las áreas</option>
                @foreach($areas as $area)
                    <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>{{ $area }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-700 mb-1">Grado</label>
            <select name="grado" class="block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-slate-600 sm:text-sm sm:leading-6">
                <option value="">Todos los grados</option>
                @for($i=1; $i<=6; $i++)
                    <option value="{{ $i }}" {{ request('grado') == $i ? 'selected' : '' }}>{{ $i }}° Grado</option>
                @endfor
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors bg-slate-100 text-slate-900 hover:bg-slate-200 h-9 px-4 py-2 flex-1">
                Filtrar
            </button>
            @if(request()->anyFilled(['buscar', 'area', 'grado']))
                <a href="{{ route('libros.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors text-slate-500 hover:text-slate-900 hover:bg-slate-100 h-9 px-3">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-slate-500">
            <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-200">
                <tr>
                    <th scope="col" class="px-6 py-3 font-semibold">Código</th>
                    <th scope="col" class="px-6 py-3 font-semibold">Libro</th>
                    <th scope="col" class="px-6 py-3 font-semibold">Área / Grado</th>
                    <th scope="col" class="px-6 py-3 font-semibold text-center">Disponibles</th>
                    <th scope="col" class="px-6 py-3 font-semibold text-center">Estado</th>
                    <th scope="col" class="px-6 py-3 font-semibold text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($libros as $libro)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900 whitespace-nowrap">
                            {{ $libro->codigo_libro }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900">{{ $libro->nombre }}</div>
                            <div class="text-xs text-slate-500 capitalize mt-0.5">{{ str_replace('_', ' ', $libro->tipo_material) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-medium">{{ $libro->area }}</span>
                            <span class="text-xs text-slate-500 ml-2">{{ $libro->grado }}° {{ ucfirst($libro->nivel) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($libro->cantidad_disponible > 5)
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium ring-1 ring-inset ring-emerald-600/20">
                                    {{ $libro->cantidad_disponible }} / {{ $libro->cantidad_total }}
                                </span>
                            @elseif($libro->cantidad_disponible > 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-medium ring-1 ring-inset ring-amber-600/20">
                                    {{ $libro->cantidad_disponible }} / {{ $libro->cantidad_total }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-red-50 text-red-700 text-xs font-medium ring-1 ring-inset ring-red-600/10">
                                    Agotado
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @switch($libro->estado)
                                @case('activo')
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-md text-xs font-medium text-emerald-700 bg-emerald-50">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Activo
                                    </span>
                                    @break
                                @case('agotado')
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-md text-xs font-medium text-amber-700 bg-amber-50">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Agotado
                                    </span>
                                    @break
                                @case('descontinuado')
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-md text-xs font-medium text-slate-700 bg-slate-100">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-500"></span> Descontinuado
                                    </span>
                                    @break
                                @case('en_revision')
                                    <span class="inline-flex items-center gap-1.5 py-1 px-2 rounded-md text-xs font-medium text-blue-700 bg-blue-50">
                                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span> Revisión
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('libros.show', $libro) }}" class="p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-md transition-colors" title="Ver detalles">
                                    <i data-lucide="eye" class="h-4 w-4"></i>
                                </a>
                                <a href="{{ route('libros.edit', $libro) }}" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors" title="Editar">
                                    <i data-lucide="pencil" class="h-4 w-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                    <i data-lucide="book-x" class="h-6 w-6 text-slate-400"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-900">No se encontraron libros</p>
                                <p class="text-xs text-slate-500 mt-1">Ajusta los filtros o registra un nuevo material educativo.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($libros->hasPages())
    <div class="border-t border-slate-200 px-4 py-3 bg-white">
        {{ $libros->links('pagination::tailwind') }}
    </div>
    @endif
</div>

{{-- =============== MODAL NUEVO LIBRO =============== --}}
<div id="modalNuevoLibro" class="fixed inset-0 z-[999] hidden" role="dialog" aria-modal="true">
    <div class="modal-overlay fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeModal('modalNuevoLibro')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="modal-panel pointer-events-auto w-full max-w-4xl bg-white rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="book" class="h-5 w-5 text-emerald-600"></i>
                    Registrar Nuevo Material
                </h3>
                <button type="button" onclick="closeModal('modalNuevoLibro')" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <form id="formNuevoLibro" class="space-y-6" data-action="{{ route('libros.api.store') }}">
                    @include('libros.form')
                </form>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 shrink-0 flex justify-end gap-3">
                <button type="button" onclick="window.closeModal('modalNuevoLibro')" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition-colors">
                    Cancelar
                </button>
                <button type="button" id="btnGuardarLibro" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 shadow-sm transition-colors flex items-center gap-2 btn-submit-modal">
                    <i data-lucide="save" class="h-4 w-4"></i> Guardar Libro
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
