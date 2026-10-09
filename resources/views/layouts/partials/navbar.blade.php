{{-- ============ NAVBAR / TOP HEADER ============ --}}
@php
    $usuario = auth()->user();
@endphp

<header class="app-navbar">
    <div class="navbar-left">
        <button class="navbar-toggle" id="toggleSidebar" type="button" aria-label="Alternar sidebar">
            <i data-lucide="menu" class="icon"></i>
        </button>

        <div class="navbar-divider"></div>

        <div class="navbar-brand">
            <div class="navbar-brand-icon">
                <i data-lucide="book-marked" class="icon"></i>
            </div>
            <h1 class="navbar-title">@yield('page-title', 'Sistema de Banco de Libros')</h1>
        </div>
    </div>

    <div class="navbar-right">
        {{-- Notificaciones (visual únicamente) --}}
        <button type="button" class="navbar-action" aria-label="Notificaciones">
            <i data-lucide="bell" class="icon"></i>
            <span class="navbar-action-badge" aria-hidden="true"></span>
        </button>

        @if($usuario)
            <div class="navbar-user" title="{{ $usuario->nombre }}">
                <div class="navbar-user-avatar">
                    {{ strtoupper(substr($usuario->nombre ?? 'U', 0, 1)) }}
                </div>
                <div class="navbar-user-info">
                    <p class="navbar-user-name">{{ $usuario->nombre ?? 'Usuario' }}</p>
                    <p class="navbar-user-role">{{ ucfirst($usuario->rol ?? 'Usuario') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="navbar-logout">
                    <i data-lucide="log-out" class="icon"></i>
                    <span class="navbar-logout-text">Cerrar sesión</span>
                </button>
            </form>
        @endif
    </div>
</header>
