// resources/js/feedback-modal.js

(function () {
    const modal = document.getElementById('feedbackModal');
    if (!modal) return;

    const iconEl = document.getElementById('feedbackModalIcon');
    const titleEl = document.getElementById('feedbackModalTitle');
    const messageEl = document.getElementById('feedbackModalMessage');
    const actionsEl = document.getElementById('feedbackModalActions');
    const overlay = modal.querySelector('.feedback-modal-overlay');
    const closeButtons = modal.querySelectorAll('[data-feedback-close]');

    const icons = {
        success: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>',
        error: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
        warning: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
        confirmation: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
        info: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
    };

    const titles = {
        success: '¡Operación exitosa!',
        error: 'Ocurrió un error',
        warning: 'Advertencia',
        confirmation: '¿Confirmar acción?',
        info: 'Información',
    };

    let onCloseCallback = null;
    let currentOnConfirm = null;
    let autoCloseTimer = null;
    let previouslyFocused = null;

    function openModal() {
        previouslyFocused = document.activeElement;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(() => {
            modal.classList.add('feedback-modal-visible');
        });

        const firstButton = actionsEl.querySelector('button');
        if (firstButton) {
            setTimeout(() => firstButton.focus(), 50);
        }

        modal.addEventListener('keydown', trapFocus);
    }

    function closeModal() {
        if (autoCloseTimer) {
            clearTimeout(autoCloseTimer);
            autoCloseTimer = null;
        }

        modal.classList.remove('feedback-modal-visible');
        modal.removeEventListener('keydown', trapFocus);

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';

            if (onCloseCallback) {
                onCloseCallback();
                onCloseCallback = null;
            }

            currentOnConfirm = null;

            if (previouslyFocused && previouslyFocused.focus) {
                previouslyFocused.focus();
            }
        }, 200);
    }

    function confirmAndClose() {
        const onConfirm = currentOnConfirm;
        closeModal();
        if (typeof onConfirm === 'function') {
            setTimeout(() => onConfirm(), 200);
        }
    }

    function trapFocus(e) {
        if (e.key !== 'Tab') return;

        const focusable = modal.querySelectorAll('button');
        if (focusable.length === 0) return;

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (e.shiftKey && document.activeElement === first) {
            e.preventDefault();
            last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
            e.preventDefault();
            first.focus();
        }
    }

    closeButtons.forEach(btn => btn.addEventListener('click', closeModal));

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    window.showFeedbackModal = function (config) {
        const type = config.type || 'info';
        const title = config.title || titles[type] || titles.info;
        const message = config.message || '';
        const confirmText = config.confirmText || 'Aceptar';
        const cancelText = config.cancelText || 'Cancelar';

        iconEl.className = 'feedback-modal-icon ' + type;
        iconEl.innerHTML = icons[type] || icons.info;
        titleEl.textContent = title;
        messageEl.innerHTML = message;
        actionsEl.innerHTML = '';

        onCloseCallback = config.onClose || null;
        currentOnConfirm = config.onConfirm || null;

        const autoClose = parseInt(config.autoClose, 10) || 0;

        if (autoClose > 0) {
            autoCloseTimer = setTimeout(() => confirmAndClose(), autoClose);
        } else {
            const acceptButton = document.createElement('button');
            acceptButton.type = 'button';
            acceptButton.className = type === 'error'
                ? 'btn-app btn-app-danger'
                : (type === 'confirmation' ? 'btn-app btn-app-danger' : 'btn-app btn-app-primary');
            acceptButton.textContent = confirmText;
            acceptButton.addEventListener('click', confirmAndClose);

            actionsEl.appendChild(acceptButton);

            if (type === 'confirmation') {
                const cancelButton = document.createElement('button');
                cancelButton.type = 'button';
                cancelButton.className = 'btn-app btn-app-secondary';
                cancelButton.textContent = cancelText;
                cancelButton.addEventListener('click', closeModal);
                actionsEl.insertBefore(cancelButton, acceptButton);
            }
        }

        openModal();
    };

    window.showConfirmModal = function (config) {
        window.showFeedbackModal({
            type: 'confirmation',
            title: config.title || '¿Confirmar acción?',
            message: config.message || '',
            confirmText: config.confirmText || 'Eliminar',
            cancelText: config.cancelText || 'Cancelar',
            onConfirm: config.onConfirm,
            onClose: config.onClose,
        });
    };

    // Confirmación genérica para formularios con data-confirm
    document.addEventListener('submit', (e) => {
        const form = e.target.closest('form[data-confirm]');
        if (!form || form.dataset.confirmSubmitted) return;

        e.preventDefault();

        window.showConfirmModal({
            title: form.dataset.confirmTitle || '¿Confirmar acción?',
            message: form.dataset.confirmMessage || 'Esta acción no se puede deshacer.',
            confirmText: form.dataset.confirmText || 'Eliminar',
            cancelText: form.dataset.cancelText || 'Cancelar',
            onConfirm: () => {
                form.dataset.confirmSubmitted = 'true';
                form.submit();
            },
        });
    });
})();
