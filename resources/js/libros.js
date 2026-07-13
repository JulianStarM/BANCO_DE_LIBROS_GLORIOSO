// resources/js/libros.js

document.addEventListener('DOMContentLoaded', () => {
    const btnGuardarLibro = document.getElementById('btnGuardarLibro');
    const formNuevoLibro = document.getElementById('formNuevoLibro');

    if (btnGuardarLibro && formNuevoLibro) {
        btnGuardarLibro.addEventListener('click', () => {
            const actionUrl = formNuevoLibro.getAttribute('data-action');
            if (window.submitModalForm) {
                window.submitModalForm('formNuevoLibro', actionUrl, 'modalNuevoLibro', (data) => {
                    localStorage.setItem('app_toast', JSON.stringify({message: data.message, type: 'success'}));
                    setTimeout(() => location.reload(), 800);
                });
            }
        });
    }
});
