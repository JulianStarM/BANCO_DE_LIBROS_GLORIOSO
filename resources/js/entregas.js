// resources/js/entregas.js

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formNuevaEntrega');
    if (!form) return;

    // ============ ENTREGA MODAL LOGIC ============
    const mBuscar = document.getElementById('modal_buscar_est');
    const mResultados = document.getElementById('modal_resultados_est');
    const mIdEst = document.getElementById('modal_id_estudiante');
    const mEstInfo = document.getElementById('modal_est_info');
    const mBuscarWrapper = document.getElementById('entrega-buscar-wrapper');
    const mSecBuscarEst = document.getElementById('modal_sec_buscar_est');
    const mSecLibros = document.getElementById('modal_sec_libros');
    const mSecRecepcion = document.getElementById('modal_sec_recepcion');
    const mListaLibros = document.getElementById('modal_lista_libros');
    const mLoadingLibros = document.getElementById('modal_loading_libros');
    const mLblTotal = document.getElementById('modal_lbl_total_libros');
    const mBtnGuardar = document.getElementById('modal_btn_guardar_entrega');
    const mAlerta = document.getElementById('modal_alerta_entrega');
    let timeoutBusq = null;

    const actionUrl = form.getAttribute('data-action');
    const buscarUrl = form.getAttribute('data-buscar-url');
    const librosUrl = form.getAttribute('data-libros-url');

    function renderEstudiantes(data) {
        mResultados.innerHTML = '';
        if (data.length === 0) {
            mResultados.innerHTML = '<div class="seleccion-empty">No se encontraron estudiantes.</div>';
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'seleccion-tabla-wrapper';
        wrapper.style.maxHeight = '16rem';
        wrapper.style.overflowY = 'auto';

        const table = document.createElement('table');
        table.className = 'table table-app table-sm mb-0 seleccion-tabla';
        table.innerHTML = `
            <thead>
                <tr>
                    <th class="text-center" style="width: 2.5rem;">#</th>
                    <th>Apellidos y nombres</th>
                    <th style="width: 7rem;">DNI</th>
                    <th class="text-center" style="width: 4rem;">Grado</th>
                    <th class="text-center" style="width: 4.5rem;">Sección</th>
                    <th class="text-center" style="width: 5.5rem;">Nivel</th>
                    <th class="text-center" style="width: 3.5rem;"></th>
                </tr>
            </thead>
            <tbody></tbody>
        `;

        const tbody = table.querySelector('tbody');
        data.forEach((est, i) => {
            const tr = document.createElement('tr');
            const entregado = est.tiene_entrega;
            tr.className = 'seleccion-fila' + (mIdEst.value == est.id ? ' active' : '') + (entregado ? ' seleccion-fila-entregado' : '');
            tr.setAttribute('data-id', est.id);

            const accion = entregado
                ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Entregado</span>'
                : `<button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 seleccionar-btn" title="Seleccionar">
                        <i data-lucide="check" class="icon" style="width: 0.875rem; height: 0.875rem;"></i>
                   </button>`;

            tr.innerHTML = `
                <td class="text-center">${i + 1}</td>
                <td class="fw-medium ${entregado ? 'text-slate-500' : 'text-slate-800'}">${est.apellidos}, ${est.nombres}</td>
                <td class="${entregado ? 'text-slate-500' : ''}">${est.dni}</td>
                <td class="text-center ${entregado ? 'text-slate-500' : ''}">${est.grado}°</td>
                <td class="text-center ${entregado ? 'text-slate-500' : ''}">${est.seccion || '-'}</td>
                <td class="text-center text-capitalize ${entregado ? 'text-slate-500' : ''}">${est.nivel || '-'}</td>
                <td class="text-center">${accion}</td>
            `;

            if (! entregado) {
                tr.addEventListener('click', (e) => {
                    if (! e.target.closest('.seleccionar-btn')) {
                        seleccionarEstModal(est, tr);
                    }
                });
                tr.querySelector('.seleccionar-btn').addEventListener('click', (e) => {
                    e.stopPropagation();
                    seleccionarEstModal(est, tr);
                });
            }

            tbody.appendChild(tr);
        });

        wrapper.appendChild(table);
        mResultados.appendChild(wrapper);
        window.initLucide?.();
    }

    function buscarEstudiantes(q = '') {
        fetch(`${buscarUrl}?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => renderEstudiantes(data))
            .catch(() => {
                mResultados.innerHTML = '<div class="seleccion-empty text-danger">Error al buscar estudiantes.</div>';
            });
    }

    function cargarListaInicial() {
        mResultados.innerHTML = '<div class="seleccion-empty"><i data-lucide="loader-2" class="icon spin"></i> Cargando estudiantes...</div>';
        window.initLucide?.();
        buscarEstudiantes('');
    }

    if (mBuscar) {
        mBuscar.addEventListener('input', function() {
            const q = this.value.trim();
            clearTimeout(timeoutBusq);
            timeoutBusq = setTimeout(() => buscarEstudiantes(q), 300);
        });

        cargarListaInicial();
    }



    function seleccionarEstModal(est, itemElement = null) {
        mIdEst.value = est.id;
        document.getElementById('modal_lbl_nombre').textContent = `${est.apellidos}, ${est.nombres}`;
        document.getElementById('modal_lbl_dni').textContent = est.dni;
        document.getElementById('modal_lbl_grado').textContent = est.grado + '°';
        document.getElementById('modal_lbl_nivel').textContent = (est.nivel || '').charAt(0).toUpperCase() + (est.nivel || '').slice(1);

        if (mSecBuscarEst) mSecBuscarEst.classList.add('d-none');

        mResultados.querySelectorAll('.seleccion-fila').forEach(el => el.classList.remove('active'));
        if (itemElement) {
            itemElement.classList.add('active');
        } else {
            const active = mResultados.querySelector(`.seleccion-fila[data-id="${est.id}"]`);
            if (active) active.classList.add('active');
        }

        if (est.tiene_entrega) {
            mAlerta.classList.remove('d-none');
            mBtnGuardar.disabled = true;
            mSecLibros.classList.add('d-none');
            mSecRecepcion.classList.add('d-none');
        } else {
            mAlerta.classList.add('d-none');
            cargarLibrosModal(est.grado, est.nivel);
            mSecRecepcion.classList.remove('d-none');
        }
        mEstInfo.classList.remove('d-none');
        window.initLucide?.();
    }

    document.getElementById('modal_btn_cambiar_est')?.addEventListener('click', () => {
        mEstInfo.classList.add('d-none');
        mSecLibros.classList.add('d-none');
        mSecRecepcion.classList.add('d-none');
        if (mSecBuscarEst) mSecBuscarEst.classList.remove('d-none');
        mBuscar.value = '';
        mIdEst.value = '';
        mBtnGuardar.disabled = true;
        mResultados.querySelectorAll('.seleccion-fila').forEach(el => el.classList.remove('active'));
        cargarListaInicial();
        mBuscar.focus();
    });

    function cargarLibrosModal(grado, nivel) {
        mSecLibros.classList.remove('d-none');
        mListaLibros.innerHTML = '';
        mLoadingLibros.classList.remove('d-none');

        fetch(`${librosUrl}?grado=${grado}&nivel=${nivel}`)
            .then(r => r.json())
            .then(data => {
                mLoadingLibros.classList.add('d-none');
                if (data.length === 0) {
                    mListaLibros.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">No hay libros disponibles para este grado.</td></tr>';
                    mBtnGuardar.disabled = true;
                    return;
                }
                let html = '';
                data.forEach((l, i) => {
                    html += `<tr>
                        <td class="text-center">${i + 1}</td>
                        <td class="fw-medium text-slate-800">${l.nombre}</td>
                        <td class="text-slate-600">${l.area}</td>
                        <td class="text-center"><span class="stock-badge success">${l.cantidad_disponible}</span></td>
                        <td class="text-center">
                            <input type="checkbox" name="libros[]" value="${l.id}" checked class="form-check-input modal-cb-libro">
                        </td>
                    </tr>`;
                });
                mListaLibros.innerHTML = html;
                actualizarTotalModal();
                document.querySelectorAll('.modal-cb-libro').forEach(cb => cb.addEventListener('change', actualizarTotalModal));
                window.initLucide?.();
            });
    }

    function actualizarTotalModal() {
        const count = document.querySelectorAll('.modal-cb-libro:checked').length;
        mLblTotal.textContent = `${count} libros seleccionados`;
        if (count === 0) {
            mBtnGuardar.disabled = true;
        } else {
            mBtnGuardar.disabled = false;
        }
    }

    // Toggle apoderado form
    document.querySelectorAll('#formNuevaEntrega input[name="tipo_firmante"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const apoderadoForm = document.getElementById('modal_form_apoderado');
            if (apoderadoForm) {
                apoderadoForm.classList.toggle('d-none', e.target.value !== 'apoderado');
            }
        });
    });

    // Submit entrega
    mBtnGuardar?.addEventListener('click', () => {
        if (window.submitModalForm) {
            window.submitModalForm('formNuevaEntrega', actionUrl, 'modalNuevaEntrega', () => {
                location.reload();
            });
        }
    });
});
