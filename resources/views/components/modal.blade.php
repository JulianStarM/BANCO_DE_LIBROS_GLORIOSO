{{-- Reusable modal component --}}
{{-- Usage: @include('components.modal', ['id' => 'miModal', 'title' => 'Titulo', 'size' => 'lg']) --}}
@props(['id' => 'modal', 'title' => '', 'size' => 'lg', 'icon' => null, 'subtitle' => null])

@php
    $sizeClass = match($size) {
        'sm' => '',
        'md' => '',
        'lg' => 'modal-wide',
        'xl' => 'modal-wide',
        'full' => 'modal-wide',
        default => 'modal-wide',
    };
    $hasFooter = isset($footer) && trim($footer->toHtml()) !== '';
@endphp

<div id="{{ $id }}" class="modal-app {{ $sizeClass }} hidden" role="dialog" aria-modal="true">
    <div class="modal-overlay" onclick="closeModal('{{ $id }}')"></div>

    <div class="modal-panel">
        <div class="modal-header">
            <h3 class="modal-title" id="{{ $id }}-title">
                @if($icon)
                    <span class="modal-title-icon">
                        <i data-lucide="{{ $icon }}" class="icon"></i>
                    </span>
                @endif
                <span class="modal-title-text">
                    {{ $title }}
                    @if($subtitle)
                        <span class="modal-subtitle">{{ $subtitle }}</span>
                    @endif
                </span>
            </h3>
            <button type="button" onclick="closeModal('{{ $id }}')" class="modal-close" aria-label="Cerrar">
                <i data-lucide="x" class="icon"></i>
            </button>
        </div>

        <div class="modal-body" id="{{ $id }}-body">
            {{ $slot ?? '' }}
        </div>

        <div class="modal-footer {{ $hasFooter ? '' : 'd-none' }}" id="{{ $id }}-footer">
            {{ $footer ?? '' }}
        </div>
    </div>
</div>
