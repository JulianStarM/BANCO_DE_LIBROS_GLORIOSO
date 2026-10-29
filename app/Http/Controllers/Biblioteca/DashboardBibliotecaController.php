<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibCatalogo;
use App\Models\BibEjemplar;
use App\Models\BibPrestamo;
use App\Models\BibSocio;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardBibliotecaController extends Controller
{
    public function index(): View
    {
        $totalTitulos = BibCatalogo::count();
        $totalEjemplares = BibEjemplar::count();
        $ejemplaresDisponibles = BibEjemplar::where('estado_disponibilidad', 'disponible')->count();
        $ejemplaresPrestados = BibEjemplar::where('estado_disponibilidad', 'prestado')->count();
        $prestamosVencidos = BibPrestamo::whereIn('estado', ['activo', 'vencido'])
            ->where('fecha_limite', '<', Carbon::today())
            ->count();
        $totalSocios = BibSocio::count();
        $prestamosHoy = BibPrestamo::whereDate('fecha_prestamo', Carbon::today())->count();
        $devolucionesHoy = BibPrestamo::whereDate('fecha_devolucion', Carbon::today())->count();

        $prestamosRecientes = BibPrestamo::with(['socio', 'detalles.ejemplar.catalogo'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('biblioteca.dashboard.index', compact(
            'totalTitulos',
            'totalEjemplares',
            'ejemplaresDisponibles',
            'ejemplaresPrestados',
            'prestamosVencidos',
            'totalSocios',
            'prestamosHoy',
            'devolucionesHoy',
            'prestamosRecientes'
        ));
    }
}
