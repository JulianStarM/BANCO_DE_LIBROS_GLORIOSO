<?php

namespace App\Http\Controllers;

use App\Models\AnioEscolar;
use App\Models\AuditoriaLog;
use App\Models\DetalleDevolucion;
use App\Models\Devolucion;
use App\Models\Entrega;
use App\Models\InventarioMovimiento;
use App\Models\Libro;
use App\Services\ConstanciaDevolucionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DevolucionController extends Controller
{
    public function __construct(private ConstanciaDevolucionService $constanciaService) {}

    public function index(Request $request): View
    {
        $query = Devolucion::with(['estudiante', 'entrega', 'padre', 'usuarioRegistro', 'anioEscolar', 'detalles.libro']);
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo_general', 'like', "%{$buscar}%")
                    ->orWhereHas('estudiante', fn ($sq) => $sq->where('apellidos', 'like', "%{$buscar}%")
                        ->orWhere('nombres', 'like', "%{$buscar}%")
                        ->orWhere('dni', 'like', "%{$buscar}%")
                        ->orWhere('codigo_estudiante', 'like', "%{$buscar}%"));
            });
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }
        if ($request->filled('anio_escolar')) {
            $query->where('id_anio_escolar', $request->input('anio_escolar'));
        }

        $devoluciones = $query->orderByDesc('fecha_devolucion')->paginate(20)->withQueryString();
        $anioEscolarOptions = AnioEscolar::orderByDesc('anio')->pluck('anio', 'id');

        return view('devoluciones.index', compact('devoluciones', 'anioEscolarOptions'));
    }

    public function create(Request $request): View
    {
        $entrega = null;
        if ($request->filled('id_entrega')) {
            $entrega = Entrega::with(['estudiante', 'detalles.libro'])->findOrFail($request->input('id_entrega'));
        }
        $entregasPendientes = Entrega::with('estudiante')
            ->completadas()->conPendientes()
            ->orderByDesc('fecha_entrega')->get();

        return view('devoluciones.create', compact('entrega', 'entregasPendientes'));
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $devolucion = $this->guardarDevolucion($request);

            return redirect()->route('devoluciones.show', $devolucion)
                ->with('success', str_replace([':codigo', ':devueltos', ':deficientes', ':perdidos'], [$devolucion->codigo_general, $devolucion->total_devueltos, $devolucion->total_deficientes, $devolucion->total_perdidos], config('messages.devoluciones.store.success')));
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput()->with('error', 'Datos inválidos.');
        } catch (\Exception $e) {
            return back()->with('error', config('messages.devoluciones.store.error'))->withInput();
        }
    }

    /**
     * API: Get entregas pendientes para dropdown
     */
    public function pendientesApi(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        $query = Entrega::with(['estudiante', 'detalles.libro', 'detalles.devolucionesDetalle'])
            ->completadas()->conPendientes();

        if ($q) {
            $query->whereHas('estudiante', function ($w) use ($q) {
                $w->where('dni', 'like', "%{$q}%")
                    ->orWhere('nombres', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%");
            })->orWhere('codigo_general', 'like', "%{$q}%");
        }

        $entregas = $query->orderByDesc('fecha_entrega')->limit(20)->get();

        $entregas->each(function (Entrega $entrega) {
            $entrega->setRelation('detalles', $entrega->detalles->filter->estaPendiente()->values());
        });

        return response()->json($entregas);
    }

    /**
     * API: Store devolucion via AJAX (modal form)
     */
    public function storeApi(Request $request): JsonResponse
    {
        try {
            $devolucion = $this->guardarDevolucion($request);

            $mensaje = str_replace([':codigo', ':devueltos', ':deficientes', ':perdidos'], [$devolucion->codigo_general, $devolucion->total_devueltos, $devolucion->total_deficientes, $devolucion->total_perdidos], config('messages.devoluciones.store.success'));
            session()->flash('success', $mensaje);

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'devolucion' => $devolucion->load('estudiante'),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => config('messages.sistema.validation.error'),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => config('messages.devoluciones.store.error'),
            ], 500);
        }
    }

    public function edit(Devolucion $devolucion): View
    {
        $devolucion->load(['estudiante', 'entrega', 'detalles.libro', 'anioEscolar']);

        return view('devoluciones.edit', compact('devolucion'));
    }

    public function update(Request $request, Devolucion $devolucion): RedirectResponse
    {
        $request->validate([
            'estado' => ['required', 'in:completada,parcial,anulada'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $devolucion->update($request->only('estado', 'observaciones'));

        return redirect()->route('devoluciones.show', $devolucion)
            ->with('success', str_replace(':codigo', $devolucion->codigo_general, config('messages.devoluciones.update.success')));
    }

    /**
     * API: Update devolucion via AJAX (modal form).
     */
    public function updateApi(Request $request, Devolucion $devolucion): JsonResponse
    {
        try {
            $request->validate([
                'estado' => ['required', 'in:completada,parcial,anulada'],
                'observaciones' => ['nullable', 'string'],
                'detalles' => ['required', 'array', 'min:1'],
                'detalles.*.id' => ['required', 'exists:detalle_devoluciones,id'],
                'detalles.*.id_libro' => ['required', 'exists:libros,id'],
                'detalles.*.estado_libro' => ['required', 'in:bueno,deteriorado,deficiente,no_devuelto,perdido'],
                'detalles.*.observaciones' => ['nullable', 'string'],
            ]);

            DB::beginTransaction();

            $devolucion->update($request->only('estado', 'observaciones'));

            $totalDevueltos = 0;
            $totalNoDevueltos = 0;
            $totalDeficientes = 0;
            $totalPerdidos = 0;

            foreach ($request->input('detalles') as $detalleInput) {
                $detalle = DetalleDevolucion::with('libro')->findOrFail($detalleInput['id']);
                $libro = $detalle->libro;
                $estadoAnterior = $detalle->estado_libro;
                $estadoNuevo = $detalleInput['estado_libro'];

                if ($estadoAnterior !== $estadoNuevo) {
                    // Revertir efecto del estado anterior
                    $this->revertirEstadoDevolucion($libro, $estadoAnterior);
                    // Aplicar efecto del nuevo estado
                    $this->aplicarEstadoDevolucion($libro, $estadoNuevo);

                    InventarioMovimiento::create([
                        'id_libro' => $libro->id,
                        'tipo_movimiento' => 'ajuste',
                        'cantidad' => 0,
                        'cantidad_anterior' => $libro->cantidad_disponible,
                        'cantidad_nueva' => $libro->cantidad_disponible,
                        'referencia_tipo' => 'devolucion_edicion',
                        'referencia_id' => $devolucion->id,
                        'id_usuario' => auth()->id(),
                        'observaciones' => "Cambio estado: {$estadoAnterior} → {$estadoNuevo}",
                    ]);
                }

                $detalle->update([
                    'estado_libro' => $estadoNuevo,
                    'observaciones' => $detalleInput['observaciones'] ?? null,
                ]);

                // Contar para totales
                if (in_array($estadoNuevo, ['bueno', 'deteriorado'])) {
                    $totalDevueltos++;
                } elseif ($estadoNuevo === 'deficiente') {
                    $totalDeficientes++;
                } elseif ($estadoNuevo === 'perdido') {
                    $totalPerdidos++;
                } else {
                    $totalNoDevueltos++;
                }
            }

            $devolucion->update([
                'total_devueltos' => $totalDevueltos,
                'total_no_devueltos' => $totalNoDevueltos,
                'total_deficientes' => $totalDeficientes,
                'total_perdidos' => $totalPerdidos,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => str_replace(':codigo', $devolucion->codigo_general, config('messages.devoluciones.update.success')),
                'devolucion' => $devolucion->load('detalles.libro'),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => config('messages.sistema.validation.error'),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => config('messages.devoluciones.update.error'),
            ], 500);
        }
    }

    /**
     * Revertir el efecto de inventario de un estado de devolución anterior.
     */
    private function revertirEstadoDevolucion(Libro $libro, string $estado): void
    {
        if ($estado === 'bueno') {
            $libro->decrement('cantidad_disponible');
            $libro->decrement('cantidad_devuelta');
            $libro->increment('cantidad_entregada');
        } elseif ($estado === 'deteriorado') {
            $libro->decrement('cantidad_deteriorado');
            $libro->decrement('cantidad_devuelta');
            $libro->increment('cantidad_entregada');
        } elseif ($estado === 'deficiente') {
            $libro->decrement('cantidad_deficiente');
            $libro->increment('cantidad_entregada');
        } elseif ($estado === 'perdido') {
            $libro->decrement('cantidad_perdida');
            $libro->increment('cantidad_entregada');
        }
        // no_devuelto: no afecta inventario
    }

    /**
     * Aplicar el efecto de inventario de un nuevo estado de devolución.
     */
    private function aplicarEstadoDevolucion(Libro $libro, string $estado): void
    {
        if ($estado === 'bueno') {
            $libro->increment('cantidad_disponible');
            $libro->increment('cantidad_devuelta');
            $libro->decrement('cantidad_entregada');
        } elseif ($estado === 'deteriorado') {
            $libro->increment('cantidad_deteriorado');
            $libro->increment('cantidad_devuelta');
            $libro->decrement('cantidad_entregada');
        } elseif ($estado === 'deficiente') {
            $libro->increment('cantidad_deficiente');
            $libro->increment('cantidad_devuelta');
            $libro->decrement('cantidad_entregada');
        } elseif ($estado === 'perdido') {
            $libro->increment('cantidad_perdida');
            $libro->increment('cantidad_devuelta');
            $libro->decrement('cantidad_entregada');
        }
        // no_devuelto: no afecta inventario
    }

    public function show(Devolucion $devolucion): View
    {
        $devolucion->load(['estudiante', 'entrega.detalles.libro', 'detalles.libro', 'usuarioRegistro']);

        return view('devoluciones.show', compact('devolucion'));
    }

    /**
     * Ver constancia de devolución en el navegador (stream).
     */
    public function verConstancia(Devolucion $devolucion): Response
    {
        return $this->constanciaService->stream($devolucion);
    }

    /**
     * Descargar constancia de devolución en PDF.
     */
    public function constancia(Devolucion $devolucion): Response
    {
        return $this->constanciaService->download($devolucion);
    }

    /**
     * Guardar una devolución (web y API) respetando libros pendientes.
     */
    private function guardarDevolucion(Request $request): Devolucion
    {
        $datos = $request->validate([
            'id_entrega' => ['required', 'exists:entregas,id'],
            'tipo_firmante' => ['required', 'in:estudiante,apoderado'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.id_detalle_entrega' => ['required', 'exists:detalle_entregas,id'],
            'detalles.*.id_libro' => ['required', 'exists:libros,id'],
            'detalles.*.estado_libro' => ['required', 'in:bueno,deteriorado,deficiente,no_devuelto,perdido'],
            'detalles.*.observaciones' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $entrega = Entrega::with('detalles')->findOrFail($datos['id_entrega']);
        $pendientes = $entrega->detalles()->pendientes()->pluck('id')->toArray();

        foreach ($datos['detalles'] as $detalle) {
            if (! in_array($detalle['id_detalle_entrega'], $pendientes)) {
                throw ValidationException::withMessages([
                    'detalles' => 'Uno o más libros ya fueron devueltos o no pertenecen a la entrega seleccionada.',
                ]);
            }
        }

        $anioActual = AnioEscolar::actual();

        return DB::transaction(function () use ($request, $datos, $entrega, $anioActual) {
            $totalDevueltos = 0;
            $totalNoDevueltos = 0;
            $totalDeficientes = 0;
            $totalPerdidos = 0;

            $devolucion = Devolucion::create([
                'codigo_general' => Devolucion::generarCodigo($anioActual?->anio ?? date('Y')),
                'id_entrega' => $entrega->id,
                'id_estudiante' => $entrega->id_estudiante,
                'id_anio_escolar' => $entrega->id_anio_escolar,
                'id_usuario_registro' => auth()->id(),
                'tipo_firmante' => $datos['tipo_firmante'],
                'fecha_devolucion' => now()->toDateString(),
                'hora_devolucion' => now()->toTimeString(),
                'estado' => 'completada',
                'observaciones' => $datos['observaciones'] ?? null,
                'ip_registro' => $request->ip(),
            ]);

            foreach ($datos['detalles'] as $detalle) {
                DetalleDevolucion::create([
                    'id_devolucion' => $devolucion->id,
                    'id_libro' => $detalle['id_libro'],
                    'id_detalle_entrega' => $detalle['id_detalle_entrega'],
                    'estado_libro' => $detalle['estado_libro'],
                    'observaciones' => $detalle['observaciones'] ?? null,
                ]);

                $libro = Libro::findOrFail($detalle['id_libro']);
                $estado = $detalle['estado_libro'];
                $cantidadAnterior = $libro->cantidad_disponible;

                $this->aplicarEstadoDevolucion($libro, $estado);
                $this->registrarMovimientoDevolucion($libro, $estado, $devolucion, $cantidadAnterior);

                switch ($estado) {
                    case 'bueno':
                    case 'deteriorado':
                        $totalDevueltos++;
                        break;
                    case 'deficiente':
                        $totalDeficientes++;
                        break;
                    case 'perdido':
                        $totalPerdidos++;
                        break;
                    case 'no_devuelto':
                        $totalNoDevueltos++;
                        break;
                }
            }

            $estadoDevolucion = $entrega->detalles()->pendientes()->exists() ? 'parcial' : 'completada';

            $devolucion->update([
                'total_devueltos' => $totalDevueltos,
                'total_no_devueltos' => $totalNoDevueltos,
                'total_deficientes' => $totalDeficientes,
                'total_perdidos' => $totalPerdidos,
                'estado' => $estadoDevolucion,
            ]);

            AuditoriaLog::registrar([
                'accion' => 'crear', 'modulo' => 'devoluciones', 'registro_id' => $devolucion->id,
                'descripcion' => "Devolución {$devolucion->codigo_general} registrada",
            ]);

            return $devolucion;
        });
    }

    /**
     * Registrar movimiento de inventario para una devolución.
     */
    private function registrarMovimientoDevolucion(Libro $libro, string $estado, Devolucion $devolucion, int $cantidadAnterior): void
    {
        InventarioMovimiento::create([
            'id_libro' => $libro->id,
            'tipo_movimiento' => $estado === 'perdido' ? 'baja' : 'devolucion',
            'cantidad' => $estado === 'bueno' ? 1 : 0,
            'cantidad_anterior' => $cantidadAnterior,
            'cantidad_nueva' => $libro->cantidad_disponible,
            'referencia_tipo' => 'devolucion',
            'referencia_id' => $devolucion->id,
            'id_usuario' => auth()->id(),
            'observaciones' => "Estado: {$estado}",
        ]);
    }
}
