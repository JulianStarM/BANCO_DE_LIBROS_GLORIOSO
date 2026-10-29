<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibSocio;
use App\Models\Estudiante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SocioBibliotecaController extends Controller
{
    public function index(Request $request): View
    {
        $query = BibSocio::query()
            ->with('estudiante');

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%")
                    ->orWhere('codigo_socio', 'like', "%{$buscar}%")
                    ->orWhere('dni', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('tipo_socio')) {
            $query->where('tipo_socio', $request->input('tipo_socio'));
        }

        $socios = $query->orderBy('apellidos')->orderBy('nombres')->paginate(20)->withQueryString();

        $estudiantes = Estudiante::where('estado', 'activo')->orderBy('apellidos')->orderBy('nombres')->get();

        return view('biblioteca.socios.index', compact('socios', 'estudiantes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->reglas());

        $validated['codigo_socio'] ??= $this->generarCodigo();

        BibSocio::create($validated);

        return redirect()->route('biblioteca.socios.index')->with('success', 'Lector registrado.');
    }

    public function update(Request $request, BibSocio $socio): RedirectResponse
    {
        $validated = $request->validate($this->reglas($socio->id));

        $socio->update($validated);

        return redirect()->route('biblioteca.socios.index')->with('success', 'Lector actualizado.');
    }

    public function destroy(BibSocio $socio): RedirectResponse
    {
        if ($socio->prestamos()->exists()) {
            return redirect()->route('biblioteca.socios.index')->with('error', 'No se puede eliminar porque tiene préstamos asociados.');
        }

        $socio->delete();

        return redirect()->route('biblioteca.socios.index')->with('success', 'Lector eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function reglas(?int $id = null): array
    {
        return [
            'id_estudiante' => ['nullable', 'exists:estudiantes,id'],
            'id_usuario' => ['nullable', 'exists:users,id'],
            'tipo_socio' => ['required', 'in:estudiante,docente,administrativo,externo'],
            'codigo_socio' => ['nullable', 'string', 'max:20', $id ? "unique:bib_socios,codigo_socio,{$id}" : 'unique:bib_socios,codigo_socio'],
            'dni' => ['required', 'string', 'max:15', $id ? "unique:bib_socios,dni,{$id}" : 'unique:bib_socios,dni'],
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'grado' => ['nullable', 'integer', 'min:1'],
            'seccion' => ['nullable', 'string', 'max:5'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'estado' => ['required', 'in:activo,suspendido,inactivo'],
            'fecha_inscripcion' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
        ];
    }

    private function generarCodigo(): string
    {
        $ultimo = BibSocio::orderByDesc('id')->value('id') ?? 0;

        return 'SOC-'.str_pad((string) ($ultimo + 1), 5, '0', STR_PAD_LEFT);
    }
}
