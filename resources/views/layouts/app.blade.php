<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Banco de Libros') | GSC Puno</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="bg-gray-50 text-slate-900 font-sans antialiased overflow-hidden">

<div id="sidebar-wrapper" class="group/sidebar-wrapper flex h-screen w-full" data-state="expanded">
    
    {{-- ============ SIDEBAR ============ --}}
    <!-- Mobile overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

    <aside id="appSidebar" class="fixed inset-y-0 left-0 z-50 flex h-full flex-col bg-slate-900 text-slate-300 transition-all duration-200 ease-linear 
        -translate-x-full md:translate-x-0 md:static 
        w-64 group-data-[state=collapsed]/sidebar-wrapper:w-[3.5rem]">
        
        <!-- Sidebar Header -->
        <div class="flex items-center gap-3 p-3 h-16 flex-shrink-0 overflow-hidden bg-slate-950/50">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-600 text-white shadow-sm">
                <i data-lucide="book-marked" class="h-5 w-5"></i>
            </div>
            <div class="flex flex-col min-w-0 flex-1 whitespace-nowrap transition-opacity duration-200 group-data-[state=collapsed]/sidebar-wrapper:opacity-0 group-data-[state=collapsed]/sidebar-wrapper:hidden">
                <p class="truncate text-base font-bold text-white tracking-wide">Banco de Libros</p>
                <p class="truncate text-[11px] text-slate-400 font-medium">I.E. Glorioso San Carlos</p>
            </div>
            <!-- Mobile close button -->
            <button id="closeSidebar" class="ml-auto md:hidden text-slate-400 hover:text-white shrink-0">
                <i data-lucide="x" class="h-5 w-5"></i>
            </button>
        </div>

        <!-- Sidebar Content -->
        <div class="flex-1 overflow-y-auto py-4 overflow-x-hidden">
            <div class="px-4 mb-2 whitespace-nowrap transition-opacity duration-200 group-data-[state=collapsed]/sidebar-wrapper:opacity-0 group-data-[state=collapsed]/sidebar-wrapper:hidden">
                <p class="text-xs font-semibold text-slate-500">Gestión</p>
            </div>
            
            <nav class="space-y-1.5 px-3">
                <a href="{{ route('dashboard') }}" data-tooltip="Dashboard" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('dashboard') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="layout-dashboard" class="h-5 w-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-500' : '' }}"></i>
                    <span class="whitespace-nowrap transition-opacity duration-200 group-data-[state=collapsed]/sidebar-wrapper:opacity-0 group-data-[state=collapsed]/sidebar-wrapper:hidden">Dashboard</span>
                </a>
                <a href="{{ route('estudiantes.index') }}" data-tooltip="Estudiantes" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('estudiantes.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="graduation-cap" class="h-5 w-5 shrink-0 {{ request()->routeIs('estudiantes.*') ? 'text-blue-500' : '' }}"></i>
                    <span class="whitespace-nowrap transition-opacity duration-200 group-data-[state=collapsed]/sidebar-wrapper:opacity-0 group-data-[state=collapsed]/sidebar-wrapper:hidden">Estudiantes</span>
                </a>
                <a href="{{ route('libros.index') }}" data-tooltip="Libros" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('libros.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="book-open" class="h-5 w-5 shrink-0 {{ request()->routeIs('libros.*') ? 'text-blue-500' : '' }}"></i>
                    <span class="whitespace-nowrap transition-opacity duration-200 group-data-[state=collapsed]/sidebar-wrapper:opacity-0 group-data-[state=collapsed]/sidebar-wrapper:hidden">Libros</span>
                </a>
                <a href="{{ route('entregas.index') }}" data-tooltip="Entregas" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('entregas.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="clipboard-list" class="h-5 w-5 shrink-0 {{ request()->routeIs('entregas.*') ? 'text-blue-500' : '' }}"></i>
                    <span class="whitespace-nowrap transition-opacity duration-200 group-data-[state=collapsed]/sidebar-wrapper:opacity-0 group-data-[state=collapsed]/sidebar-wrapper:hidden">Entregas</span>
                </a>
                <a href="{{ route('reportes.index') }}" data-tooltip="Reportes" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('reportes.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="file-text" class="h-5 w-5 shrink-0 {{ request()->routeIs('reportes.*') ? 'text-blue-500' : '' }}"></i>
                    <span class="whitespace-nowrap transition-opacity duration-200 group-data-[state=collapsed]/sidebar-wrapper:opacity-0 group-data-[state=collapsed]/sidebar-wrapper:hidden">Reportes</span>
                </a>
                
                @if(auth()->user() && (auth()->user()->rol === 'Administrador' || auth()->user()->rol === 'Admin'))
                <a href="{{ route('usuarios.index') }}" data-tooltip="Usuarios" class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 relative {{ request()->routeIs('usuarios.*') ? 'bg-blue-600/10 text-blue-400' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="users" class="h-5 w-5 shrink-0 {{ request()->routeIs('usuarios.*') ? 'text-blue-500' : '' }}"></i>
                    <span class="whitespace-nowrap transition-opacity duration-200 group-data-[state=collapsed]/sidebar-wrapper:opacity-0 group-data-[state=collapsed]/sidebar-wrapper:hidden">Usuarios</span>
                </a>
                @endif
            </nav>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/30 overflow-hidden mt-auto">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white shadow-inner">
                    {{ strtoupper(substr(auth()->user()->nombres ?? auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div class="flex flex-col min-w-0 flex-1 whitespace-nowrap transition-opacity duration-200 group-data-[state=collapsed]/sidebar-wrapper:opacity-0 group-data-[state=collapsed]/sidebar-wrapper:hidden">
                    <p class="truncate text-sm font-semibold text-white leading-tight">{{ auth()->user()->nombres ?? auth()->user()->name ?? 'Administrador GSC' }}</p>
                    <p class="truncate text-[11px] text-slate-400 mt-0.5">{{ auth()->user()->rol ?? 'Administrador' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="w-full transition-opacity duration-200 group-data-[state=collapsed]/sidebar-wrapper:opacity-0 group-data-[state=collapsed]/sidebar-wrapper:hidden">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium rounded-lg bg-slate-800 text-slate-300 hover:bg-red-500/10 hover:text-red-400 transition-colors border border-slate-700 hover:border-red-500/30">
                    <i data-lucide="log-out" class="h-4 w-4 shrink-0"></i>
                    <span>Cerrar sesión</span>
                </button>
            </form>
            <!-- Minimized logout for collapsed state -->
            <form method="POST" action="{{ route('logout') }}" class="w-full hidden group-data-[state=collapsed]/sidebar-wrapper:block">
                @csrf
                <button type="submit" data-tooltip="Cerrar sesión" class="w-full flex items-center justify-center py-2 text-slate-400 hover:text-red-400 transition-colors">
                    <i data-lucide="log-out" class="h-5 w-5 shrink-0"></i>
                </button>
            </form>
        </div>
    </aside>

    {{-- ============ MAIN CONTENT ============ --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#f5f7fb] relative">
        
        <!-- Toggle Button -->
        <header class="h-16 flex items-center px-6 border-b border-slate-200 bg-white justify-between shrink-0 shadow-sm z-10">
            <div class="flex items-center gap-4">
                <button class="p-2 -ml-2 rounded-xl text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors" id="toggleSidebar">
                    <i data-lucide="menu" class="h-5 w-5"></i>
                </button>
                <div class="h-6 w-px bg-slate-200 hidden md:block"></div>
                <h1 class="text-base font-semibold text-slate-800 m-0 hidden md:block tracking-tight">Sistema de Banco de Libros</h1>
            </div>
            
            <div class="flex items-center">
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-slate-50">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('scripts')
</body>
</html>

