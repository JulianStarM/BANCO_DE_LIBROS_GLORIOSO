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
            if (q.length < 2) { mResultados.classList.add('hidden'); return; }
            timeoutBusq = setTimeout(() => {
                fetch(`${buscarUrl}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => {
                        mResultados.innerHTML = '';
                        if (data.length === 0) {
                            mResultados.innerHTML = '<div class="p-3 text-sm text-slate-500">No se encontraron estudiantes.</div>';
                        } else {
                            data.forEach(est => {
                                const div = document.createElement('div');
                                div.className = 'p-3 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0 text-sm';
                                div.innerHTML = `<div class="font-medium text-slate-800">${est.apellidos}, ${est.nombres}</div><div class="text-xs text-slate-500">DNI: ${est.dni} | ${est.grado}° ${est.nivel || ''}</div>`;
                                div.addEventListener('click', () => seleccionarEstModal(est));
                                mResultados.appendChild(div);
                            });
                        }
                        mResultados.classList.remove('hidden');
                    }).catch(() => { 
                        mResultados.innerHTML = '<div class="p-3 text-sm text-red-500">Error al buscar.</div>'; 
                        mResultados.classList.remove('hidden'); 
                    });
            }, 300);
        });
    }

    document.addEventListener('click', (e) => {
        if (mBuscar && !mBuscar.contains(e.target) && mResultados && !mResultados.contains(e.target)) {
            mResultados.classList.add('hidden');
        }
    });

    function seleccionarEstModal(est) {
        mResultados.classList.add('hidden');
        mBuscarWrapper.classList.add('hidden');
        mIdEst.value = est.id;
        document.getElementById('modal_lbl_nombre').textContent = `${est.apellidos}, ${est.nombres}`;
        document.getElementById('modal_lbl_dni').textContent = est.dni;
        document.getElementById('modal_lbl_grado').textContent = est.grado + '°';
        document.getElementById('modal_lbl_nivel').textContent = (est.nivel || '').charAt(0).toUpperCase() + (est.nivel || '').slice(1);
        
        if (est.tiene_entrega) {
            mAlerta.classList.remove('hidden');
            mBtnGuardar.disabled = true; 
            mBtnGuardar.classList.add('opacity-50','cursor-not-allowed');
            mSecLibros.classList.add('hidden'); 
            mSecRecepcion.classList.add('hidden');
        } else {
            mAlerta.classList.add('hidden');
            cargarLibrosModal(est.grado, est.nivel);
            mSecRecepcion.classList.remove('hidden');
        }
        mEstInfo.classList.remove('hidden');
        window.initLucide();
    }

    document.getElementById('modal_btn_cambiar_est')?.addEventListener('click', () => {
        mEstInfo.classList.add('hidden'); 
        mSecLibros.classList.add('hidden'); 
        mSecRecepcion.classList.add('hidden');
        mBuscarWrapper.classList.remove('hidden'); 
        mBuscar.value = ''; 
        mIdEst.value = '';
        mBtnGuardar.disabled = true; 
        mBtnGuardar.classList.add('opacity-50','cursor-not-allowed');
        mBuscar.focus();
    });

    function cargarLibrosModal(grado, nivel) {
        mSecLibros.classList.remove('hidden'); 
        mListaLibros.innerHTML = ''; 
        mLoadingLibros.classList.remove('hidden');
        
        fetch(`${librosUrl}?grado=${grado}&nivel=${nivel}`)
            .then(r => r.json())
            .then(data => {
                mLoadingLibros.classList.add('hidden');
                if (data.length === 0) {
                    mListaLibros.innerHTML = '<div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-600">No hay libros disponibles para este grado.</div>';
                    mBtnGuardar.disabled = true; 
                    mBtnGuardar.classList.add('opacity-50','cursor-not-allowed');
                    return;
                }
                let html = '';
                data.forEach(l => {
                    html += `<label class="flex items-center justify-between p-2.5 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer has-[:checked]:bg-blue-50 has-[:checked]:border-blue-200 text-sm">
                        <div class="flex items-center gap-2"><input type="checkbox" name="libros[]" value="${l.id}" checked class="h-4 w-4 rounded border-slate-300 text-blue-600 modal-cb-libro"><div><p class="font-semibold text-slate-800">${l.nombre}</p><p class="text-xs text-slate-500">${l.area}</p></div></div>
                        <span class="bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded-md text-xs">Disp: ${l.cantidad_disponible}</span></label>`;
                });
                mListaLibros.innerHTML = html;
                actualizarTotalModal();
                document.querySelectorAll('.modal-cb-libro').forEach(cb => cb.addEventListener('change', actualizarTotalModal));
            });
    }

    function actualizarTotalModal() {
        const count = document.querySelectorAll('.modal-cb-libro:checked').length;
        mLblTotal.textContent = `${count} libros seleccionados`;
        if (count === 0) { 
            mBtnGuardar.disabled = true; 
            mBtnGuardar.classList.add('opacity-50','cursor-not-allowed'); 
        } else { 
            mBtnGuardar.disabled = false; 
            mBtnGuardar.classList.remove('opacity-50','cursor-not-allowed'); 
        }
    }

    // Toggle apoderado form
    document.querySelectorAll('#formNuevaEntrega input[name="tipo_firmante"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            const apoderadoForm = document.getElementById('modal_form_apoderado');
            if (apoderadoForm) {
                apoderadoForm.classList.toggle('hidden', e.target.value !== 'apoderado');
            }
        });
    });

    // Submit entrega
    mBtnGuardar?.addEventListener('click', () => {
        if (window.submitModalForm) {
            window.submitModalForm('formNuevaEntrega', actionUrl, 'modalNuevaEntrega', () => {
                setTimeout(() => location.reload(), 800);
            });
        }
    });
});
