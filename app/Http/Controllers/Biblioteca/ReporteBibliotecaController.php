<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibCatalogo;
use App\Models\BibEjemplar;
use App\Models\BibMulta;
use App\Models\BibPrestamo;
use App\Models\BibSocio;
use App\Models\BibVisita;
use Carbon\Carbon;
use Illuminate\View\View;

class ReporteBibliotecaController extends Controller
{
    public function index(): View
    {
        $totalTitulos = BibCatalogo::count();
        $totalEjemplares = BibEjemplar::count();
        $ejemplaresDisponibles = BibEjemplar::where('estado_disponibilidad', 'disponible')->count();
        $ejemplaresPrestados = BibEjemplar::where('estado_disponibilidad', 'prestado')->count();
        $ejemplaresBaja = BibEjemplar::where('estado_disponibilidad', 'baja')->count();
        $totalSocios = BibSocio::count();
        $prestamosMes = BibPrestamo::whereMonth('fecha_prestamo', Carbon::now()->month)
            ->whereYear('fecha_prestamo', Carbon::now()->year)
            ->count();
        $prestamosVencidos = BibPrestamo::whereIn('estado', ['activo', 'vencido'])
            ->where('fecha_limite', '<', Carbon::today())
            ->count();
        $multasPendientes = BibMulta::where('estado', 'pendiente')->count();
        $visitasMes = BibVisita::whereMonth('fecha', Carbon::now()->month)
            ->whereYear('fecha', Carbon::now()->year)
            ->count();

        $prestamosPorEstado = [
            'activo' => BibPrestamo::where('estado', 'activo')->count(),
            'devuelto' => BibPrestamo::where('estado', 'devuelto')->count(),
            'vencido' => BibPrestamo::where('estado', 'vencido')->count(),
            'perdido' => BibPrestamo::where('estado', 'perdido')->count(),
        ];

        return view('biblioteca.reportes.index', compact(
            'totalTitulos',
            'totalEjemplares',
            'ejemplaresDisponibles',
            'ejemplaresPrestados',
            'ejemplaresBaja',
            'totalSocios',
            'prestamosMes',
            'prestamosVencidos',
            'multasPendientes',
            'visitasMes',
            'prestamosPorEstado'
        ));
    }
}
