<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibEjemplar;
use App\Models\BibPrestamo;
use App\Models\BibSocio;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PrestamoBibliotecaController extends Controller
{
    public function index(Request $request): View
    {
        $query = BibPrestamo::query()
            ->with(['socio', 'detalles.ejemplar.catalogo']);

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo_prestamo', 'like', "%{$buscar}%")
                    ->orWhereHas('socio', function ($sq) use ($buscar) {
                        $sq->where('nombres', 'like', "%{$buscar}%")
                            ->orWhere('apellidos', 'like', "%{$buscar}%");
                    });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        $prestamos = $query->orderByDesc('fecha_prestamo')->paginate(20)->withQueryString();
        $socios = BibSocio::where('estado', 'activo')->orderBy('apellidos')->get();
        $ejemplares = BibEjemplar::with('catalogo')
            ->where('estado_disponibilidad', 'disponible')
            ->where('estado_fisico', '!=', 'baja')
            ->orderBy('codigo_ejemplar')
            ->get();

        return view('biblioteca.prestamos.index', compact('prestamos', 'socios', 'ejemplares'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_socio' => ['required', 'exists:bib_socios,id'],
            'tipo_prestamo' => ['required', 'in:sala,domicilio'],
            'fecha_prestamo' => ['required', 'date'],
            'fecha_limite' => ['required', 'date', 'after_or_equal:fecha_prestamo'],
            'ejemplares' => ['required', 'array', 'min:1'],
            'ejemplares.*' => ['required', 'exists:bib_ejemplares,id'],
            'observaciones' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {
            $prestamo = BibPrestamo::create([
                'id_socio' => $validated['id_socio'],
                'id_usuario_registro' => auth()->id(),
                'codigo_prestamo' => $this->generarCodigo(),
                'tipo_prestamo' => $validated['tipo_prestamo'],
                'fecha_prestamo' => $validated['fecha_prestamo'],
                'hora_prestamo' => Carbon::now()->format('H:i:s'),
                'fecha_limite' => $validated['fecha_limite'],
                'estado' => 'activo',
                'renovaciones' => 0,
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            foreach ($validated['ejemplares'] as $idEjemplar) {
                $ejemplar = BibEjemplar::lockForUpdate()->findOrFail($idEjemplar);

                if (! $ejemplar->estaDisponible()) {
                    throw new \Exception("El ejemplar {$ejemplar->codigo_ejemplar} no está disponible.");
                }

                $prestamo->detalles()->create([
                    'id_ejemplar' => $ejemplar->id,
                    'estado_devolucion' => 'pendiente',
                    'estado_fisico_salida' => $ejemplar->estado_fisico,
                ]);

                $ejemplar->update(['estado_disponibilidad' => 'prestado']);
            }

            DB::commit();

            return redirect()->route('biblioteca.prestamos.index')->with('success', 'Préstamo registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('biblioteca.prestamos.index')->with('error', $e->getMessage());
        }
    }

    public function destroy(BibPrestamo $prestamo): RedirectResponse
    {
        if ($prestamo->detalles()->where('estado_devolucion', 'pendiente')->exists()) {
            return redirect()->route('biblioteca.prestamos.index')->with('error', 'No se puede eliminar un préstamo con ejemplares pendientes.');
        }

        DB::transaction(function () use ($prestamo) {
            $prestamo->detalles()->delete();
            $prestamo->delete();
        });

        return redirect()->route('biblioteca.prestamos.index')->with('success', 'Préstamo eliminado.');
    }

    private function generarCodigo(): string
    {
        $ultimo = BibPrestamo::orderByDesc('id')->value('id') ?? 0;

        return 'PRE-'.date('Y').'-'.str_pad((string) ($ultimo + 1), 5, '0', STR_PAD_LEFT);
    }
}
