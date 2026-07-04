// resources/js/estudiantes.js

document.addEventListener('DOMContentLoaded', () => {
    const btnGuardarEstudiante = document.getElementById('btnGuardarEstudiante');
    const formNuevoEstudiante = document.getElementById('formNuevoEstudiante');

    if (btnGuardarEstudiante && formNuevoEstudiante) {
        btnGuardarEstudiante.addEventListener('click', () => {
            const actionUrl = formNuevoEstudiante.getAttribute('data-action');
            if (window.submitModalForm) {
                window.submitModalForm('formNuevoEstudiante', actionUrl, 'modalNuevoEstudiante', () => {
                    setTimeout(() => location.reload(), 800);
                });
            }
        });
    }
});
