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
    const mSecLibros = document.getElementById('modal_dev_sec_libros');
    const mSecFirmante = document.getElementById('modal_dev_sec_firmante');
    const mListaLibros = document.getElementById('modal_dev_lista_libros');
    const mBtnGuardar = document.getElementById('modal_btn_guardar_dev');
    let timeoutBusq = null;

    if (mBuscar) {
        mBuscar.addEventListener('input', function() {
            const q = this.value.trim();
            clearTimeout(timeoutBusq);
            if (q.length < 2) {
                if(mResultados) mResultados.classList.add('d-none');
                return;
            }
            timeoutBusq = setTimeout(() => {
                fetch(`${pendientesUrl}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => {
                        if (!mResultados) return;
                        mResultados.innerHTML = '';
                        if (data.length === 0) {
                            mResultados.innerHTML = '<div class="result-empty">No se encontraron entregas pendientes.</div>';
                        } else {
                            data.forEach(ent => {
                                const div = document.createElement('div');
                                div.className = 'result-item';
                                div.innerHTML = `<div class="result-title">${ent.codigo_general}</div><div class="result-meta">${ent.estudiante.apellidos}, ${ent.estudiante.nombres} | DNI: ${ent.estudiante.dni}</div>`;
                                div.addEventListener('click', () => seleccionarEntregaModal(ent));
                                mResultados.appendChild(div);
                            });
                        }
                        mResultados.classList.remove('d-none');
                    });
            }, 300);
        });
    }

    document.addEventListener('click', (e) => {
        if (mBuscar && !mBuscar.contains(e.target) && mResultados && !mResultados.contains(e.target)) {
            mResultados.classList.add('d-none');
        }
    });

    function seleccionarEntregaModal(ent) {
        if(mResultados) mResultados.classList.add('d-none');
        if(mBuscarWrapper) mBuscarWrapper.classList.add('d-none');

        if(mIdEntrega) mIdEntrega.value = ent.id;

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
            if(mBuscarWrapper) mBuscarWrapper.classList.remove('d-none');

            if(mBuscar) {
                mBuscar.value = '';
                mBuscar.focus();
            }
            if(mIdEntrega) mIdEntrega.value = '';

            if(mBtnGuardar) {
                mBtnGuardar.disabled = true;
            }
        });
    }

    if (mBtnGuardar) {
        mBtnGuardar.addEventListener('click', () => {
            if (window.submitModalForm) {
                window.submitModalForm('formNuevaDevolucion', actionUrl, 'modalNuevaDevolucion', () => {
                    setTimeout(() => location.reload(), 800);
                });
            }
        });
    }
});
