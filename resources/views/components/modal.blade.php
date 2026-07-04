{{-- Reusable modal component --}}
{{-- Usage: @include('components.modal', ['id' => 'miModal', 'title' => 'Titulo', 'size' => 'lg']) --}}
@props(['id' => 'modal', 'title' => '', 'size' => 'lg'])

@php
    $maxW = match($size) {
        'sm' => 'max-w-md',
        'md' => 'max-w-xl',
        'lg' => 'max-w-3xl',
        'xl' => 'max-w-5xl',
        'full' => 'max-w-6xl',
        default => 'max-w-3xl',
    };
@endphp

<div id="{{ $id }}" class="fixed inset-0 z-[999] hidden" role="dialog" aria-modal="true">
    {{-- Overlay con blur y transición suave --}}
    <div class="modal-overlay fixed inset-0 bg-slate-900/40 backdrop-blur-md transition-all duration-300 opacity-0" onclick="closeModal('{{ $id }}')"></div>
    
    {{-- Panel Contenedor --}}
    <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none sm:p-6">
        <div class="modal-panel pointer-events-auto w-full {{ $maxW }} bg-white rounded-3xl shadow-[0_20px_50px_-12px_rgba(0,0,0,0.3)] ring-1 ring-slate-200 transform transition-all duration-500 cubic-bezier(0.4,0,0.2,1) translate-y-4 sm:translate-y-8 scale-95 opacity-0 max-h-[90vh] flex flex-col overflow-hidden">
            
            {{-- Header Dinámico --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 bg-slate-50/50 shrink-0">
                <h3 class="text-xl font-bold text-slate-800 flex items-center gap-3 tracking-tight" id="{{ $id }}-title">
                    {{ $title }}
                </h3>
                <button type="button" onclick="closeModal('{{ $id }}')" class="group p-2 text-slate-400 hover:text-slate-700 bg-white border border-slate-200 shadow-sm hover:bg-slate-100 rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-slate-300">
                    <i data-lucide="x" class="h-4 w-4 transform group-hover:rotate-90 transition-transform duration-300"></i>
                </button>
            </div>
            
            {{-- Body --}}
            <div class="flex-1 overflow-y-auto px-6 py-6 custom-scrollbar" id="{{ $id }}-body">
                {{ $slot ?? '' }}
            </div>
            
            {{-- Footer (optional) --}}
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 shrink-0 hidden" id="{{ $id }}-footer">
            </div>
        </div>
    </div>
</div>
