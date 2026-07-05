// resources/js/ui.js

window.initLucide = function () {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    window.initLucide();
});

// ===================== MODAL SYSTEM =====================
window.openModal = function (id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    requestAnimationFrame(() => {
        modal.classList.add('modal-visible');
    });

    setTimeout(() => window.initLucide(), 50);
};

window.closeModal = function (id) {
    const modal = document.getElementById(id);
    if (!modal) return;

    modal.classList.remove('modal-visible');

    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 200);
};

// Close modals on Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('[role="dialog"]:not(.hidden)').forEach(m => window.closeModal(m.id));
    }
});

// ===================== TOAST SYSTEM =====================
window.showToast = function (message, type = 'success') {
    const colors = {
        success: 'toast-success',
        error: 'toast-error',
        warning: 'toast-warning',
        info: 'toast-info',
    };
    const icons = {
        success: 'check-circle',
        error: 'x-circle',
        warning: 'alert-triangle',
        info: 'info',
    };

    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast ${colors[type]}`;
    toast.innerHTML = `<i data-lucide="${icons[type]}" class="icon"></i><span>${message}</span>`;
    container.appendChild(toast);
    window.initLucide();

    requestAnimationFrame(() => toast.classList.add('toast-show'));

    setTimeout(() => {
        toast.classList.remove('toast-show');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
};

// ===================== AJAX FORM HELPER =====================
window.submitModalForm = async function (formId, url, modalId, onSuccess) {
    const form = document.getElementById(formId);
    if (!form) return;

    const formData = new FormData(form);
    const submitBtn = form.querySelector('[type="submit"], .btn-submit-modal');
    const originalHtml = submitBtn ? submitBtn.innerHTML : '';

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader-2" class="icon spin"></i> Guardando...';
        window.initLucide();
    }

    form.querySelectorAll('.field-error').forEach(e => e.remove());
    form.querySelectorAll('.is-invalid').forEach(e => e.classList.remove('is-invalid'));

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: formData,
        });

        const data = await response.json();

        if (data.success) {
            window.showToast(data.message, 'success');
            window.closeModal(modalId);
            form.reset();
            if (onSuccess) onSuccess(data);
        } else {
            if (data.errors) {
                Object.entries(data.errors).forEach(([field, messages]) => {
                    let input = form.querySelector(`[name="${field}"]`);
                    if (!input && field.includes('.')) {
                        const parts = field.split('.');
                        if (parts.length === 3) {
                            input = form.querySelector(`[name="${parts[0]}[${parts[1]}][${parts[2]}]"]`);
                        }
                    }

                    if (input) {
                        input.classList.add('is-invalid');
                        const errDiv = document.createElement('p');
                        errDiv.className = 'field-error form-error-app';
                        errDiv.textContent = messages[0];
                        input.parentElement.appendChild(errDiv);
                    }
                });
            }
            window.showToast(data.message || 'Error al procesar.', 'error');
        }
    } catch (err) {
        window.showToast('Error de conexión. Inténtalo de nuevo.', 'error');
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
            window.initLucide();
        }
    }
};

// ===================== SIDEBAR TOGGLE =====================
document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.getElementById('appWrapper');
    const sidebar = document.getElementById('appSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('toggleSidebar');
    const closeBtn = document.getElementById('closeSidebar');

    if (wrapper && sidebar) {
        const savedState = localStorage.getItem('sidebar_state') || 'expanded';
        if (window.innerWidth >= 768) {
            wrapper.setAttribute('data-sidebar-expanded', savedState === 'expanded' ? 'true' : 'false');
            if (savedState === 'collapsed') {
                sidebar.classList.add('collapsed');
            }
        }

        function toggleSidebar() {
            if (window.innerWidth < 768) {
                sidebar.classList.toggle('show');
                if (overlay) overlay.classList.toggle('d-none');
            } else {
                const isExpanded = wrapper.getAttribute('data-sidebar-expanded') === 'true';
                const newExpanded = !isExpanded;
                wrapper.setAttribute('data-sidebar-expanded', newExpanded ? 'true' : 'false');
                sidebar.classList.toggle('collapsed', !newExpanded);
                localStorage.setItem('sidebar_state', newExpanded ? 'expanded' : 'collapsed');
            }
        }

        if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
        if (closeBtn) closeBtn.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);

        window.addEventListener('keydown', (e) => {
            if (e.key === 'b' && (e.metaKey || e.ctrlKey)) {
                e.preventDefault();
                toggleSidebar();
            }
        });
    }
});
