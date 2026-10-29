{{-- Reusable CRUD page wrapper for Biblioteca General config pages --}}
@props([
    'title' => '',
    'subtitle' => '',
    'icon' => 'settings',
    'createLabel' => 'Nuevo',
    'modalCreateId' => 'modalCrear',
    'modalEditId' => 'modalEditar',
    'routePrefix' => '',
])

<div class="row align-items-center mb-4">
    <div class="col-md-8">
        <h2 class="h3 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-1">
            <span class="d-flex align-items-center justify-content-center rounded bg-success bg-opacity-10 text-success" style="width: 2.5rem; height: 2.5rem; font-size: 1.125rem;">
                <i data-lucide="{{ $icon }}" class="icon"></i>
            </span>
            {{ $title }}
        </h2>
        <p class="text-sm text-slate-500 mb-0">{{ $subtitle }}</p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        {{ $headerActions ?? '' }}
        <button type="button" onclick="openModal('{{ $modalCreateId }}')" class="btn-app btn-app-primary">
            <i data-lucide="plus" class="icon"></i>
            {{ $createLabel }}
        </button>
    </div>
</div>

@if(isset($filters))
<div class="filter-bar mb-4">
    <form method="GET" class="row g-3 align-items-end w-100">
        {{ $filters }}
        <div class="col-lg-2">
            <button type="submit" class="btn-app btn-app-secondary w-100">Filtrar</button>
        </div>
        <div class="col-lg-2">
            <a href="{{ route($routePrefix.'.index') }}" class="btn-app btn-app-light w-100">Limpiar</a>
        </div>
    </form>
</div>
@endif

<div class="table-responsive-app">
    <table class="table-app">
        <thead>
            <tr>
                {{ $tableHead }}
                <th class="text-end">Acciones</th>
            </tr>
        </thead>
        <tbody>
            {{ $tableBody }}
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $pagination ?? '' }}
</div>

{{-- Modal crear --}}
@component('components.modal', ['id' => $modalCreateId, 'title' => 'Registrar '.$title, 'icon' => 'plus'])
    <form method="POST" action="{{ route($routePrefix.'.store') }}">
        @csrf
        {{ $modalCreateBody }}
        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('{{ $modalCreateId }}')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">Guardar</button>
        </div>
    </form>
@endcomponent

{{-- Modal editar --}}
@component('components.modal', ['id' => $modalEditId, 'title' => 'Editar '.$title, 'icon' => 'pencil'])
    <form id="formEditar{{ $modalEditId }}" method="POST">
        @csrf
        @method('PUT')
        {{ $modalEditBody }}
        <div class="d-flex justify-content-end gap-2 pt-3 mt-4" style="border-top: 1px solid var(--border-color);">
            <button type="button" onclick="closeModal('{{ $modalEditId }}')" class="btn-app btn-app-secondary">Cancelar</button>
            <button type="submit" class="btn-app btn-app-primary">Guardar cambios</button>
        </div>
    </form>
@endcomponent

@push('scripts')
<script>
    function editarRegistro{{ $modalEditId }}(ruta, datos) {
        document.getElementById('formEditar{{ $modalEditId }}').action = ruta;
        for (const [campo, valor] of Object.entries(datos)) {
            const input = document.querySelector('#{{ $modalEditId }} [name="' + campo + '"]');
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = Boolean(valor);
                } else {
                    input.value = valor ?? '';
                }
            }
        }
        openModal('{{ $modalEditId }}');
    }
</script>
@endpush
