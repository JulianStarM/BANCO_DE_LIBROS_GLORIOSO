<?php

namespace App\Http\Controllers;

use App\Models\AnioEscolar;
use App\Models\AuditoriaLog;
use App\Models\InventarioMovimiento;
use App\Models\Libro;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $areas = Libro::select('area')->distinct()->orderBy('area')->pluck('area');
        $aniosEscolares = AnioEscolar::orderByDesc('anio')->get();

        return view('libros.index', compact('libros', 'areas', 'aniosEscolares'));
    }

    public function create(): View
    {
        $aniosEscolares = AnioEscolar::orderByDesc('anio')->get();

        return view('libros.create', compact('aniosEscolares'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo_libro' => ['required', 'string', 'max:30', 'unique:libros,codigo_libro'],
            'nombre' => ['required', 'string', 'max:255'],
            'tipo_material' => ['required', 'in:libro,cuaderno_trabajo,guia_docente,material_complementario'],
            'area' => ['required', 'string', 'max:100'],
            'grado' => ['required', 'integer', 'min:1', 'max:6'],
            'nivel' => ['required', 'in:inicial,primaria,secundaria'],
            'editorial' => ['nullable', 'string', 'max:150'],
            'anio_edicion' => ['nullable', 'integer'],
            'id_anio_escolar' => ['nullable', 'exists:anios_escolares,id'],
            'cantidad_total' => ['required', 'integer', 'min:0'],
            'observaciones' => ['nullable', 'string'],
        ]);
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
            $validated = $request->validate([
                'codigo_libro' => ['required', 'string', 'max:30', 'unique:libros,codigo_libro'],
                'nombre' => ['required', 'string', 'max:255'],
                'tipo_material' => ['required', 'in:libro,cuaderno_trabajo,guia_docente,material_complementario'],
                'area' => ['required', 'string', 'max:100'],
                'grado' => ['required', 'integer', 'min:1', 'max:6'],
                'nivel' => ['required', 'in:inicial,primaria,secundaria'],
                'editorial' => ['nullable', 'string', 'max:150'],
                'anio_edicion' => ['nullable', 'integer'],
                'id_anio_escolar' => ['nullable', 'exists:anios_escolares,id'],
                'cantidad_total' => ['required', 'integer', 'min:0'],
                'observaciones' => ['nullable', 'string'],
            ]);
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

            return response()->json([
                'success' => true,
                'message' => str_replace('"":titulo""', '"'.$libro->nombre.'"', config('messages.libros.store.success')),
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

        return view('libros.edit', compact('libro', 'aniosEscolares'));
    }

    public function update(Request $request, Libro $libro): RedirectResponse
    {
        $validated = $request->validate([
            'codigo_libro' => ['required', 'string', 'max:30', "unique:libros,codigo_libro,{$libro->id}"],
            'nombre' => ['required', 'string', 'max:255'],
            'tipo_material' => ['required', 'in:libro,cuaderno_trabajo,guia_docente,material_complementario'],
            'area' => ['required', 'string', 'max:100'],
            'grado' => ['required', 'integer', 'min:1', 'max:6'],
            'nivel' => ['required', 'in:inicial,primaria,secundaria'],
            'editorial' => ['nullable', 'string', 'max:150'],
            'cantidad_total' => ['required', 'integer', 'min:0'],
            'estado' => ['required', 'in:activo,agotado,descontinuado,en_revision'],
            'observaciones' => ['nullable', 'string'],
        ]);
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

    public function destroy(Libro $libro): RedirectResponse
    {
        $libro->update(['estado' => 'descontinuado']);
        AuditoriaLog::registrar([
            'accion' => 'desactivar', 'modulo' => 'libros', 'registro_id' => $libro->id,
            'descripcion' => "Libro descontinuado: {$libro->nombre}",
        ]);

        return redirect()->route('libros.index')->with('warning', config('messages.libros.destroy.warning'));
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
}
