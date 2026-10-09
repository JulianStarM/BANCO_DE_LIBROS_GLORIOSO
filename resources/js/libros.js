// resources/js/libros.js

window.abrirModalNuevoLibro = function () {
    const form = document.getElementById('formNuevoLibro');
    if (form) {
        form.reset();
        form.querySelectorAll('.field-error').forEach((el) => el.remove());
        form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
    }

    const codigoInput = document.getElementById('vistaCodigoLibro');
    if (codigoInput) {
        codigoInput.value = '';
    }

    openModal('modalNuevoLibro');
};

document.addEventListener('DOMContentLoaded', () => {
    const btnGuardarLibro = document.getElementById('btnGuardarLibro');
    const formNuevoLibro = document.getElementById('formNuevoLibro');

    if (btnGuardarLibro && formNuevoLibro) {
        btnGuardarLibro.addEventListener('click', () => {
            const actionUrl = formNuevoLibro.getAttribute('data-action');
            if (window.submitModalForm) {
                window.submitModalForm('formNuevoLibro', actionUrl, 'modalNuevoLibro', () => {
                    location.reload();
                });
            }
        });
    }

    // Previsualización automática del código según el nombre
    document.querySelectorAll('.js-form-crear-libro').forEach((form) => {
        const nombreInput = form.querySelector('input[name="nombre"]');
        const codigoInput = form.querySelector('#vistaCodigoLibro');

        if (! nombreInput || ! codigoInput) {
            return;
        }

        let timeoutId = null;

        const actualizarCodigo = async () => {
            const nombre = nombreInput.value.trim();
            if (nombre.length < 2) {
                codigoInput.value = '';

                return;
            }

            try {
                const response = await fetch(`/libros/api/siguiente-codigo?nombre=${encodeURIComponent(nombre)}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    },
                });

                if (! response.ok) {
                    return;
                }

                const data = await response.json();
                codigoInput.value = data.codigo || '';
            } catch (err) {
                // Silencioso: la previsualización no es crítica
            }
        };

        nombreInput.addEventListener('input', () => {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(actualizarCodigo, 300);
        });

        nombreInput.addEventListener('blur', actualizarCodigo);
    });
});
