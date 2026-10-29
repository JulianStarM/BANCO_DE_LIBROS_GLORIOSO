{{-- ============ SIDEBAR ============ --}}
<!-- Mobile overlay -->
<div id="sidebarOverlay" class="sidebar-overlay d-md-none d-none"></div>

<aside id="appSidebar" class="sidebar">

    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="sidebar-brand-icon">
            <i data-lucide="book-marked" class="icon"></i>
        </div>
        <div class="sidebar-brand-text">
            <p class="sidebar-brand-title">Banco de Libros</p>
            <p class="sidebar-brand-subtitle">I.E. Glorioso San Carlos</p>
        </div>
        <!-- Mobile close button -->
        <button id="closeSidebar" class="sidebar-close d-md-none">
            <i data-lucide="x" class="icon"></i>
        </button>
    </div>

    <!-- Sidebar Content -->
    <div class="sidebar-content">
        <div class="sidebar-section-title">
            <p class="mb-0">Gestión</p>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" data-tooltip="Dashboard" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="icon"></i>
                <span class="sidebar-link-text">Dashboard</span>
            </a>
            <a href="{{ route('estudiantes.index') }}" data-tooltip="Estudiantes" class="sidebar-link {{ request()->routeIs('estudiantes.*') ? 'active' : '' }}">
                <i data-lucide="graduation-cap" class="icon"></i>
                <span class="sidebar-link-text">Estudiantes</span>
            </a>
            <a href="{{ route('libros.index') }}" data-tooltip="Libros" class="sidebar-link {{ request()->routeIs('libros.*') ? 'active' : '' }}">
                <i data-lucide="book-open" class="icon"></i>
                <span class="sidebar-link-text">Libros</span>
            </a>
            <a href="{{ route('entregas.index') }}" data-tooltip="Entregas" class="sidebar-link {{ request()->routeIs('entregas.*') ? 'active' : '' }}">
                <i data-lucide="clipboard-list" class="icon"></i>
                <span class="sidebar-link-text">Entregas</span>
            </a>
            <a href="{{ route('devoluciones.index') }}" data-tooltip="Devoluciones" class="sidebar-link {{ request()->routeIs('devoluciones.*') ? 'active' : '' }}">
                <i data-lucide="rotate-ccw" class="icon"></i>
                <span class="sidebar-link-text">Devoluciones</span>
            </a>
            <a href="{{ route('reportes.index') }}" data-tooltip="Reportes" class="sidebar-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                <i data-lucide="file-text" class="icon"></i>
                <span class="sidebar-link-text">Reportes</span>
            </a>

            @if(auth()->user() && auth()->user()->rol === 'admin')
            <a href="{{ route('usuarios.index') }}" data-tooltip="Usuarios" class="sidebar-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                <i data-lucide="users" class="icon"></i>
                <span class="sidebar-link-text">Usuarios</span>
            </a>
            @endif
        </nav>

        <div class="sidebar-section-title mt-4">
            <p class="mb-0">Biblioteca General</p>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('biblioteca.dashboard.index') }}" data-tooltip="Dashboard de Biblioteca" class="sidebar-link {{ request()->routeIs('biblioteca.dashboard.*') ? 'active' : '' }}">
                <i data-lucide="library" class="icon"></i>
                <span class="sidebar-link-text">Dashboard</span>
            </a>
            <a href="{{ route('biblioteca.catalogo.index') }}" data-tooltip="Catálogo de libros" class="sidebar-link {{ request()->routeIs('biblioteca.catalogo.*') ? 'active' : '' }}">
                <i data-lucide="book" class="icon"></i>
                <span class="sidebar-link-text">Catálogo de libros</span>
            </a>
            <a href="{{ route('biblioteca.prestamos.index') }}" data-tooltip="Préstamos y devoluciones" class="sidebar-link {{ request()->routeIs('biblioteca.prestamos.*') ? 'active' : '' }}">
                <i data-lucide="clipboard-list" class="icon"></i>
                <span class="sidebar-link-text">Préstamos</span>
            </a>
            <a href="{{ route('biblioteca.devoluciones.index') }}" data-tooltip="Devoluciones" class="sidebar-link {{ request()->routeIs('biblioteca.devoluciones.*') ? 'active' : '' }}">
                <i data-lucide="rotate-ccw" class="icon"></i>
                <span class="sidebar-link-text">Devoluciones</span>
            </a>
            <a href="{{ route('biblioteca.socios.index') }}" data-tooltip="Lectores" class="sidebar-link {{ request()->routeIs('biblioteca.socios.*') ? 'active' : '' }}">
                <i data-lucide="users" class="icon"></i>
                <span class="sidebar-link-text">Lectores</span>
            </a>
            <a href="{{ route('biblioteca.ejemplares.index') }}" data-tooltip="Inventario de ejemplares" class="sidebar-link {{ request()->routeIs('biblioteca.ejemplares.*') ? 'active' : '' }}">
                <i data-lucide="book-copy" class="icon"></i>
                <span class="sidebar-link-text">Inventario</span>
            </a>
            <a href="{{ route('biblioteca.autores.index') }}" data-tooltip="Autores" class="sidebar-link {{ request()->routeIs('biblioteca.autores.*') ? 'active' : '' }}">
                <i data-lucide="pen-tool" class="icon"></i>
                <span class="sidebar-link-text">Autores</span>
            </a>
            <a href="{{ route('biblioteca.editoriales.index') }}" data-tooltip="Editoriales" class="sidebar-link {{ request()->routeIs('biblioteca.editoriales.*') ? 'active' : '' }}">
                <i data-lucide="building" class="icon"></i>
                <span class="sidebar-link-text">Editoriales</span>
            </a>
            <a href="{{ route('biblioteca.areas.index') }}" data-tooltip="Categorías y áreas" class="sidebar-link {{ request()->routeIs('biblioteca.areas.*') ? 'active' : '' }}">
                <i data-lucide="tag" class="icon"></i>
                <span class="sidebar-link-text">Categorías y áreas</span>
            </a>
            <a href="{{ route('biblioteca.ubicaciones.index') }}" data-tooltip="Ubicaciones" class="sidebar-link {{ request()->routeIs('biblioteca.ubicaciones.*') ? 'active' : '' }}">
                <i data-lucide="map-pin" class="icon"></i>
                <span class="sidebar-link-text">Ubicaciones</span>
            </a>
            <a href="{{ route('biblioteca.estantes.index') }}" data-tooltip="Estantes" class="sidebar-link {{ request()->routeIs('biblioteca.estantes.*') ? 'active' : '' }}">
                <i data-lucide="align-justify" class="icon"></i>
                <span class="sidebar-link-text">Estantes</span>
            </a>
            <a href="{{ route('biblioteca.multas.index') }}" data-tooltip="Multas" class="sidebar-link {{ request()->routeIs('biblioteca.multas.*') ? 'active' : '' }}">
                <i data-lucide="alert-circle" class="icon"></i>
                <span class="sidebar-link-text">Multas</span>
            </a>
            <a href="{{ route('biblioteca.visitas.index') }}" data-tooltip="Visitas" class="sidebar-link {{ request()->routeIs('biblioteca.visitas.*') ? 'active' : '' }}">
                <i data-lucide="door-open" class="icon"></i>
                <span class="sidebar-link-text">Visitas</span>
            </a>
            <a href="{{ route('biblioteca.reportes.index') }}" data-tooltip="Reportes de biblioteca" class="sidebar-link {{ request()->routeIs('biblioteca.reportes.*') ? 'active' : '' }}">
                <i data-lucide="file-text" class="icon"></i>
                <span class="sidebar-link-text">Reportes</span>
            </a>

            @if(auth()->user() && auth()->user()->rol === 'admin')
            {{-- Configuraciones con submenu --}}
            <div class="sidebar-submenu">
                <button type="button"
                        class="sidebar-submenu-toggle {{ request()->routeIs('configuraciones.*') ? 'active' : '' }}"
                        data-bs-toggle="collapse"
                        aria-expanded="{{ request()->routeIs('configuraciones.*') ? 'true' : 'false' }}"
                        onclick="toggleConfigSubmenu(this)">
                    <i data-lucide="settings" class="icon"></i>
                    <span class="sidebar-link-text">Configuraciones</span>
                    <i data-lucide="chevron-down" class="icon icon-chevron"></i>
                </button>
                <div class="sidebar-submenu-items {{ request()->routeIs('configuraciones.*') ? 'show' : '' }}">
                    <a href="{{ route('configuraciones.index') }}" class="sidebar-submenu-link {{ request()->routeIs('configuraciones.index') ? 'active' : '' }}">
                        <span class="sidebar-link-text">General</span>
                    </a>
                    <a href="{{ route('configuraciones.estudiantes') }}" class="sidebar-submenu-link {{ request()->routeIs('configuraciones.estudiantes') ? 'active' : '' }}">
                        <span class="sidebar-link-text">Estudiantes</span>
                    </a>
                </div>
            </div>
            @endif
        </nav>
    </div>

</aside>

<script>
    function toggleConfigSubmenu(btn) {
        const items = btn.nextElementSibling;
        const isExpanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', !isExpanded);
        items.classList.toggle('show');
    }
</script>
