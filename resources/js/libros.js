// resources/js/libros.js

document.addEventListener('DOMContentLoaded', () => {
    const btnGuardarLibro = document.getElementById('btnGuardarLibro');
    const formNuevoLibro = document.getElementById('formNuevoLibro');

    if (btnGuardarLibro && formNuevoLibro) {
        btnGuardarLibro.addEventListener('click', () => {
            const actionUrl = formNuevoLibro.getAttribute('data-action');
            if (window.submitModalForm) {
                window.submitModalForm('formNuevoLibro', actionUrl, 'modalNuevoLibro', () => {
                    setTimeout(() => location.reload(), 800);
                });
            }
        });
    }
});
