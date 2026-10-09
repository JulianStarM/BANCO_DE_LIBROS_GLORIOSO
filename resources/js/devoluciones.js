// resources/js/devoluciones.js

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formNuevaDevolucion');
    if (!form) return;

    const actionUrl = form.getAttribute('data-action');
    const pendientesUrl = form.getAttribute('data-pendientes-url');

    const mBuscar = document.getElementById('modal_dev_buscar');
    const mResultados = document.getElementById('modal_dev_resultados');
    const mIdEntrega = document.getElementById('modal_id_entrega');
    const mInfo = document.getElementById('modal_dev_info');
    const mBuscarWrapper = document.getElementById('dev-buscar-wrapper');
    const mSecBuscarEnt = document.getElementById('modal_sec_buscar_ent');
    const mSecLibros = document.getElementById('modal_dev_sec_libros');
    const mSecFirmante = document.getElementById('modal_dev_sec_firmante');
    const mListaLibros = document.getElementById('modal_dev_lista_libros');
    const mBtnGuardar = document.getElementById('modal_btn_guardar_dev');
    let timeoutBusq = null;

    function renderEntregas(data) {
        if (!mResultados) return;
        mResultados.innerHTML = '';
        if (data.length === 0) {
            mResultados.innerHTML = '<div class="seleccion-empty">No se encontraron entregas pendientes.</div>';
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
                    <th style="width: 9rem;">Código entrega</th>
                    <th>Estudiante</th>
                    <th style="width: 7rem;">DNI</th>
                    <th class="text-center" style="width: 3.5rem;"></th>
                </tr>
            </thead>
            <tbody></tbody>
        `;

        const tbody = table.querySelector('tbody');
        data.forEach((ent, i) => {
            const tr = document.createElement('tr');
            tr.className = 'seleccion-fila' + (mIdEntrega.value == ent.id ? ' active' : '');
            tr.setAttribute('data-id', ent.id);
            tr.innerHTML = `
                <td class="text-center">${i + 1}</td>
                <td class="fw-medium text-slate-800 font-mono">${ent.codigo_general}</td>
                <td class="fw-medium text-slate-800">${ent.estudiante.apellidos}, ${ent.estudiante.nombres}</td>
                <td>${ent.estudiante.dni}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2 seleccionar-btn" title="Seleccionar">
                        <i data-lucide="check" class="icon" style="width: 0.875rem; height: 0.875rem;"></i>
                    </button>
                </td>
            `;
            tr.addEventListener('click', (e) => {
                if (! e.target.closest('.seleccionar-btn')) {
                    seleccionarEntregaModal(ent, tr);
                }
            });
            tr.querySelector('.seleccionar-btn').addEventListener('click', (e) => {
                e.stopPropagation();
                seleccionarEntregaModal(ent, tr);
            });
            tbody.appendChild(tr);
        });

        wrapper.appendChild(table);
        mResultados.appendChild(wrapper);
        window.initLucide?.();
    }

    function buscarEntregas(q = '') {
        fetch(`${pendientesUrl}?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(data => renderEntregas(data))
            .catch(() => {
                if (!mResultados) return;
                mResultados.innerHTML = '<div class="seleccion-empty text-danger">Error al cargar entregas.</div>';
            });
    }

    function cargarListaInicial() {
        if (!mResultados) return;
        mResultados.innerHTML = '<div class="seleccion-empty"><i data-lucide="loader-2" class="icon spin"></i> Cargando entregas pendientes...</div>';
        window.initLucide?.();
        buscarEntregas('');
    }

    if (mBuscar) {
        mBuscar.addEventListener('input', function() {
            const q = this.value.trim();
            clearTimeout(timeoutBusq);
            timeoutBusq = setTimeout(() => buscarEntregas(q), 300);
        });

        cargarListaInicial();
    }



    function seleccionarEntregaModal(ent, itemElement = null) {
        if(mIdEntrega) mIdEntrega.value = ent.id;
        if(mSecBuscarEnt) mSecBuscarEnt.classList.add('d-none');

        if(mResultados) {
            mResultados.querySelectorAll('.seleccion-fila').forEach(el => el.classList.remove('active'));
            if (itemElement) {
                itemElement.classList.add('active');
            } else {
                const active = mResultados.querySelector(`.seleccion-fila[data-id="${ent.id}"]`);
                if (active) active.classList.add('active');
            }
        }

        const lblCod = document.getElementById('modal_dev_lbl_cod');
        const lblEst = document.getElementById('modal_dev_lbl_est');
        const lblFecha = document.getElementById('modal_dev_lbl_fecha');

        if(lblCod) lblCod.textContent = ent.codigo_general;
        if(lblEst) lblEst.textContent = `${ent.estudiante.apellidos}, ${ent.estudiante.nombres}`;
        if(lblFecha) lblFecha.textContent = ent.fecha_entrega;

        if(mListaLibros) {
            mListaLibros.innerHTML = '';
            ent.detalles.forEach((d, i) => {
                mListaLibros.innerHTML += `
                    <tr>
                        <td>
                            <input type="hidden" name="detalles[${i}][id_detalle_entrega]" value="${d.id}">
                            <input type="hidden" name="detalles[${i}][id_libro]" value="${d.id_libro}">
                            ${i+1}
                        </td>
                        <td class="fw-medium text-slate-800">${d.libro.nombre}</td>
                        <td class="text-center">
                            <select name="detalles[${i}][estado_libro]" class="form-select-app mx-auto" style="max-width: 160px;">
                                <option value="bueno" selected>Bueno</option>
                                <option value="deteriorado">Deteriorado</option>
                                <option value="deficiente">Deficiente</option>
                                <option value="no_devuelto">No devuelto</option>
                                <option value="perdido">Perdido</option>
                            </select>
                        </td>
                    </tr>
                `;
            });
        }

        if(mInfo) mInfo.classList.remove('d-none');
        if(mSecLibros) mSecLibros.classList.remove('d-none');
        if(mSecFirmante) mSecFirmante.classList.remove('d-none');

        if(mBtnGuardar) {
            mBtnGuardar.disabled = false;
        }
    }

    const btnCambiar = document.getElementById('modal_dev_btn_cambiar');
    if (btnCambiar) {
        btnCambiar.addEventListener('click', () => {
            if(mInfo) mInfo.classList.add('d-none');
            if(mSecLibros) mSecLibros.classList.add('d-none');
            if(mSecFirmante) mSecFirmante.classList.add('d-none');
            if(mSecBuscarEnt) mSecBuscarEnt.classList.remove('d-none');

            if(mBuscar) {
                mBuscar.value = '';
                mBuscar.focus();
            }
            if(mIdEntrega) mIdEntrega.value = '';
            if(mResultados) mResultados.querySelectorAll('.seleccion-fila').forEach(el => el.classList.remove('active'));

            if(mBtnGuardar) {
                mBtnGuardar.disabled = true;
            }

            cargarListaInicial();
        });
    }

    if (mBtnGuardar) {
        mBtnGuardar.addEventListener('click', () => {
            if (window.submitModalForm) {
                window.submitModalForm('formNuevaDevolucion', actionUrl, 'modalNuevaDevolucion', () => {
                    location.reload();
                });
            }
        });
    }
});
