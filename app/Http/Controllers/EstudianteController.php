<?php

namespace App\Http\Controllers;

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

        return view('estudiantes.index', compact(
            'estudiantes',
            'q',
            'grado',
            'seccion',
            'nivel'
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
            $data['id_institucion'] = 1; // Valor por defecto
            $data['estado'] = $request->has('estado') ? 'activo' : 'retirado';

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

            // Default turno si viene vacío
            if (empty($data['turno'])) {
                $data['turno'] = 'mañana';
            }

            $estudiante = Estudiante::create($data);

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
        return $request->validate([
            'codigo_estudiante' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('estudiantes', 'codigo_estudiante')->ignore($id),
            ],

            'dni' => [
                'required',
                'string',
                'max:15',
                Rule::unique('estudiantes', 'dni')->ignore($id),
            ],

            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],

            'sexo' => ['required', 'in:M,F'],

            // TU BD: tinyint
            'grado' => ['required', 'integer'],

            'seccion' => ['required', 'string', 'max:5'],

            // IMPORTANTE: minúsculas según tu BD
            'nivel' => ['required', 'in:inicial,primaria,secundaria'],

            // IMPORTANTE: enum minúsculas según tu BD
            'turno' => ['nullable', 'in:mañana,tarde,noche'],

            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],

            'id_anio_escolar' => ['required', 'integer'],
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
