<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Biblioteca\AreaBibliotecaController;
use App\Http\Controllers\Biblioteca\AutorBibliotecaController;
use App\Http\Controllers\Biblioteca\CatalogoBibliotecaController;
use App\Http\Controllers\Biblioteca\DashboardBibliotecaController;
use App\Http\Controllers\Biblioteca\DevolucionBibliotecaController;
use App\Http\Controllers\Biblioteca\EditorialBibliotecaController;
use App\Http\Controllers\Biblioteca\EjemplarBibliotecaController;
use App\Http\Controllers\Biblioteca\EstanteBibliotecaController;
use App\Http\Controllers\Biblioteca\MultaBibliotecaController;
use App\Http\Controllers\Biblioteca\PrestamoBibliotecaController;
use App\Http\Controllers\Biblioteca\ReporteBibliotecaController;
use App\Http\Controllers\Biblioteca\SocioBibliotecaController;
use App\Http\Controllers\Biblioteca\TipoMaterialBibliotecaController;
use App\Http\Controllers\Biblioteca\UbicacionBibliotecaController;
use App\Http\Controllers\Biblioteca\VisitaBibliotecaController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\EntregaController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas publicas (Login)
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::post('logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas protegidas (Administrador / Bibliotecario)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('usuarios', UsuarioController::class);

    Route::get('estudiantes/buscar-dni', [EstudianteController::class, 'buscarDni'])->name('estudiantes.buscar_dni');
    Route::get('estudiantes/api/buscar', [EstudianteController::class, 'buscarApi'])->name('estudiantes.api.buscar');
    Route::post('estudiantes/api/store', [EstudianteController::class, 'storeApi'])->name('estudiantes.api.store');
    Route::resource('estudiantes', EstudianteController::class);

    Route::get('libros/api/buscar', [LibroController::class, 'buscarLibros'])->name('libros.api.buscar');
    Route::post('libros/api/store', [LibroController::class, 'storeApi'])->name('libros.api.store');
    Route::get('libros/api/siguiente-codigo', [LibroController::class, 'siguienteCodigo'])->name('libros.api.siguiente-codigo');
    Route::resource('libros', LibroController::class);

    // Entregas
    Route::get('entregas/api/buscar-estudiantes', [EntregaController::class, 'buscarEstudiantes'])->name('entregas.api.buscar-estudiantes');
    Route::get('entregas/api/libros-por-grado', [EntregaController::class, 'librosPorGrado'])->name('entregas.api.libros-por-grado');
    Route::post('entregas/api/store', [EntregaController::class, 'storeApi'])->name('entregas.api.store');
    Route::post('entregas/{entrega}/api/update', [EntregaController::class, 'updateApi'])->name('entregas.api.update');
    Route::resource('entregas', EntregaController::class);
    Route::put('entregas/{entrega}/codigo', [EntregaController::class, 'actualizarCodigo'])->name('entregas.codigo.update');
    Route::get('entregas/{entrega}/ver-constancia', [EntregaController::class, 'verConstancia'])->name('entregas.ver-constancia');
    Route::get('entregas/{entrega}/constancia', [EntregaController::class, 'constancia'])->name('entregas.constancia');

    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/por-grado', [ReporteController::class, 'porGrado'])->name('reportes.por_grado');
    Route::get('reportes/por-estudiante', [ReporteController::class, 'porEstudiante'])->name('reportes.por_estudiante');
    Route::get('reportes/por-libro', [ReporteController::class, 'porLibro'])->name('reportes.por_libro');
    Route::get('reportes/entregas', [ReporteController::class, 'entregasRealizadas'])->name('reportes.entregas');
    Route::get('reportes/devoluciones', [ReporteController::class, 'devolucionesRealizadas'])->name('reportes.devoluciones');
    Route::get('reportes/pendientes', [ReporteController::class, 'pendientes'])->name('reportes.pendientes');
    Route::get('reportes/stock', [ReporteController::class, 'stockLibros'])->name('reportes.stock');
    Route::get('reportes/inventario', [ReporteController::class, 'inventarioGeneral'])->name('reportes.inventario');
    Route::get('reportes/stock-critico', [ReporteController::class, 'stockCritico'])->name('reportes.stock_critico');
    Route::get('reportes/situacion-estudiantes', [ReporteController::class, 'situacionEstudiantes'])->name('reportes.situacion_estudiantes');
    Route::get('reportes/historial-estudiante', [ReporteController::class, 'historialEstudiante'])->name('reportes.historial_estudiante');
    Route::get('reportes/perdidos', [ReporteController::class, 'perdidos'])->name('reportes.perdidos');
    Route::get('reportes/deteriorados', [ReporteController::class, 'deteriorados'])->name('reportes.deteriorados');
    Route::get('reportes/bajas', [ReporteController::class, 'bajas'])->name('reportes.bajas');
    Route::get('reportes/por-anio', [ReporteController::class, 'porAnio'])->name('reportes.por_anio');
    Route::get('reportes/comparativo', [ReporteController::class, 'comparativo'])->name('reportes.comparativo');

    Route::get('devoluciones/api/pendientes', [DevolucionController::class, 'pendientesApi'])->name('devoluciones.api.pendientes');
    Route::post('devoluciones/api/store', [DevolucionController::class, 'storeApi'])->name('devoluciones.api.store');
    Route::post('devoluciones/{devolucion}/api/update', [DevolucionController::class, 'updateApi'])->name('devoluciones.api.update');
    Route::resource('devoluciones', DevolucionController::class);
    Route::get('devoluciones/{devolucion}/ver-constancia', [DevolucionController::class, 'verConstancia'])->name('devoluciones.ver-constancia');
    Route::get('devoluciones/{devolucion}/constancia', [DevolucionController::class, 'constancia'])->name('devoluciones.constancia');

    // Configuraciones
    Route::get('configuraciones', [ConfiguracionController::class, 'index'])->name('configuraciones.index');

    Route::get('configuraciones/estudiantes', [ConfiguracionController::class, 'estudiantes'])->name('configuraciones.estudiantes');
    Route::post('configuraciones/estudiantes/{categoria}/agregar', [ConfiguracionController::class, 'agregarItem'])->name('configuraciones.estudiantes.agregar');
    Route::put('configuraciones/estudiantes/{categoria}/{id}/actualizar', [ConfiguracionController::class, 'actualizarItem'])->name('configuraciones.estudiantes.actualizar');
    Route::put('configuraciones/estudiantes/{categoria}/{id}/toggle', [ConfiguracionController::class, 'toggleItem'])->name('configuraciones.estudiantes.toggle');
    Route::delete('configuraciones/estudiantes/{categoria}/{id}/eliminar', [ConfiguracionController::class, 'eliminarItem'])->name('configuraciones.estudiantes.eliminar');
    Route::put('configuraciones/estudiantes', [ConfiguracionController::class, 'updateEstudiantes'])->name('configuraciones.estudiantes.update');

    Route::get('configuraciones/libros', [ConfiguracionController::class, 'libros'])->name('configuraciones.libros');
    Route::post('configuraciones/libros/{categoria}/agregar', [ConfiguracionController::class, 'agregarItemLibro'])->name('configuraciones.libros.agregar');
    Route::put('configuraciones/libros/{categoria}/{id}/actualizar', [ConfiguracionController::class, 'actualizarItemLibro'])->name('configuraciones.libros.actualizar');
    Route::put('configuraciones/libros/{categoria}/{id}/toggle', [ConfiguracionController::class, 'toggleItemLibro'])->name('configuraciones.libros.toggle');
    Route::delete('configuraciones/libros/{categoria}/{id}/eliminar', [ConfiguracionController::class, 'eliminarItemLibro'])->name('configuraciones.libros.eliminar');

    /*
    |--------------------------------------------------------------------------
    | Biblioteca General
    |--------------------------------------------------------------------------
    */
    Route::prefix('biblioteca')->name('biblioteca.')->group(function () {
        Route::get('dashboard', [DashboardBibliotecaController::class, 'index'])->name('dashboard.index');

        Route::resource('catalogo', CatalogoBibliotecaController::class)->except(['create', 'edit', 'show']);
        Route::resource('ejemplares', EjemplarBibliotecaController::class)->except(['create', 'edit', 'show']);
        Route::resource('socios', SocioBibliotecaController::class)->except(['create', 'edit', 'show']);
        Route::resource('prestamos', PrestamoBibliotecaController::class)->except(['create', 'edit', 'show', 'update']);
        Route::get('devoluciones', [DevolucionBibliotecaController::class, 'index'])->name('devoluciones.index');
        Route::post('devoluciones/{prestamo}', [DevolucionBibliotecaController::class, 'store'])->name('devoluciones.store');

        Route::resource('autores', AutorBibliotecaController::class)->except(['create', 'edit', 'show']);
        Route::resource('editoriales', EditorialBibliotecaController::class)->except(['create', 'edit', 'show']);
        Route::resource('areas', AreaBibliotecaController::class)->except(['create', 'edit', 'show']);
        Route::resource('tipos-material', TipoMaterialBibliotecaController::class)->except(['create', 'edit', 'show']);
        Route::resource('ubicaciones', UbicacionBibliotecaController::class)->except(['create', 'edit', 'show']);
        Route::resource('estantes', EstanteBibliotecaController::class)->except(['create', 'edit', 'show']);

        Route::resource('multas', MultaBibliotecaController::class)->except(['create', 'edit', 'show']);
        Route::resource('visitas', VisitaBibliotecaController::class)->except(['create', 'edit', 'show']);
        Route::get('reportes', [ReporteBibliotecaController::class, 'index'])->name('reportes.index');
    });

});
