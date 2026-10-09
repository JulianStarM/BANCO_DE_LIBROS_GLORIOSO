<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Banco de Libros') | I.E. Glorioso San Carlos</title>

    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2" type="image/x-icon">
    <link rel="icon" href="{{ asset('favicon.ico') }}?v=2" type="image/x-icon">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')

    {{-- Toast system inline - NO depende del bundle de Vite --}}
    <style>
        .toast-container-inline {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            pointer-events: none;
        }
        .toast-inline {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            color: #ffffff;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.12);
            font-size: 0.875rem;
            font-weight: 500;
            max-width: 24rem;
            transform: translateX(150%);
            transition: transform 400ms cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: auto;
        }
        .toast-inline.show {
            transform: translateX(0);
        }
        .toast-inline.success { background-color: #16A34A; }
        .toast-inline.error { background-color: #E65345; }
        .toast-inline.warning { background-color: #F59E0B; }
        .toast-inline.info { background-color: #0EA5E9; }
        .toast-inline .icon {
            width: 1.25rem;
            height: 1.25rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body>

<div id="appWrapper" class="app-wrapper" data-sidebar-expanded="true">

    {{-- Sidebar --}}
    @include('layouts.partials.sidebar')

    {{-- Main Content --}}
    <div class="main-content">

        {{-- Navbar --}}
        @include('layouts.partials.navbar')

        {{-- Content Area --}}
        <main class="content-scroll">
            @yield('content')
        </main>
    </div>
</div>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

{{-- Toast system standalone - funciona sin depender del bundle --}}
<script>
    (function() {
        // Crear contenedor si no existe
        let container = document.getElementById('toast-container-inline');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container-inline';
            container.className = 'toast-container-inline';
            document.body.appendChild(container);
        }

        // Función global showToast
        window.showToast = function(message, type) {
            type = type || 'success';
            const icons = {
                success: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
                error: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
                warning: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
                info: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
            };

            const toast = document.createElement('div');
            toast.className = 'toast-inline ' + type;
            toast.innerHTML = (icons[type] || icons.success) + '<span>' + message + '</span>';
            container.appendChild(toast);

            // Animación de entrada
            requestAnimationFrame(function() {
                toast.classList.add('show');
            });

            // Auto-cerrar después de 4 segundos
            setTimeout(function() {
                toast.classList.remove('show');
                setTimeout(function() {
                    if (toast.parentNode) toast.remove();
                }, 400);
            }, 4000);
        };

        window.displayAppNotification = function (message, type) {
            type = type || 'success';
            if (window.showFeedbackModal) {
                const titles = {
                    success: '¡Operación exitosa!',
                    error: 'Ocurrió un error',
                    warning: 'Advertencia',
                    info: 'Información',
                };
                window.showFeedbackModal({
                    type: type,
                    title: titles[type] || 'Información',
                    message: message,
                    autoClose: type === 'success' ? 2500 : 0,
                });
            } else {
                window.showToast(message, type);
            }
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Mostrar notificaciones persistidas desde localStorage (después de reload AJAX)
            const persistedToast = localStorage.getItem('app_toast');
            if (persistedToast) {
                try {
                    const data = JSON.parse(persistedToast);
                    window.displayAppNotification(data.message, data.type || 'success');
                } catch (e) {}
                localStorage.removeItem('app_toast');
            }

            // Mostrar notificaciones de session flash (desde redirect PHP)
            @if (session('success'))
                window.displayAppNotification(@json(session('success')), 'success');
            @endif
            @if (session('error'))
                window.displayAppNotification(@json(session('error')), 'error');
            @endif
            @if (session('warning'))
                window.displayAppNotification(@json(session('warning')), 'warning');
            @endif
            @if (session('info'))
                window.displayAppNotification(@json(session('info')), 'info');
            @endif
        });
    })();
</script>

@include('components.feedback-modal')

@stack('scripts')

</body>
</html>
