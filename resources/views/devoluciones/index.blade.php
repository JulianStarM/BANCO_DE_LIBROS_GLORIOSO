@extends('layouts.app')

@section('title', 'Devoluciones')
@section('page-title', 'Devolución de libros')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Devolución de libros</h1>
            <p class="mt-2 text-sm text-slate-500">Registro y control de materiales educativos devueltos por los estudiantes al finalizar el año.</p>
        </div>
        <button type="button" onclick="openModal('modalNuevaDevolucion')" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">
            <i data-lucide="corner-down-left" class="h-4 w-4"></i>
            Registrar Devolución
        </button>
    </div>

    {{-- Filtros y tabla --}}
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="p-6 border-b border-slate-100">
            <form id="filtroDevoluciones" method="GET" action="{{ route('devoluciones.index') }}" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="relative lg:col-span-2">
                    <i data-lucide="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por DNI, estudiante, código..." class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-11 py-3 text-sm text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" />
                </div>
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    <i data-lucide="filter" class="h-4 w-4 mr-2"></i> Filtrar
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-700">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-4 py-3">Código</th>
                        <th class="px-4 py-3">Entrega Original</th>
                        <th class="px-4 py-3">Estudiante</th>
                        <th class="px-4 py-3 text-center">Devueltos</th>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Responsable</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($devoluciones ?? [] as $devolucion)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-4 font-medium">{{ $devolucion->codigo_general }}</td>
                            <td class="px-4 py-4 text-slate-500">{{ $devolucion->entrega->codigo_general ?? '—' }}</td>
                            <td class="px-4 py-4">{{ $devolucion->estudiante->apellidos ?? '' }}, {{ $devolucion->estudiante->nombres ?? '' }}</td>
                            <td class="px-4 py-4 text-center font-medium">{{ $devolucion->total_libros_devueltos }}</td>
                            <td class="px-4 py-4">{{ optional($devolucion->fecha_devolucion)->format('d/m/Y') }}</td>
                            <td class="px-4 py-4">{{ $devolucion->usuarioRegistro->nombres ?? '—' }}</td>
                            <td class="px-4 py-4 text-right">
                                <a href="{{ route('devoluciones.show', $devolucion) }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800">Ver</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-sm text-slate-500">Sin devoluciones registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($devoluciones) && $devoluciones->hasPages())
            <div class="border-t border-slate-100 bg-slate-50 px-4 py-4">{{ $devoluciones->links('pagination::tailwind') }}</div>
        @endif
    </div>
</div>

{{-- =============== MODAL NUEVA DEVOLUCIÓN =============== --}}
<div id="modalNuevaDevolucion" class="fixed inset-0 z-[999] hidden" role="dialog" aria-modal="true">
    <div class="modal-overlay fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity duration-300 opacity-0" onclick="closeModal('modalNuevaDevolucion')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="modal-panel pointer-events-auto w-full max-w-3xl bg-white rounded-2xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="corner-down-left" class="h-5 w-5 text-blue-600"></i>
                    Registrar Devolución
                </h3>
                <button type="button" onclick="closeModal('modalNuevaDevolucion')" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <form id="formNuevaDevolucion" class="space-y-6"
                      data-action="{{ route('devoluciones.api.store') }}"
                      data-pendientes-url="{{ route('devoluciones.api.pendientes') }}">
                    {{-- 1: Buscar Entrega --}}
                    <div>
                        <h4 class="text-sm font-semibold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2 mb-3">1. Seleccionar Entrega</h4>
                        <div class="relative" id="dev-buscar-wrapper">
                            <div class="relative">
                                <i data-lucide="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"></i>
                                <input type="text" id="modal_dev_buscar" class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm" placeholder="Buscar por DNI o código de entrega..." autocomplete="off">
                            </div>
                            <div id="modal_dev_resultados" class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg hidden max-h-48 overflow-y-auto"></div>
                        </div>
                        <input type="hidden" name="id_entrega" id="modal_id_entrega">
                        <div id="modal_dev_info" class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-xl hidden">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-xs text-slate-500">Entrega seleccionada:</p>
                                    <p class="font-bold text-slate-800 text-base" id="modal_dev_lbl_cod"></p>
                                    <div class="flex gap-4 mt-1 text-sm text-slate-600">
                                        <span>Estudiante: <strong id="modal_dev_lbl_est"></strong></span>
                                        <span>Fecha Entrega: <strong id="modal_dev_lbl_fecha"></strong></span>
                                    </div>
                                </div>
                                <button type="button" id="modal_dev_btn_cambiar" class="text-xs text-blue-600 hover:text-blue-700 font-medium bg-blue-100 hover:bg-blue-200 px-3 py-1.5 rounded-lg transition-colors">Cambiar</button>
                            </div>
                        </div>
                    </div>

                    {{-- 2: Libros a devolver --}}
                    <div id="modal_dev_sec_libros" class="hidden">
                        <h4 class="text-sm font-semibold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2 mb-3">2. Libros a Devolver</h4>
                        <div class="overflow-x-auto border border-slate-200 rounded-xl">
                            <table class="w-full text-left text-sm text-slate-700">
                                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200">
                                    <tr>
                                        <th class="px-4 py-2 w-10">#</th>
                                        <th class="px-4 py-2">Libro</th>
                                        <th class="px-4 py-2 text-center">Estado de Devolución</th>
                                    </tr>
                                </thead>
                                <tbody id="modal_dev_lista_libros" class="divide-y divide-slate-100 bg-white">
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- 3: Quien devuelve --}}
                    <div id="modal_dev_sec_firmante" class="hidden">
                        <h4 class="text-sm font-semibold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2 mb-3">3. ¿Quién devuelve?</h4>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="tipo_firmante" value="estudiante" class="peer sr-only" checked>
                                <div class="p-3 border border-slate-200 rounded-xl hover:bg-slate-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all flex items-center gap-2 text-sm">
                                    <i data-lucide="user" class="h-4 w-4 text-blue-600"></i>
                                    <span class="font-medium text-slate-800">Estudiante</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="tipo_firmante" value="apoderado" class="peer sr-only">
                                <div class="p-3 border border-slate-200 rounded-xl hover:bg-slate-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all flex items-center gap-2 text-sm">
                                    <i data-lucide="users" class="h-4 w-4 text-blue-600"></i>
                                    <span class="font-medium text-slate-800">Apoderado</span>
                                </div>
                            </label>
                        </div>
                        <div><label class="block text-xs text-slate-500 mb-1">Observaciones</label><textarea name="observaciones" rows="2" class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Opcional"></textarea></div>
                    </div>
                </form>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 shrink-0 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalNuevaDevolucion')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50">Cancelar</button>
                <button type="button" id="modal_btn_guardar_dev" class="px-5 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 flex items-center gap-2 opacity-50 cursor-not-allowed btn-submit-modal" disabled>
                    <i data-lucide="check-circle" class="h-4 w-4"></i> Registrar Devolución
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@endsection
