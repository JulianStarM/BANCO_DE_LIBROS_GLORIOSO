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

    if (mBuscar) {
        mBuscar.addEventListener('input', function() {
            const q = this.value.trim();
            clearTimeout(timeoutBusq);
            if (q.length < 2) { mResultados.classList.add('d-none'); return; }
            timeoutBusq = setTimeout(() => {
                fetch(`${buscarUrl}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => {
                        mResultados.innerHTML = '';
                        if (data.length === 0) {
                            mResultados.innerHTML = '<div class="result-empty">No se encontraron estudiantes.</div>';
                        } else {
                            data.forEach(est => {
                                const div = document.createElement('div');
                                div.className = 'result-item';
                                div.innerHTML = `<div class="result-title">${est.apellidos}, ${est.nombres}</div><div class="result-meta">DNI: ${est.dni} | ${est.grado}° ${est.nivel || ''}</div>`;
                                div.addEventListener('click', () => seleccionarEstModal(est));
                                mResultados.appendChild(div);
                            });
                        }
                        mResultados.classList.remove('d-none');
                    }).catch(() => {
                        mResultados.innerHTML = '<div class="result-empty text-danger">Error al buscar.</div>';
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

    function seleccionarEstModal(est) {
        mResultados.classList.add('d-none');
        mBuscarWrapper.classList.add('d-none');
        mIdEst.value = est.id;
        document.getElementById('modal_lbl_nombre').textContent = `${est.apellidos}, ${est.nombres}`;
        document.getElementById('modal_lbl_dni').textContent = est.dni;
        document.getElementById('modal_lbl_grado').textContent = est.grado + '°';
        document.getElementById('modal_lbl_nivel').textContent = (est.nivel || '').charAt(0).toUpperCase() + (est.nivel || '').slice(1);

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
        mBuscarWrapper.classList.remove('d-none');
        mBuscar.value = '';
        mIdEst.value = '';
        mBtnGuardar.disabled = true;
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
                    mListaLibros.innerHTML = '<div class="alert alert-light border small mb-0">No hay libros disponibles para este grado.</div>';
                    mBtnGuardar.disabled = true;
                    return;
                }
                let html = '';
                data.forEach(l => {
                    html += `<label class="book-option">
                        <div class="d-flex align-items-center gap-3">
                            <input type="checkbox" name="libros[]" value="${l.id}" checked class="form-check-input modal-cb-libro">
                            <div>
                                <p class="book-title mb-0">${l.nombre}</p>
                                <p class="book-meta mb-0">${l.area}</p>
                            </div>
                        </div>
                        <span class="stock-badge success">Disp: ${l.cantidad_disponible}</span>
                    </label>`;
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
            window.submitModalForm('formNuevaEntrega', actionUrl, 'modalNuevaEntrega', (data) => {
                localStorage.setItem('app_toast', JSON.stringify({message: data.message, type: 'success'}));
                setTimeout(() => location.reload(), 800);
            });
        }
    });
});
