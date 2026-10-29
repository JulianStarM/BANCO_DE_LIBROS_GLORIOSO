<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibSocio;
use App\Models\BibVisita;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VisitaBibliotecaController extends Controller
{
    public function index(Request $request): View
    {
        $query = BibVisita::query()->with('socio');

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")
                    ->orWhereHas('socio', function ($sq) use ($buscar) {
                        $sq->where('nombres', 'like', "%{$buscar}%")
                            ->orWhere('apellidos', 'like', "%{$buscar}%");
                    });
            });
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->input('fecha'));
        }

        $visitas = $query->orderByDesc('fecha')->orderByDesc('hora_entrada')->paginate(20)->withQueryString();
        $socios = BibSocio::where('estado', 'activo')->orderBy('apellidos')->get();

        return view('biblioteca.visitas.index', compact('visitas', 'socios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_socio' => ['nullable', 'exists:bib_socios,id'],
            'tipo_visitante' => ['required', 'in:estudiante,docente,administrativo,externo'],
            'nombres' => ['nullable', 'string', 'max:200'],
            'grado' => ['nullable', 'integer', 'min:1'],
            'seccion' => ['nullable', 'string', 'max:5'],
            'motivo' => ['required', 'in:lectura,investigacion,prestamo,devolucion,otro'],
            'fecha' => ['required', 'date'],
            'hora_entrada' => ['required'],
            'hora_salida' => ['nullable'],
        ]);

        $validated['id_usuario'] = auth()->id();

        BibVisita::create($validated);

        return redirect()->route('biblioteca.visitas.index')->with('success', 'Visita registrada.');
    }

    public function update(Request $request, BibVisita $visita): RedirectResponse
    {
        $validated = $request->validate([
            'id_socio' => ['nullable', 'exists:bib_socios,id'],
            'tipo_visitante' => ['required', 'in:estudiante,docente,administrativo,externo'],
            'nombres' => ['nullable', 'string', 'max:200'],
            'grado' => ['nullable', 'integer', 'min:1'],
            'seccion' => ['nullable', 'string', 'max:5'],
            'motivo' => ['required', 'in:lectura,investigacion,prestamo,devolucion,otro'],
            'fecha' => ['required', 'date'],
            'hora_entrada' => ['required'],
            'hora_salida' => ['nullable'],
        ]);

        $visita->update($validated);

        return redirect()->route('biblioteca.visitas.index')->with('success', 'Visita actualizada.');
    }

    public function destroy(BibVisita $visita): RedirectResponse
    {
        $visita->delete();

        return redirect()->route('biblioteca.visitas.index')->with('success', 'Visita eliminada.');
    }
}
