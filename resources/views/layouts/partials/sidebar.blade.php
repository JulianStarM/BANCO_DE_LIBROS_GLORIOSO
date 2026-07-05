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
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="sidebar-user-avatar">
                {{ strtoupper(substr(auth()->user()->nombre ?? 'U', 0, 1)) }}
            </div>
            <div class="sidebar-user-info">
                <p class="sidebar-user-name">{{ auth()->user()->nombre ?? 'Administrador GSC' }}</p>
                <p class="sidebar-user-role">{{ auth()->user()->rol ?? 'Administrador' }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="sidebar-logout-form">
            @csrf
            <button type="submit" class="sidebar-logout">
                <i data-lucide="log-out" class="icon"></i>
                <span class="sidebar-logout-text">Cerrar sesión</span>
            </button>
            <button type="submit" data-tooltip="Cerrar sesión" class="sidebar-logout-collapsed">
                <i data-lucide="log-out" class="icon"></i>
            </button>
        </form>
    </div>
</aside>
