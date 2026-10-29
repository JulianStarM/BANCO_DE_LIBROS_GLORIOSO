<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibDetallePrestamo;
use App\Models\BibMulta;
use App\Models\BibPrestamo;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DevolucionBibliotecaController extends Controller
{
    public function index(Request $request): View
    {
        $query = BibPrestamo::query()
            ->with(['socio', 'detalles.ejemplar.catalogo'])
            ->whereIn('estado', ['activo', 'vencido']);

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

        $prestamos = $query->orderByDesc('fecha_prestamo')->paginate(20)->withQueryString();

        return view('biblioteca.devoluciones.index', compact('prestamos'));
    }

    public function store(Request $request, BibPrestamo $prestamo): RedirectResponse
    {
        $validated = $request->validate([
            'fecha_devolucion' => ['required', 'date'],
            'detalles' => ['required', 'array'],
            'detalles.*.estado_fisico_retorno' => ['required', 'in:bueno,regular,malo,perdido'],
            'detalles.*.observaciones' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {
            $todosDevueltos = true;
            $diasAtraso = 0;

            if (Carbon::parse($validated['fecha_devolucion'])->greaterThan($prestamo->fecha_limite)) {
                $diasAtraso = (int) Carbon::parse($validated['fecha_devolucion'])->diffInDays($prestamo->fecha_limite);
            }

            foreach ($validated['detalles'] as $detalleId => $data) {
                $detalle = BibDetallePrestamo::lockForUpdate()->findOrFail($detalleId);

                if ($detalle->estado_devolucion !== 'pendiente') {
                    continue;
                }

                $detalle->update([
                    'estado_devolucion' => $data['estado_fisico_retorno'] === 'perdido' ? 'perdido' : 'devuelto',
                    'estado_fisico_retorno' => $data['estado_fisico_retorno'],
                    'observaciones' => $data['observaciones'] ?? null,
                ]);

                $nuevoEstado = $data['estado_fisico_retorno'] === 'perdido' ? 'baja' : 'disponible';
                $detalle->ejemplar->update(['estado_disponibilidad' => $nuevoEstado]);

                if ($data['estado_fisico_retorno'] === 'perdido') {
                    $todosDevueltos = false;
                }
            }

            $pendientes = $prestamo->detalles()->where('estado_devolucion', 'pendiente')->count();

            $prestamo->update([
                'fecha_devolucion' => $validated['fecha_devolucion'],
                'hora_devolucion' => Carbon::now()->format('H:i:s'),
                'id_usuario_devolucion' => auth()->id(),
                'estado' => $pendientes === 0 ? 'devuelto' : 'activo',
            ]);

            if ($diasAtraso > 0) {
                BibMulta::create([
                    'id_prestamo' => $prestamo->id,
                    'id_socio' => $prestamo->id_socio,
                    'tipo_multa' => 'mora',
                    'dias_atraso' => $diasAtraso,
                    'monto' => $diasAtraso * 1.00,
                    'estado' => 'pendiente',
                    'fecha_multa' => Carbon::today(),
                    'id_usuario' => auth()->id(),
                ]);
            }

            DB::commit();

            return redirect()->route('biblioteca.devoluciones.index')->with('success', 'Devolución registrada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('biblioteca.devoluciones.index')->with('error', $e->getMessage());
        }
    }
}
