// Biblioteca General - scripts específicos del módulo
// Reutiliza el sistema de modales y notificaciones globales de ui.js

document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.initLucide === 'function') {
        window.initLucide();
    }
});
