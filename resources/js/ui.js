// resources/js/ui.js

// Import Lucide (assuming it's installed via npm, or we rely on the CDN)
// If relying on CDN, it's attached to window. We'll just call it.
// We will export functions to window to be accessible from inline HTML onclick handlers.

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
    // Animate in
    requestAnimationFrame(() => {
        const overlay = modal.querySelector('.modal-overlay');
        const panel = modal.querySelector('.modal-panel');
        if (overlay) overlay.classList.replace('opacity-0', 'opacity-100');
        if (panel) {
            panel.classList.replace('scale-95', 'scale-100');
            panel.classList.replace('opacity-0', 'opacity-100');
            panel.classList.replace('translate-y-4', 'translate-y-0');
            panel.classList.replace('sm:translate-y-8', 'sm:translate-y-0');
        }
    });
    // Re-render lucide icons inside modal
    setTimeout(() => window.initLucide(), 50);
};

window.closeModal = function (id) {
    const modal = document.getElementById(id);
    if (!modal) return;
    const overlay = modal.querySelector('.modal-overlay');
    const panel = modal.querySelector('.modal-panel');
    if (overlay) overlay.classList.replace('opacity-100', 'opacity-0');
    if (panel) {
        panel.classList.replace('scale-100', 'scale-95');
        panel.classList.replace('opacity-100', 'opacity-0');
        panel.classList.replace('translate-y-0', 'translate-y-4');
        panel.classList.replace('sm:translate-y-0', 'sm:translate-y-8');
    }
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 500); // Increased timeout to match the 500ms duration of the new CSS transition
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
        success: 'bg-emerald-600',
        error: 'bg-red-600',
        warning: 'bg-amber-600',
        info: 'bg-blue-600',
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
        container.className = 'fixed top-4 right-4 z-[9999] flex flex-col gap-2';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `${colors[type]} text-white px-5 py-3 rounded-xl shadow-xl flex items-center gap-3 text-sm font-medium transform translate-x-full transition-transform duration-300 max-w-sm`;
    toast.innerHTML = `<i data-lucide="${icons[type]}" class="h-5 w-5 shrink-0"></i><span>${message}</span>`;
    container.appendChild(toast);
    window.initLucide();
    
    requestAnimationFrame(() => toast.classList.replace('translate-x-full', 'translate-x-0'));
    
    setTimeout(() => {
        toast.classList.replace('translate-x-0', 'translate-x-full');
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
        submitBtn.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 animate-spin"></i> Guardando...';
        window.initLucide();
    }
    
    // Clear previous errors
    form.querySelectorAll('.field-error').forEach(e => e.remove());
    form.querySelectorAll('.border-red-500').forEach(e => e.classList.remove('border-red-500'));
    
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
                    // Trata arrays en inputs como `libros.*` buscando `name="libros[]"` u otras variantes
                    // Para simplificar, buscamos nombres exactos primero
                    let input = form.querySelector(`[name="${field}"]`);
                    if (!input && field.includes('.')) {
                         const parts = field.split('.');
                         // Por ejemplo, "detalles.0.entregas" -> `name="detalles[0][entregas]"`
                         if(parts.length === 3) {
                             input = form.querySelector(`[name="${parts[0]}[${parts[1]}][${parts[2]}]"]`);
                         }
                    }

                    if (input) {
                        input.classList.add('border-red-500');
                        const errDiv = document.createElement('p');
                        errDiv.className = 'field-error text-xs text-red-500 mt-1';
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
    const wrapper = document.getElementById('sidebar-wrapper');
    const sidebar = document.getElementById('appSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('toggleSidebar');
    const closeBtn = document.getElementById('closeSidebar');

    if (wrapper) {
        const savedState = localStorage.getItem('sidebar_state') || 'expanded';
        if (window.innerWidth >= 768) {
            wrapper.setAttribute('data-state', savedState);
        }

        function toggleSidebar() {
            if (window.innerWidth < 768) {
                sidebar.classList.toggle('-translate-x-full');
                if (overlay) overlay.classList.toggle('hidden');
            } else {
                const currentState = wrapper.getAttribute('data-state');
                const newState = currentState === 'expanded' ? 'collapsed' : 'expanded';
                wrapper.setAttribute('data-state', newState);
                localStorage.setItem('sidebar_state', newState);
            }
        }
        
        const style = document.createElement('style');
        style.innerHTML = `
            .group\\/sidebar-wrapper[data-state="expanded"] [data-tooltip]:hover::after {
                display: none;
            }
        `;
        document.head.appendChild(style);

        if(toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
        if(closeBtn) closeBtn.addEventListener('click', toggleSidebar);
        if(overlay) overlay.addEventListener('click', toggleSidebar);

        window.addEventListener('keydown', (e) => {
            if (e.key === 'b' && (e.metaKey || e.ctrlKey)) {
                e.preventDefault();
                toggleSidebar();
            }
        });
    }
});
