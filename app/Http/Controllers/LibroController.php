<?php

namespace App\Http\Controllers;

use App\Models\AnioEscolar;
use App\Models\AuditoriaLog;
use App\Models\Configuracion;
use App\Models\DetalleDevolucion;
use App\Models\DetalleEntrega;
use App\Models\Devolucion;
use App\Models\Entrega;
use App\Models\InventarioMovimiento;
use App\Models\Libro;
use App\Models\ReposicionLibro;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LibroController extends Controller
{
    public function index(Request $request): View
    {
        $query = Libro::with(['anioEscolar']);
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('codigo_libro', 'like', "%{$buscar}%")
                    ->orWhere('area', 'like', "%{$buscar}%");
            });
        }
        if ($request->filled('grado')) {
            $query->deGrado((int) $request->input('grado'));
        }
        if ($request->filled('nivel')) {
            $query->deNivel($request->input('nivel'));
        }
        if ($request->filled('area')) {
            $query->where('area', $request->input('area'));
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        $libros = $query->orderBy('grado')->orderBy('area')->paginate(20)->withQueryString();
        $aniosEscolares = AnioEscolar::orderByDesc('anio')->get();

        return view('libros.index', array_merge(
            compact('libros', 'aniosEscolares'),
            $this->listasConfiguracionLibros()
        ));
    }

    public function create(): View
    {
        $aniosEscolares = AnioEscolar::orderByDesc('anio')->get();

        return view('libros.create', array_merge(
            compact('aniosEscolares'),
            $this->listasConfiguracionLibros()
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['codigo_libro' => $request->input('codigo_libro') ?: null]);

        $config = $this->listasConfiguracionLibros();

        $this->normalizarInputsConfig($request, $config);

        $validated = $request->validate([
            'codigo_libro' => ['nullable', 'string', 'max:30', 'unique:libros,codigo_libro'],
            'nombre' => ['required', 'string', 'max:255'],
            'tipo_material' => ['required', 'in:'.implode(',', $config['tiposMaterial'])],
            'area' => ['required', 'in:'.implode(',', $config['areas'])],
            'grado' => ['required', 'in:'.implode(',', $config['grados'])],
            'nivel' => ['required', 'in:'.implode(',', $config['niveles'])],
            'editorial' => ['nullable', 'in:'.implode(',', $config['editoriales'])],
            'anio_edicion' => ['nullable', 'integer'],
            'id_anio_escolar' => ['nullable', 'exists:anios_escolares,id'],
            'cantidad_total' => ['required', 'integer', 'min:0'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $validated['grado'] = (int) $validated['grado'];
        $validated['codigo_libro'] ??= Libro::generarCodigo($validated['nombre']);
        $validated['cantidad_disponible'] = $validated['cantidad_total'];
        $libro = Libro::create($validated);

        if ($validated['cantidad_total'] > 0) {
            InventarioMovimiento::create([
                'id_libro' => $libro->id, 'tipo_movimiento' => 'ingreso',
                'cantidad' => $validated['cantidad_total'], 'cantidad_anterior' => 0,
                'cantidad_nueva' => $validated['cantidad_total'],
                'referencia_tipo' => 'registro_inicial', 'id_usuario' => auth()->id(),
            ]);
        }

        AuditoriaLog::registrar([
            'accion' => 'crear', 'modulo' => 'libros', 'registro_id' => $libro->id,
            'descripcion' => "Libro registrado: {$libro->nombre}",
        ]);

        $msg = str_replace('"":titulo""', '"'.$libro->nombre.'"', config('messages.libros.store.success'));

        return redirect()->route('libros.index')->with('success', $msg);
    }

    /**
     * API: Store libro via AJAX (modal form).
     */
    public function storeApi(Request $request): JsonResponse
    {
        try {
            $request->merge(['codigo_libro' => $request->input('codigo_libro') ?: null]);

            $config = $this->listasConfiguracionLibros();

            $this->normalizarInputsConfig($request, $config);

            $validated = $request->validate([
                'codigo_libro' => ['nullable', 'string', 'max:30', 'unique:libros,codigo_libro'],
                'nombre' => ['required', 'string', 'max:255'],
                'tipo_material' => ['required', 'in:'.implode(',', $config['tiposMaterial'])],
                'area' => ['required', 'in:'.implode(',', $config['areas'])],
                'grado' => ['required', 'in:'.implode(',', $config['grados'])],
                'nivel' => ['required', 'in:'.implode(',', $config['niveles'])],
                'editorial' => ['nullable', 'in:'.implode(',', $config['editoriales'])],
                'anio_edicion' => ['nullable', 'integer'],
                'id_anio_escolar' => ['nullable', 'exists:anios_escolares,id'],
                'cantidad_total' => ['required', 'integer', 'min:0'],
                'observaciones' => ['nullable', 'string'],
            ]);

            $validated['grado'] = (int) $validated['grado'];
            $validated['codigo_libro'] ??= Libro::generarCodigo($validated['nombre']);
            $validated['cantidad_disponible'] = $validated['cantidad_total'];
            $libro = Libro::create($validated);

            if ($validated['cantidad_total'] > 0) {
                InventarioMovimiento::create([
                    'id_libro' => $libro->id, 'tipo_movimiento' => 'ingreso',
                    'cantidad' => $validated['cantidad_total'], 'cantidad_anterior' => 0,
                    'cantidad_nueva' => $validated['cantidad_total'],
                    'referencia_tipo' => 'registro_inicial', 'id_usuario' => auth()->id(),
                ]);
            }

            AuditoriaLog::registrar([
                'accion' => 'crear', 'modulo' => 'libros', 'registro_id' => $libro->id,
                'descripcion' => "Libro registrado: {$libro->nombre}",
            ]);

            $mensaje = str_replace('"":titulo""', '"'.$libro->nombre.'"', config('messages.libros.store.success'));
            session()->flash('success', $mensaje);

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'libro' => $libro,
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
                'message' => config('messages.libros.store.error'),
            ], 500);
        }
    }

    public function show(Libro $libro): View
    {
        $libro->load(['movimientos' => fn ($q) => $q->with('usuario')->orderByDesc('created_at')->limit(50)]);

        return view('libros.show', compact('libro'));
    }

    public function edit(Libro $libro): View
    {
        $aniosEscolares = AnioEscolar::orderByDesc('anio')->get();

        return view('libros.edit', array_merge(
            compact('libro', 'aniosEscolares'),
            $this->listasConfiguracionLibros()
        ));
    }

    public function update(Request $request, Libro $libro): RedirectResponse
    {
        $config = $this->listasConfiguracionLibros();

        $this->normalizarInputsConfig($request, $config);

        $validated = $request->validate([
            'codigo_libro' => ['required', 'string', 'max:30', "unique:libros,codigo_libro,{$libro->id}"],
            'nombre' => ['required', 'string', 'max:255'],
            'tipo_material' => ['required', 'in:'.implode(',', $config['tiposMaterial'])],
            'area' => ['required', 'in:'.implode(',', $config['areas'])],
            'grado' => ['required', 'in:'.implode(',', $config['grados'])],
            'nivel' => ['required', 'in:'.implode(',', $config['niveles'])],
            'editorial' => ['nullable', 'in:'.implode(',', $config['editoriales'])],
            'cantidad_total' => ['required', 'integer', 'min:0'],
            'estado' => ['required', 'in:activo,agotado,descontinuado,en_revision'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $validated['grado'] = (int) $validated['grado'];
        $diferencia = $validated['cantidad_total'] - $libro->cantidad_total;
        if ($diferencia !== 0) {
            $validated['cantidad_disponible'] = max(0, $libro->cantidad_disponible + $diferencia);
            InventarioMovimiento::create([
                'id_libro' => $libro->id, 'tipo_movimiento' => 'ajuste',
                'cantidad' => $diferencia, 'cantidad_anterior' => $libro->cantidad_disponible,
                'cantidad_nueva' => $validated['cantidad_disponible'],
                'referencia_tipo' => 'ajuste_manual', 'id_usuario' => auth()->id(),
            ]);
        }
        $libro->update($validated);
        AuditoriaLog::registrar([
            'accion' => 'actualizar', 'modulo' => 'libros', 'registro_id' => $libro->id,
            'descripcion' => "Libro actualizado: {$libro->nombre}",
        ]);

        return redirect()->route('libros.index')->with('success', config('messages.libros.update.success'));
    }

    public function destroy(Request $request, Libro $libro): RedirectResponse
    {
        $forzar = $request->boolean('force');

        if ($forzar) {
            DB::beginTransaction();
            try {
                $this->eliminarEnCascada($libro);
                DB::commit();

                AuditoriaLog::registrar([
                    'accion' => 'eliminar', 'modulo' => 'libros', 'registro_id' => $libro->id,
                    'descripcion' => "Libro eliminado en cascada: {$libro->nombre}",
                ]);

                return redirect()->route('libros.index')->with('success', config('messages.libros.force_destroy.success'));
            } catch (\Exception $e) {
                DB::rollBack();
                report($e);

                return redirect()->route('libros.index')->with('error', config('messages.libros.force_destroy.error'));
            }
        }

        try {
            $libro->delete();
            AuditoriaLog::registrar([
                'accion' => 'eliminar', 'modulo' => 'libros', 'registro_id' => $libro->id,
                'descripcion' => "Libro eliminado: {$libro->nombre}",
            ]);

            return redirect()->route('libros.index')->with('success', config('messages.libros.destroy.success'));
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                $relaciones = $this->contarRelaciones($libro);
                $mensaje = config('messages.libros.destroy.error')."\n\nRegistros vinculados:";
                foreach ($relaciones as $nombre => $cantidad) {
                    if ($cantidad > 0) {
                        $mensaje .= "\n• {$nombre}: {$cantidad}";
                    }
                }
                $mensaje .= "\n\n".config('messages.libros.force_destroy.warning');

                return redirect()->route('libros.index')
                    ->with('error', $mensaje)
                    ->with('libro_para_forzar', $libro->id);
            }

            throw $e;
        }
    }

    /**
     * Elimina un libro y todas sus relaciones en cascada.
     */
    private function eliminarEnCascada(Libro $libro): void
    {
        $devolucionIds = $libro->detalleDevoluciones()->distinct()->pluck('id_devolucion');
        DetalleDevolucion::where('id_libro', $libro->id)->delete();
        Devolucion::whereIn('id', $devolucionIds)->whereDoesntHave('detalles')->delete();

        $entregaIds = $libro->detalleEntregas()->distinct()->pluck('id_entrega');
        DetalleEntrega::where('id_libro', $libro->id)->delete();
        foreach (Entrega::whereIn('id', $entregaIds)->get() as $entrega) {
            if ($entrega->detalles()->count() === 0) {
                $entrega->delete();
            } else {
                $entrega->update(['total_libros' => $entrega->detalles()->sum('cantidad')]);
            }
        }

        $libro->movimientos()->delete();
        ReposicionLibro::where('id_libro', $libro->id)->delete();

        $libro->delete();
    }

    /**
     * Cuenta las relaciones de un libro para mostrar en el mensaje de error.
     *
     * @return array<string, int>
     */
    private function contarRelaciones(Libro $libro): array
    {
        return [
            'Entregas' => $libro->detalleEntregas()->distinct('id_entrega')->count('id_entrega'),
            'Devoluciones' => $libro->detalleDevoluciones()->distinct('id_devolucion')->count('id_devolucion'),
            'Movimientos de inventario' => $libro->movimientos()->count(),
            'Reposiciones' => $libro->reposiciones()->count(),
        ];
    }

    /**
     * API: Buscar libros por nombre o código (para autocompletado).
     */
    public function buscarLibros(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $libros = Libro::query()
            ->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%{$q}%")
                    ->orWhere('codigo_libro', 'like', "%{$q}%")
                    ->orWhere('area', 'like', "%{$q}%");
            })
            ->where('estado', 'activo')
            ->where('cantidad_disponible', '>', 0)
            ->orderBy('grado')
            ->orderBy('area')
            ->limit(20)
            ->get(['id', 'codigo_libro', 'nombre', 'area', 'grado', 'nivel', 'cantidad_disponible']);

        return response()->json($libros);
    }

    /**
     * API: Devuelve el siguiente código sugerido para un nombre de libro.
     */
    public function siguienteCodigo(Request $request): JsonResponse
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
        ]);

        return response()->json([
            'codigo' => Libro::generarCodigo($request->input('nombre')),
        ]);
    }

    /**
     * Devuelve el valor canónico de una lista de configuración comparando
     * sin distinguir mayúsculas, minúsculas ni tildes.
     */
    private function normalizarValorConfig(?string $valor, array $opciones): ?string
    {
        if ($valor === null) {
            return null;
        }

        $buscado = Str::lower(Str::ascii($valor));

        foreach ($opciones as $opcion) {
            if (Str::lower(Str::ascii($opcion)) === $buscado) {
                return $opcion;
            }
        }

        return $valor;
    }

    /**
     * Normaliza los valores de configuración antes de validar para que
     * importaciones con ortografía ligeramente distinta sean aceptadas.
     *
     * @param  array<string, array<int, string>>  $config
     */
    private function normalizarInputsConfig(Request $request, array $config): void
    {
        $request->merge([
            'area' => $this->normalizarValorConfig($request->input('area'), $config['areas']),
            'nivel' => $this->normalizarValorConfig($request->input('nivel'), $config['niveles']),
            'grado' => $this->normalizarValorConfig($request->input('grado'), $config['grados']),
            'tipo_material' => $this->normalizarValorConfig($request->input('tipo_material'), $config['tiposMaterial']),
            'editorial' => $this->normalizarValorConfig($request->input('editorial'), $config['editoriales']),
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function listasConfiguracionLibros(): array
    {
        return [
            'areas' => Configuracion::nombresActivos('libros', 'areas', [
                'MATEMÁTICA', 'COMUNICACIÓN', 'CIENCIA Y TECNOLOGÍA', 'PERSONAL SOCIAL',
                'EDUCACIÓN FÍSICA', 'ARTE Y CULTURA', 'INGLÉS',
            ]),
            'editoriales' => Configuracion::nombresActivos('libros', 'editoriales', [
                'Santillana', 'Norma', 'Editorial SM', 'Educando',
            ]),
            'tiposMaterial' => Configuracion::nombresActivos('libros', 'tipos_material', [
                'libro', 'cuaderno_trabajo', 'guia_docente', 'material_complementario',
            ]),
            'niveles' => Configuracion::nombresActivos('libros', 'niveles', ['inicial', 'primaria', 'secundaria']),
            'grados' => Configuracion::nombresActivos('libros', 'grados', ['1', '2', '3', '4', '5', '6']),
        ];
    }
}
