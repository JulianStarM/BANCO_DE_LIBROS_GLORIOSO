@extends('layouts.app')

@section('title', 'Registrar Entrega')
@section('page-title', 'Registrar Entrega de Libros')

@section('content')
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-md-between gap-3 mb-4">
    <div>
        <h2 class="h4 fw-bold text-slate-800 d-flex align-items-center gap-2 mb-0">
            <div class="d-flex align-items-center justify-content-center rounded-3" style="width: 2rem; height: 2rem; background-color: var(--color-primary-light); color: var(--color-primary);">
                <i data-lucide="clipboard-list" class="icon"></i>
            </div>
            Registrar Entrega
        </h2>
        <p class="small text-slate-500 mt-1 mb-0">Registra la entrega de libros a un estudiante.</p>
    </div>
</div>

<div class="card-app p-4">
    <form action="{{ route('entregas.store') }}" method="POST" id="form-entrega">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <div class="fw-bold mb-2">Hay errores en el formulario:</div>
                <ul class="list-disc list-inside mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Paso 1: Buscar Estudiante -->
        <div class="mb-4">
            <h3 class="section-title">1. Seleccionar Estudiante</h3>

            <div class="search-wrapper">
                <label class="form-label-app">Buscar por DNI o Nombres</label>
                <div class="position-relative">
                    <div class="position-absolute top-50 start-0 translate-middle-y ms-3">
                        <i data-lucide="search" class="icon text-slate-400"></i>
                    </div>
                    <input type="text" id="buscar_estudiante" class="form-control-app ps-5" placeholder="Escribe para buscar..." autocomplete="off">
                </div>
                <!-- Resultados de búsqueda -->
                <div id="resultados_busqueda" class="search-results d-none">
                    <!-- Js lo llena -->
                </div>
            </div>

            <!-- Datos del estudiante seleccionado -->
            <div id="estudiante_info" class="info-box d-none position-relative">
                <input type="hidden" name="id_estudiante" id="id_estudiante" value="{{ old('id_estudiante') }}">
                <div class="d-flex align-items-start justify-content-between">
                    <div>
                        <p class="info-label">Estudiante Seleccionado:</p>
                        <p class="info-name" id="lbl_nombre_estudiante">-</p>
                        <div class="d-flex gap-4 info-meta">
                            <p class="mb-0"><span class="fw-medium">DNI:</span> <span id="lbl_dni_estudiante">-</span></p>
                            <p class="mb-0"><span class="fw-medium">Grado:</span> <span id="lbl_grado_estudiante">-</span></p>
                            <p class="mb-0"><span class="fw-medium">Nivel:</span> <span id="lbl_nivel_estudiante">-</span></p>
                        </div>
                    </div>
                    <button type="button" id="btn_cambiar_estudiante" class="btn btn-sm btn-outline-primary">Cambiar</button>
                </div>

                <div id="alerta_entrega" class="alert alert-warning d-none mt-3 d-flex align-items-start gap-2 py-2 small">
                    <i data-lucide="alert-triangle" class="icon flex-shrink-0 mt-1"></i>
                    <div>
                        <strong>¡Atención!</strong> Este estudiante ya tiene una entrega registrada.
                    </div>
                </div>
            </div>
        </div>

        <!-- Paso 2: Libros a Entregar -->
        <div id="seccion_libros" class="d-none mb-4">
            <h3 class="section-title d-flex align-items-center justify-content-between">
                <span>2. Libros Asignados</span>
                <span class="badge-counter" id="lbl_total_libros">0 libros</span>
            </h3>

            <div id="loading_libros" class="d-none text-center py-5 text-muted">
                <i data-lucide="loader-2" class="icon spin mb-2"></i>
                <div>Cargando libros del grado...</div>
            </div>

            <div id="lista_libros" class="d-flex flex-column gap-2">
                <!-- Js lo llena -->
                <p class="small text-muted fst-italic py-3 mb-0">Selecciona un estudiante para cargar sus libros.</p>
            </div>
        </div>

        <!-- Paso 3: Datos de Recepción -->
        <div id="seccion_recepcion" class="d-none mb-4">
            <h3 class="section-title">3. ¿Quién recibe?</h3>

            <div class="radio-group mb-4">
                <label class="radio-card">
                    <input type="radio" name="tipo_firmante" value="estudiante" class="radio-card-input" checked>
                    <div class="radio-card-body">
                        <div class="radio-card-icon" style="width: 2.5rem; height: 2.5rem;">
                            <i data-lucide="user" class="icon"></i>
                        </div>
                        <div>
                            <p class="radio-card-text mb-0">Estudiante</p>
                            <p class="small text-slate-500 mb-0">Recibe personalmente</p>
                        </div>
                    </div>
                </label>

                <label class="radio-card">
                    <input type="radio" name="tipo_firmante" value="apoderado" class="radio-card-input">
                    <div class="radio-card-body">
                        <div class="radio-card-icon" style="width: 2.5rem; height: 2.5rem;">
                            <i data-lucide="users" class="icon"></i>
                        </div>
                        <div>
                            <p class="radio-card-text mb-0">Apoderado</p>
                            <p class="small text-slate-500 mb-0">Padre, madre o tutor</p>
                        </div>
                    </div>
                </label>
            </div>

            <!-- Formulario Apoderado (Oculto por defecto) -->
            <div id="form_apoderado" class="d-none card-app p-3 bg-slate-50 mb-4">
                <p class="form-label-app mb-3">Datos del Apoderado</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-app">DNI *</label>
                        <input type="text" name="apoderado_dni" id="apoderado_dni" class="form-control-app">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-app">Parentesco *</label>
                        <select name="apoderado_parentesco" id="apoderado_parentesco" class="form-select-app">
                            <option value="">Seleccione...</option>
                            <option value="Madre">Madre</option>
                            <option value="Padre">Padre</option>
                            <option value="Tutor">Tutor/a</option>
                            <option value="Hermano/a">Hermano/a</option>
                            <option value="Abuelo/a">Abuelo/a</option>
                            <option value="Tio/a">Tío/a</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-app">Nombres *</label>
                        <input type="text" name="apoderado_nombres" id="apoderado_nombres" class="form-control-app">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-app">Apellidos *</label>
                        <input type="text" name="apoderado_apellidos" id="apoderado_apellidos" class="form-control-app">
                    </div>
                    <div class="col-12">
                        <label class="form-label-app">Teléfono</label>
                        <input type="text" name="apoderado_telefono" id="apoderado_telefono" class="form-control-app">
                    </div>
                </div>
            </div>

            <div>
                <label class="form-label-app">Observaciones</label>
                <textarea name="observaciones" rows="2" class="form-control-app" placeholder="Opcional. Ej. Falta firmar un libro."></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
            <a href="{{ route('entregas.index') }}" class="btn-app btn-app-secondary">
                Cancelar
            </a>
            <button type="button" id="btn_guardar" class="btn-app btn-app-primary" disabled>
                <i data-lucide="check-circle" class="icon"></i>
                Registrar Entrega
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputBuscar = document.getElementById('buscar_estudiante');
        const resultados = document.getElementById('resultados_busqueda');
        const estInfo = document.getElementById('estudiante_info');
        const inputIdEst = document.getElementById('id_estudiante');
        const btnCambiar = document.getElementById('btn_cambiar_estudiante');

        const secLibros = document.getElementById('seccion_libros');
        const loadingLibros = document.getElementById('loading_libros');
        const listaLibros = document.getElementById('lista_libros');
        const lblTotalLibros = document.getElementById('lbl_total_libros');

        const secRecepcion = document.getElementById('seccion_recepcion');
        const radiosFirmante = document.querySelectorAll('input[name="tipo_firmante"]');
        const formApoderado = document.getElementById('form_apoderado');
        const btnGuardar = document.getElementById('btn_guardar');
        const form = document.getElementById('form-entrega');

        const alertaEntrega = document.getElementById('alerta_entrega');

        let timeoutBusqueda = null;

        radiosFirmante.forEach(radio => {
            radio.addEventListener('change', (e) => {
                if(e.target.value === 'apoderado') {
                    formApoderado.classList.remove('d-none');
                    document.getElementById('apoderado_dni').required = true;
                    document.getElementById('apoderado_nombres').required = true;
                    document.getElementById('apoderado_apellidos').required = true;
                    document.getElementById('apoderado_parentesco').required = true;
                } else {
                    formApoderado.classList.add('d-none');
                    document.getElementById('apoderado_dni').required = false;
                    document.getElementById('apoderado_nombres').required = false;
                    document.getElementById('apoderado_apellidos').required = false;
                    document.getElementById('apoderado_parentesco').required = false;
                }
            });
        });

        inputBuscar.addEventListener('input', function() {
            const q = this.value;
            clearTimeout(timeoutBusqueda);

            if(q.length < 2) {
                resultados.classList.add('d-none');
                return;
            }

            timeoutBusqueda = setTimeout(() => {
                fetch(`{{ route('entregas.api.buscar-estudiantes') }}?q=${q}`)
                    .then(r => r.json())
                    .then(data => {
                        resultados.innerHTML = '';
                        if(data.length === 0) {
                            resultados.innerHTML = '<div class="result-empty">No se encontraron estudiantes.</div>';
                        } else {
                            data.forEach(est => {
                                const div = document.createElement('div');
                                div.className = 'result-item';
                                div.innerHTML = `
                                    <div class="result-title">${est.apellidos}, ${est.nombres}</div>
                                    <div class="result-meta">DNI: ${est.dni} | ${est.grado}° ${est.nivel}</div>
                                `;
                                div.addEventListener('click', () => seleccionarEstudiante(est));
                                resultados.appendChild(div);
                            });
                        }
                        resultados.classList.remove('d-none');
                    });
            }, 300);
        });

        document.addEventListener('click', (e) => {
            if(!inputBuscar.contains(e.target) && !resultados.contains(e.target)) {
                resultados.classList.add('d-none');
            }
        });

        function seleccionarEstudiante(est) {
            resultados.classList.add('d-none');
            inputBuscar.parentElement.parentElement.classList.add('d-none');

            inputIdEst.value = est.id;
            document.getElementById('lbl_nombre_estudiante').textContent = `${est.apellidos}, ${est.nombres}`;
            document.getElementById('lbl_dni_estudiante').textContent = est.dni;
            document.getElementById('lbl_grado_estudiante').textContent = est.grado + '°';
            document.getElementById('lbl_nivel_estudiante').textContent = est.nivel.charAt(0).toUpperCase() + est.nivel.slice(1);

            if(est.tiene_entrega) {
                alertaEntrega.classList.remove('d-none');
                btnGuardar.disabled = true;
                secLibros.classList.add('d-none');
                secRecepcion.classList.add('d-none');
            } else {
                alertaEntrega.classList.add('d-none');
                btnGuardar.disabled = false;
                cargarLibros(est.grado, est.nivel);
                secRecepcion.classList.remove('d-none');
            }

            estInfo.classList.remove('d-none');
        }

        btnCambiar.addEventListener('click', () => {
            estInfo.classList.add('d-none');
            secLibros.classList.add('d-none');
            secRecepcion.classList.add('d-none');
            inputBuscar.parentElement.parentElement.classList.remove('d-none');
            inputBuscar.value = '';
            inputIdEst.value = '';
            inputBuscar.focus();
            btnGuardar.disabled = true;
        });

        function cargarLibros(grado, nivel) {
            secLibros.classList.remove('d-none');
            listaLibros.innerHTML = '';
            loadingLibros.classList.remove('d-none');

            fetch(`{{ route('entregas.api.libros-por-grado') }}?grado=${grado}&nivel=${nivel}`)
                .then(r => r.json())
                .then(data => {
                    loadingLibros.classList.add('d-none');

                    if(data.length === 0) {
                        listaLibros.innerHTML = '<div class="alert alert-light border small mb-0">No hay libros disponibles en inventario para este grado.</div>';
                        btnGuardar.disabled = true;
                        return;
                    }

                    let html = '';
                    data.forEach(l => {
                        html += `
                        <label class="book-option">
                            <div class="d-flex align-items-center gap-3">
                                <input type="checkbox" name="libros[]" value="${l.id}" checked class="form-check-input cb-libro">
                                <div>
                                    <p class="book-title">${l.nombre}</p>
                                    <p class="book-meta">${l.codigo_libro} | Área: ${l.area}</p>
                                </div>
                            </div>
                            <span class="stock-badge success">Disp: ${l.cantidad_disponible}</span>
                        </label>`;
                    });

                    listaLibros.innerHTML = html;
                    actualizarTotalLibros();

                    document.querySelectorAll('.cb-libro').forEach(cb => {
                        cb.addEventListener('change', actualizarTotalLibros);
                    });
                });
        }

        function actualizarTotalLibros() {
            const count = document.querySelectorAll('.cb-libro:checked').length;
            lblTotalLibros.textContent = `${count} libros seleccionados`;
            if(count === 0) {
                btnGuardar.disabled = true;
            } else {
                btnGuardar.disabled = false;
            }
        }

        btnGuardar.addEventListener('click', () => {
            const tipo = document.querySelector('input[name="tipo_firmante"]:checked').value;
            if(tipo === 'apoderado') {
                const dni = document.getElementById('apoderado_dni').value;
                const nom = document.getElementById('apoderado_nombres').value;
                const ape = document.getElementById('apoderado_apellidos').value;
                if(!dni || !nom || !ape) {
                    window.showToast && window.showToast('Por favor, completa los datos requeridos del apoderado (DNI, Nombres, Apellidos).', 'warning');
                    return;
                }
            }

            if(document.querySelectorAll('.cb-libro:checked').length === 0) {
                window.showToast && window.showToast('Debe seleccionar al menos un libro para entregar.', 'warning');
                return;
            }

            btnGuardar.innerHTML = '<i data-lucide="loader-2" class="icon spin"></i> Registrando...';
            btnGuardar.disabled = true;
            form.submit();
        });
    });
</script>
@endpush
