<?php

namespace App\Http\Controllers;

use App\Models\AnioEscolar;
use App\Models\AuditoriaLog;
use App\Models\DetalleEntrega;
use App\Models\Entrega;
use App\Models\Estudiante;
use App\Models\InventarioMovimiento;
use App\Models\Libro;
use App\Models\PadreApoderado;
use App\Services\ConstanciaEntregaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EntregaController extends Controller
{
    public function __construct(private ConstanciaEntregaService $constanciaService) {}

    public function index(Request $request): View
    {
        $query = Entrega::with(['estudiante', 'padre', 'anioEscolar', 'usuarioRegistro', 'detalles.libro']);
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo_general', 'like', "%{$buscar}%")
                    ->orWhereHas('estudiante', fn ($sq) => $sq->where('apellidos', 'like', "%{$buscar}%")
                        ->orWhere('nombres', 'like', "%{$buscar}%")
                        ->orWhere('dni', 'like', "%{$buscar}%"));
            });
        }
        if ($request->filled('grado')) {
            $query->whereHas('estudiante', fn ($sq) => $sq->where('grado', $request->input('grado')));
        }
        if ($request->filled('seccion')) {
            $query->whereHas('estudiante', fn ($sq) => $sq->where('seccion', $request->input('seccion')));
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_entrega', '>=', $request->input('fecha_desde'));
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_entrega', '<=', $request->input('fecha_hasta'));
        }
        if ($request->filled('anio_escolar')) {
            $query->where('id_anio_escolar', $request->input('anio_escolar'));
        }

        $summaryQuery = (clone $query);
        $totalEstudiantes = Estudiante::activos()->count();
        $totalLibros = Libro::activos()->sum('cantidad_disponible');
        $totalEntregas = $summaryQuery->count();
        $librosEntregados = (clone $summaryQuery)->sum('total_libros');
        $estudiantesConEntrega = (clone $summaryQuery)->distinct('id_estudiante')->count('id_estudiante');
        $estudiantesPendientes = max(0, $totalEstudiantes - $estudiantesConEntrega);
        $avance = $totalEstudiantes === 0 ? 0 : (int) round(($estudiantesConEntrega / $totalEstudiantes) * 100);

        $entregasPorGrado = (clone $summaryQuery)->get()->groupBy(fn ($entrega) => $entrega->estudiante?->grado ? $entrega->estudiante->grado.'°' : 'Sin grado')->map->count();
        $gradoOptions = Estudiante::activos()->distinct('grado')->orderBy('grado')->pluck('grado');
        $seccionOptions = Estudiante::activos()->distinct('seccion')->orderBy('seccion')->pluck('seccion');
        $anioEscolarOptions = AnioEscolar::orderByDesc('anio')->pluck('anio', 'id');

        $entregas = $query->orderByDesc('fecha_entrega')->orderByDesc('id')->paginate(20)->withQueryString();

        $librosDisponibles = Libro::activos()->conStock()->orderBy('grado')->orderBy('area')->orderBy('nombre')->get(['id', 'codigo_libro', 'nombre', 'grado', 'nivel']);

        return view('entregas.index', compact(
            'entregas',
            'totalEstudiantes',
            'totalLibros',
            'totalEntregas',
            'librosEntregados',
            'estudiantesPendientes',
            'avance',
            'entregasPorGrado',
            'gradoOptions',
            'seccionOptions',
            'anioEscolarOptions',
            'librosDisponibles'
        ));
    }

    public function create(): View
    {
        $anioActual = AnioEscolar::actual();
        $estudiantes = Estudiante::activos()
            ->when($anioActual, fn ($q) => $q->where('id_anio_escolar', $anioActual->id))
            ->orderBy('apellidos')->get();
        $libros = Libro::activos()->conStock()
            ->when($anioActual, fn ($q) => $q->where('id_anio_escolar', $anioActual->id))
            ->orderBy('grado')->orderBy('area')->get();

        return view('entregas.create', compact('estudiantes', 'libros', 'anioActual'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'id_estudiante' => ['required', 'exists:estudiantes,id'],
            'tipo_firmante' => ['required', 'in:estudiante,apoderado'],
            'apoderado_dni' => ['required_if:tipo_firmante,apoderado', 'nullable', 'string', 'max:15'],
            'apoderado_nombres' => ['required_if:tipo_firmante,apoderado', 'nullable', 'string', 'max:255'],
            'apoderado_apellidos' => ['required_if:tipo_firmante,apoderado', 'nullable', 'string', 'max:255'],
            'apoderado_parentesco' => ['required_if:tipo_firmante,apoderado', 'nullable', 'string', 'max:100'],
            'apoderado_telefono' => ['nullable', 'string', 'max:20'],
            'libros' => ['required', 'array', 'min:1'],
            'libros.*' => ['required', 'exists:libros,id'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $anioActual = AnioEscolar::actual();
        if (! $anioActual) {
            return back()->with('error', config('messages.entregas.validacion.error'))->withInput();
        }

        $estudiante = Estudiante::findOrFail($request->input('id_estudiante'));

        // Verificar si ya tiene entrega en el anio actual
        $entregaExistente = Entrega::where('id_estudiante', $estudiante->id)
            ->where('id_anio_escolar', $anioActual->id)
            ->where('estado', 'completada')
            ->first();

        if ($entregaExistente) {
            return back()->with('warning', str_replace(':codigo', $entregaExistente->codigo_general, config('messages.entregas.validacion.warning')))->withInput();
        }

        DB::beginTransaction();
        try {
            $idPadre = null;
            if ($request->input('tipo_firmante') === 'apoderado') {
                $padre = PadreApoderado::firstOrCreate(
                    ['dni' => $request->input('apoderado_dni')],
                    [
                        'nombres' => $request->input('apoderado_nombres'),
                        'apellidos' => $request->input('apoderado_apellidos'),
                        'telefono' => $request->input('apoderado_telefono'),
                    ]
                );
                $idPadre = $padre->id;

                // Relacionar si no existe
                if (! $estudiante->padres()->where('id_padre', $padre->id)->exists()) {
                    $estudiante->padres()->attach($padre->id, [
                        'parentesco' => $request->input('apoderado_parentesco'),
                        'es_principal' => true,
                    ]);
                }
            }

            $entrega = Entrega::create([
                'codigo_general' => Entrega::generarCodigo($anioActual->anio),
                'id_estudiante' => $estudiante->id,
                'id_padre' => $idPadre,
                'id_anio_escolar' => $anioActual->id,
                'id_usuario_registro' => auth()->id(),
                'tipo_firmante' => $request->input('tipo_firmante'),
                'fecha_entrega' => now()->toDateString(),
                'hora_entrega' => now()->toTimeString(),
                'total_libros' => 0,
                'estado' => 'completada',
                'observaciones' => $request->input('observaciones'),
                'ip_registro' => $request->ip(),
            ]);

            $totalLibros = 0;
            $orden = 1;
            foreach ($request->input('libros') as $idLibro) {
                $libro = Libro::findOrFail($idLibro);
                $cantidad = 1; // 1 libro de cada materia

                if (! $libro->tieneStock($cantidad)) {
                    DB::rollBack();

                    return back()->with('error', str_replace(':titulo', $libro->nombre, config('messages.libros.stock.error')))->withInput();
                }

                DetalleEntrega::create([
                    'id_entrega' => $entrega->id,
                    'id_libro' => $libro->id,
                    'cantidad' => $cantidad,
                    'entregas' => 'bueno',
                    'numero_orden' => $orden++,
                ]);

                $libro->decrement('cantidad_disponible', $cantidad);
                $libro->increment('cantidad_entregada', $cantidad);

                InventarioMovimiento::create([
                    'id_libro' => $libro->id, 'tipo_movimiento' => 'entrega',
                    'cantidad' => -$cantidad,
                    'cantidad_anterior' => $libro->cantidad_disponible + $cantidad,
                    'cantidad_nueva' => $libro->cantidad_disponible,
                    'referencia_tipo' => 'entrega', 'referencia_id' => $entrega->id,
                    'id_usuario' => auth()->id(),
                ]);

                $totalLibros += $cantidad;
            }

            $entrega->update(['total_libros' => $totalLibros]);
            DB::commit();

            AuditoriaLog::registrar([
                'accion' => 'crear', 'modulo' => 'entregas', 'registro_id' => $entrega->id,
                'descripcion' => "Entrega {$entrega->codigo_general}: {$totalLibros} libros",
            ]);

            return redirect()->route('entregas.show', $entrega)
                ->with('success', str_replace([':codigo', ':total'], [$entrega->codigo_general, $totalLibros], config('messages.entregas.store.success')));
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', config('messages.entregas.store.error'))->withInput();
        }
    }

    public function show(Entrega $entrega): View
    {
        $entrega->load(['estudiante', 'anioEscolar', 'usuarioRegistro', 'detalles.libro', 'devolucion']);

        return view('entregas.show', compact('entrega'));
    }

    /**
     * API: Store entrega via AJAX (modal form).
     */
    public function storeApi(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'id_estudiante' => ['required', 'exists:estudiantes,id'],
                'tipo_firmante' => ['required', 'in:estudiante,apoderado'],
                'apoderado_dni' => ['required_if:tipo_firmante,apoderado', 'nullable', 'string', 'max:15'],
                'apoderado_nombres' => ['required_if:tipo_firmante,apoderado', 'nullable', 'string', 'max:255'],
                'apoderado_apellidos' => ['required_if:tipo_firmante,apoderado', 'nullable', 'string', 'max:255'],
                'apoderado_parentesco' => ['required_if:tipo_firmante,apoderado', 'nullable', 'string', 'max:100'],
                'apoderado_telefono' => ['nullable', 'string', 'max:20'],
                'libros' => ['required', 'array', 'min:1'],
                'libros.*' => ['required', 'exists:libros,id'],
                'observaciones' => ['nullable', 'string'],
            ]);

            $anioActual = AnioEscolar::actual();
            if (! $anioActual) {
                return response()->json(['success' => false, 'message' => config('messages.entregas.validacion.error')], 422);
            }

            $estudiante = Estudiante::findOrFail($request->input('id_estudiante'));

            $entregaExistente = Entrega::where('id_estudiante', $estudiante->id)
                ->where('id_anio_escolar', $anioActual->id)
                ->where('estado', 'completada')
                ->first();

            if ($entregaExistente) {
                return response()->json([
                    'success' => false,
                    'message' => str_replace(':codigo', $entregaExistente->codigo_general, config('messages.entregas.validacion.warning')),
                ], 422);
            }

            DB::beginTransaction();

            $idPadre = null;
            if ($request->input('tipo_firmante') === 'apoderado') {
                $padre = PadreApoderado::firstOrCreate(
                    ['dni' => $request->input('apoderado_dni')],
                    [
                        'nombres' => $request->input('apoderado_nombres'),
                        'apellidos' => $request->input('apoderado_apellidos'),
                        'telefono' => $request->input('apoderado_telefono'),
                    ]
                );
                $idPadre = $padre->id;

                if (! $estudiante->padres()->where('id_padre', $padre->id)->exists()) {
                    $estudiante->padres()->attach($padre->id, [
                        'parentesco' => $request->input('apoderado_parentesco'),
                        'es_principal' => true,
                    ]);
                }
            }

            $entrega = Entrega::create([
                'codigo_general' => Entrega::generarCodigo($anioActual->anio),
                'id_estudiante' => $estudiante->id,
                'id_padre' => $idPadre,
                'id_anio_escolar' => $anioActual->id,
                'id_usuario_registro' => auth()->id(),
                'tipo_firmante' => $request->input('tipo_firmante'),
                'fecha_entrega' => now()->toDateString(),
                'hora_entrega' => now()->toTimeString(),
                'total_libros' => 0,
                'estado' => 'completada',
                'observaciones' => $request->input('observaciones'),
                'ip_registro' => $request->ip(),
            ]);

            $totalLibros = 0;
            $orden = 1;
            foreach ($request->input('libros') as $idLibro) {
                $libro = Libro::findOrFail($idLibro);
                $cantidad = 1;

                if (! $libro->tieneStock($cantidad)) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => str_replace(':titulo', $libro->nombre, config('messages.libros.stock.error')),
                    ], 422);
                }

                DetalleEntrega::create([
                    'id_entrega' => $entrega->id,
                    'id_libro' => $libro->id,
                    'cantidad' => $cantidad,
                    'entregas' => 'bueno',
                    'numero_orden' => $orden++,
                ]);

                $libro->decrement('cantidad_disponible', $cantidad);
                $libro->increment('cantidad_entregada', $cantidad);

                InventarioMovimiento::create([
                    'id_libro' => $libro->id, 'tipo_movimiento' => 'entrega',
                    'cantidad' => -$cantidad,
                    'cantidad_anterior' => $libro->cantidad_disponible + $cantidad,
                    'cantidad_nueva' => $libro->cantidad_disponible,
                    'referencia_tipo' => 'entrega', 'referencia_id' => $entrega->id,
                    'id_usuario' => auth()->id(),
                ]);

                $totalLibros += $cantidad;
            }

            $entrega->update(['total_libros' => $totalLibros]);
            DB::commit();

            AuditoriaLog::registrar([
                'accion' => 'crear', 'modulo' => 'entregas', 'registro_id' => $entrega->id,
                'descripcion' => "Entrega {$entrega->codigo_general}: {$totalLibros} libros",
            ]);

            $entrega->load('estudiante');

            return response()->json([
                'success' => true,
                'message' => str_replace([':codigo', ':total'], [$entrega->codigo_general, $totalLibros], config('messages.entregas.store.success')),
                'entrega' => $entrega,
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
                'message' => config('messages.entregas.store.error'),
            ], 500);
        }
    }

    public function edit(Entrega $entrega): View
    {
        $entrega->load(['estudiante', 'anioEscolar', 'detalles.libro']);

        return view('entregas.edit', compact('entrega'));
    }

    public function update(Request $request, Entrega $entrega): RedirectResponse
    {
        $request->validate([
            'estado' => ['required', 'in:completada,parcial,anulada'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $entrega->update($request->only('estado', 'observaciones'));

        return redirect()->route('entregas.show', $entrega)
            ->with('success', str_replace(':codigo', $entrega->codigo_general, config('messages.entregas.update.success')));
    }

    /**
     * API: Update entrega via AJAX (modal form).
     */
    public function updateApi(Request $request, Entrega $entrega): JsonResponse
    {
        try {
            $request->validate([
                'estado' => ['required', 'in:completada,parcial,anulada'],
                'observaciones' => ['nullable', 'string'],
                'libros' => ['required', 'array', 'min:1'],
                'libros.*' => ['required', 'integer', 'exists:libros,id'],
            ]);

            DB::beginTransaction();

            $entrega->update($request->only('estado', 'observaciones'));

            $nuevosLibros = array_unique(array_map('intval', $request->input('libros')));
            $detallesActuales = $entrega->detalles()->with('libro', 'detalleDevolucion')->get();
            $librosActuales = $detallesActuales->keyBy('id_libro');
            $warnings = [];

            // Libros a eliminar (están en DB pero no en la nueva lista)
            foreach ($detallesActuales as $detalle) {
                if (! in_array($detalle->id_libro, $nuevosLibros)) {
                    if ($detalle->detalleDevolucion) {
                        $warnings[] = "No se puede quitar '{$detalle->libro->nombre}' porque ya tiene una devolución registrada.";

                        continue;
                    }

                    $libro = $detalle->libro;
                    $cantidad = $detalle->cantidad ?: 1;

                    $libro->increment('cantidad_disponible', $cantidad);
                    DB::table('libros')->where('id', $libro->id)->update([
                        'cantidad_entregada' => DB::raw('GREATEST(0, cantidad_entregada - '.(int) $cantidad.')'),
                    ]);

                    InventarioMovimiento::create([
                        'id_libro' => $libro->id,
                        'tipo_movimiento' => 'ajuste',
                        'cantidad' => $cantidad,
                        'cantidad_anterior' => $libro->cantidad_disponible - $cantidad,
                        'cantidad_nueva' => $libro->cantidad_disponible,
                        'referencia_tipo' => 'entrega_edicion',
                        'referencia_id' => $entrega->id,
                        'id_usuario' => auth()->id(),
                        'observaciones' => 'Edición de entrega: libro quitado',
                    ]);

                    $detalle->delete();
                }
            }

            // Libros a agregar (están en la nueva lista pero no en DB)
            $orden = $detallesActuales->count();
            foreach ($nuevosLibros as $idLibro) {
                if (! $librosActuales->has($idLibro)) {
                    $libro = Libro::findOrFail($idLibro);
                    $cantidad = 1;

                    if (! $libro->tieneStock($cantidad)) {
                        DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' => str_replace(':titulo', $libro->nombre, config('messages.libros.stock.error')),
                        ], 422);
                    }

                    DetalleEntrega::create([
                        'id_entrega' => $entrega->id,
                        'id_libro' => $libro->id,
                        'cantidad' => $cantidad,
                        'estado_entrega' => 'entregado',
                        'numero_orden' => $orden++,
                    ]);

                    $libro->decrement('cantidad_disponible', $cantidad);
                    $libro->increment('cantidad_entregada', $cantidad);

                    InventarioMovimiento::create([
                        'id_libro' => $libro->id,
                        'tipo_movimiento' => 'entrega',
                        'cantidad' => -$cantidad,
                        'cantidad_anterior' => $libro->cantidad_disponible + $cantidad,
                        'cantidad_nueva' => $libro->cantidad_disponible,
                        'referencia_tipo' => 'entrega_edicion',
                        'referencia_id' => $entrega->id,
                        'id_usuario' => auth()->id(),
                        'observaciones' => 'Edición de entrega: libro agregado',
                    ]);
                }
            }

            $totalLibros = $entrega->detalles()->sum('cantidad');
            $entrega->update(['total_libros' => $totalLibros]);

            DB::commit();

            $message = str_replace(':codigo', $entrega->codigo_general, config('messages.entregas.update.success'));
            if (! empty($warnings)) {
                $message .= ' '.implode(' ', $warnings);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'entrega' => $entrega->load('detalles.libro'),
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
                'message' => config('messages.entregas.update.error'),
                'debug' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ver constancia de entrega en el navegador (stream).
     */
    public function verConstancia(Entrega $entrega): Response
    {
        return $this->constanciaService->stream($entrega);
    }

    /**
     * Descargar constancia de entrega en PDF.
     */
    public function constancia(Entrega $entrega): Response
    {
        return $this->constanciaService->download($entrega);
    }

    /**
     * API: Buscar estudiantes (para select dinámico).
     */
    public function buscarEstudiantes(Request $request): JsonResponse
    {
        $buscar = $request->input('q', '');
        $estudiantes = Estudiante::activos()
            ->where(function ($q) use ($buscar) {
                $q->where('apellidos', 'like', "%{$buscar}%")
                    ->orWhere('nombres', 'like', "%{$buscar}%")
                    ->orWhere('dni', 'like', "%{$buscar}%");
            })
            ->limit(20)->get(['id', 'codigo_estudiante', 'dni', 'nombres', 'apellidos', 'grado', 'seccion', 'nivel', 'id_anio_escolar']);

        $anioActual = AnioEscolar::actual();
        $estudiantes->map(function ($estudiante) use ($anioActual) {
            $estudiante->tiene_entrega = false;
            if ($anioActual) {
                $estudiante->tiene_entrega = Entrega::where('id_estudiante', $estudiante->id)
                    ->where('id_anio_escolar', $anioActual->id)
                    ->where('estado', 'completada')
                    ->exists();
            }

            return $estudiante;
        });

        return response()->json($estudiantes);
    }

    /**
     * API: Libros disponibles para un grado.
     */
    public function librosPorGrado(Request $request): JsonResponse
    {
        $grado = (int) $request->input('grado', 0);
        $nivel = $request->input('nivel', '');

        $libros = Libro::activos()->conStock()
            ->when($grado, fn ($q) => $q->where('grado', $grado))
            ->when($nivel, fn ($q) => $q->where('nivel', $nivel))
            ->orderBy('area')->get(['id', 'codigo_libro', 'nombre', 'area', 'grado', 'cantidad_disponible', 'tipo_material']);

        return response()->json($libros);
    }
}
