// resources/js/estudiantes.js

document.addEventListener('DOMContentLoaded', () => {
    const btnGuardarEstudiante = document.getElementById('btnGuardarEstudiante');
    const formNuevoEstudiante = document.getElementById('formNuevoEstudiante');

    // Solo agregar listener si el botón NO tiene onclick inline (evita duplicados)
    if (btnGuardarEstudiante && formNuevoEstudiante && !btnGuardarEstudiante.getAttribute('onclick')) {
        btnGuardarEstudiante.addEventListener('click', () => {
            const actionUrl = formNuevoEstudiante.getAttribute('data-action');
            if (window.submitModalForm) {
                window.submitModalForm('formNuevoEstudiante', actionUrl, 'modalNuevoEstudiante', (data) => {
                    localStorage.setItem('app_toast', JSON.stringify({message: data.message, type: 'success'}));
                    setTimeout(() => location.reload(), 800);
                });
            }
        });
    }
});
