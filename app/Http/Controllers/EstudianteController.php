<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\Estudiante;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EstudianteController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $grado = $request->get('grado');
        $seccion = $request->get('seccion');
        $nivel = $request->get('nivel');

        $estudiantes = Estudiante::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nombres', 'like', "%{$q}%")
                        ->orWhere('apellidos', 'like', "%{$q}%")
                        ->orWhere('dni', 'like', "%{$q}%")
                        ->orWhere('codigo_estudiante', 'like', "%{$q}%");
                });
            })
            ->when($grado, fn ($q2) => $q2->where('grado', $grado))
            ->when($seccion, fn ($q2) => $q2->where('seccion', $seccion))
            ->when($nivel, fn ($q2) => $q2->where('nivel', $nivel))
            ->orderBy('apellidos')
            ->paginate(15)
            ->withQueryString();

        $configEst = [
            'niveles' => Configuracion::nombresActivosEstudiantes('niveles', ['inicial', 'primaria', 'secundaria']),
            'grados' => Configuracion::nombresActivosEstudiantes('grados', ['1', '2', '3', '4', '5', '6']),
            'secciones' => Configuracion::nombresActivosEstudiantes('secciones', ['A', 'B', 'C', 'D']),
            'turnos' => Configuracion::nombresActivosEstudiantes('turnos', ['mañana', 'tarde']),
            'campos_obligatorios' => Configuracion::obtener('estudiantes', 'campos_obligatorios', [
                'dni' => true,
                'nombres' => true,
                'apellidos' => true,
                'sexo' => true,
                'nivel' => true,
                'grado' => true,
                'seccion' => true,
            ]),
            'campos_visibles' => Configuracion::obtener('estudiantes', 'campos_visibles', [
                'codigo_estudiante' => true,
                'dni' => true,
                'nombres' => true,
                'apellidos' => true,
                'sexo' => true,
                'nivel' => true,
                'grado' => true,
                'seccion' => true,
                'turno' => true,
                'direccion' => true,
                'telefono' => true,
                'id_anio_escolar' => true,
                'estado' => true,
            ]),
        ];

        return view('estudiantes.index', compact(
            'estudiantes',
            'q',
            'grado',
            'seccion',
            'nivel',
            'configEst'
        ));
    }

    public function create()
    {
        return view('estudiantes.form', [
            'estudiante' => new Estudiante,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = $this->validateData($request);
            $data['id_institucion'] = 1;
            $data['estado'] = $request->has('estado') ? 'activo' : 'retirado';

            // Defaults para campos no enviados cuando están ocultos
            $data['id_anio_escolar'] ??= 1;
            $data['turno'] ??= null;
            $data['codigo_estudiante'] ??= null;

            Estudiante::create($data);

            return redirect()
                ->route('estudiantes.index')
                ->with('success', config('messages.estudiantes.store.success'));
        } catch (QueryException $e) {
            return redirect()
                ->route('estudiantes.index')
                ->with('error', config('messages.estudiantes.store.error'));
        } catch (\Exception $e) {
            return redirect()
                ->route('estudiantes.index')
                ->with('error', config('messages.estudiantes.store.error'));
        }
    }

    public function show(Estudiante $estudiante)
    {
        return view('estudiantes.show', compact('estudiante'));
    }

    public function edit(Estudiante $estudiante)
    {
        return view('estudiantes.form', compact('estudiante'));
    }

    public function update(Request $request, Estudiante $estudiante)
    {
        try {
            $data = $this->validateData($request, $estudiante->id);
            $data['estado'] = $request->has('estado') ? 'activo' : 'retirado';

            $estudiante->update($data);

            return redirect()
                ->route('estudiantes.index')
                ->with('success', config('messages.estudiantes.update.success'));
        } catch (QueryException $e) {
            return redirect()
                ->route('estudiantes.index')
                ->with('error', config('messages.estudiantes.update.error'));
        } catch (\Exception $e) {
            return redirect()
                ->route('estudiantes.index')
                ->with('error', config('messages.estudiantes.update.error'));
        }
    }

    public function destroy(Request $request, Estudiante $estudiante)
    {
        $forzar = $request->boolean('force');

        if ($forzar) {
            DB::beginTransaction();
            try {
                $this->eliminarEnCascada($estudiante);
                DB::commit();

                return redirect()
                    ->route('estudiantes.index')
                    ->with('success', config('messages.estudiantes.force_destroy.success'));
            } catch (\Exception $e) {
                DB::rollBack();

                return redirect()
                    ->route('estudiantes.index')
                    ->with('error', config('messages.estudiantes.force_destroy.error'));
            }
        }

        try {
            $estudiante->delete();

            return redirect()
                ->route('estudiantes.index')
                ->with('success', config('messages.estudiantes.destroy.success'));
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'foreign key constraint')) {
                $relaciones = $this->contarRelaciones($estudiante);
                $mensaje = config('messages.estudiantes.destroy.error')."\n\nRegistros vinculados:";
                foreach ($relaciones as $nombre => $cantidad) {
                    if ($cantidad > 0) {
                        $mensaje .= "\n• {$nombre}: {$cantidad}";
                    }
                }
                $mensaje .= "\n\n".config('messages.estudiantes.force_destroy.warning');

                return redirect()
                    ->route('estudiantes.index')
                    ->with('error', $mensaje)
                    ->with('estudiante_para_forzar', $estudiante->id);
            }

            throw $e;
        }
    }

    /**
     * Elimina un estudiante y todas sus relaciones en cascada.
     */
    private function eliminarEnCascada(Estudiante $estudiante): void
    {
        // 1. Eliminar detalles de devoluciones y devoluciones
        foreach ($estudiante->devoluciones as $devolucion) {
            $devolucion->detalles()->delete();
            $devolucion->delete();
        }

        // 2. Eliminar detalles de entregas y entregas
        foreach ($estudiante->entregas as $entrega) {
            $entrega->detalles()->delete();
            $entrega->delete();
        }

        // 3. Eliminar observaciones y reposiciones
        $estudiante->observacionesGenerales()->delete();
        $estudiante->reposiciones()->delete();

        // 4. Desvincular padres/apoderados
        $estudiante->padres()->detach();

        // 5. Eliminar estudiante
        $estudiante->delete();
    }

    /**
     * Cuenta las relaciones de un estudiante para mostrar en el mensaje de error.
     *
     * @return array<string, int>
     */
    private function contarRelaciones(Estudiante $estudiante): array
    {
        return [
            'Entregas' => $estudiante->entregas()->count(),
            'Devoluciones' => $estudiante->devoluciones()->count(),
            'Observaciones' => $estudiante->observacionesGenerales()->count(),
            'Reposiciones' => $estudiante->reposiciones()->count(),
            'Apoderados vinculados' => $estudiante->padres()->count(),
        ];
    }

    /**
     * API: Store estudiante via AJAX (modal form).
     */
    public function storeApi(Request $request): JsonResponse
    {
        try {
            $data = $this->validateData($request);
            $data['id_institucion'] = 1;
            $data['estado'] = $request->has('estado') ? 'activo' : 'retirado';

            // Defaults para campos no enviados cuando están ocultos
            $data['id_anio_escolar'] ??= 1;
            $data['turno'] ??= null;
            $data['codigo_estudiante'] ??= null;

            $estudiante = Estudiante::create($data);
            session()->flash('success', config('messages.estudiantes.store.success'));

            return response()->json([
                'success' => true,
                'message' => config('messages.estudiantes.store.success'),
                'estudiante' => $estudiante,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => config('messages.sistema.validation.error'),
                'errors' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            $message = $e->getMessage();

            if (str_contains($message, 'Duplicate entry')) {
                $cleanMessage = config('messages.estudiantes.store.error');
            } elseif (str_contains($message, 'Data truncated') || str_contains($message, 'Incorrect')) {
                $cleanMessage = config('messages.sistema.validation.error');
            } else {
                $cleanMessage = config('messages.sistema.generic_error');
            }

            return response()->json([
                'success' => false,
                'message' => $cleanMessage,
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => config('messages.sistema.generic_error'),
            ], 500);
        }
    }

    /**
     * API: Buscar estudiantes (for autocomplete/search).
     */
    public function buscarApi(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $estudiantes = Estudiante::query()
            ->where(function ($w) use ($q) {
                $w->where('nombres', 'like', "%{$q}%")
                    ->orWhere('apellidos', 'like', "%{$q}%")
                    ->orWhere('dni', 'like', "%{$q}%")
                    ->orWhere('codigo_estudiante', 'like', "%{$q}%");
            })
            ->orderBy('apellidos')
            ->limit(20)
            ->get(['id', 'codigo_estudiante', 'dni', 'nombres', 'apellidos', 'grado', 'seccion', 'nivel', 'id_anio_escolar', 'estado']);

        return response()->json($estudiantes);
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        $obligatorios = Configuracion::obtener('estudiantes', 'campos_obligatorios', [
            'dni' => true,
            'nombres' => true,
            'apellidos' => true,
            'sexo' => true,
            'nivel' => true,
            'grado' => true,
            'seccion' => true,
            'id_anio_escolar' => false,
        ]);

        $visibles = Configuracion::obtener('estudiantes', 'campos_visibles', [
            'dni' => true,
            'nombres' => true,
            'apellidos' => true,
            'sexo' => true,
            'nivel' => true,
            'grado' => true,
            'seccion' => true,
            'turno' => true,
            'direccion' => true,
            'telefono' => true,
            'codigo_estudiante' => true,
            'id_anio_escolar' => true,
        ]);

        // Si un campo no es visible, nunca debe ser requerido (el usuario no puede llenarlo)
        $req = fn (string $campo) => ($visibles[$campo] ?? true) && ($obligatorios[$campo] ?? false) ? 'required' : 'nullable';

        return $request->validate([
            'codigo_estudiante' => [
                $req('codigo_estudiante'),
                'string',
                'max:20',
                Rule::unique('estudiantes', 'codigo_estudiante')->ignore($id),
            ],

            'dni' => [
                $req('dni'),
                'string',
                'max:15',
                Rule::unique('estudiantes', 'dni')->ignore($id),
            ],

            'nombres' => [$req('nombres'), 'string', 'max:100'],
            'apellidos' => [$req('apellidos'), 'string', 'max:100'],

            'sexo' => [$req('sexo'), 'in:M,F'],

            'grado' => [$req('grado'), 'integer'],

            'seccion' => [$req('seccion'), 'string', 'max:5'],

            'nivel' => [$req('nivel'), 'in:inicial,primaria,secundaria'],

            'turno' => [$req('turno'), 'in:mañana,tarde,noche'],

            'direccion' => [$req('direccion'), 'string', 'max:255'],
            'telefono' => [$req('telefono'), 'string', 'max:20'],

            'id_anio_escolar' => [$req('id_anio_escolar'), 'integer'],
        ]);
    }

    public function buscarDni(Request $request)
    {
        $request->validate(['dni' => 'required|string|size:8']);
        $dni = $request->dni;

        // Intentar usar el API gratuito de apis.net.pe v1
        $response = Http::withoutVerifying()->get("https://api.apis.net.pe/v1/dni?numero={$dni}");

        if ($response->successful() && ! isset($response['error'])) {
            return response()->json([
                'success' => true,
                'nombres' => $response['nombres'] ?? '',
                'apellidos' => trim(($response['apellidoPaterno'] ?? '').' '.($response['apellidoMaterno'] ?? '')),
            ]);
        }

        // Intento con otro endpoint en caso el primero falle
        $response2 = Http::withoutVerifying()->get("https://dniruc.apisperu.com/api/v1/dni/{$dni}?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InZhbGxlc21hcnRpbjkwQGdtYWlsLmNvbSJ9...."); // Placeholder if they want to use one, but let's just return error for now.

        return response()->json(['success' => false, 'message' => 'No se encontró el DNI o la API está inactiva'], 404);
    }
}
