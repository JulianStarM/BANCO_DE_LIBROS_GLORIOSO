<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibCatalogo;
use App\Models\BibEjemplar;
use App\Models\BibEstante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EjemplarBibliotecaController extends Controller
{
    public function index(Request $request): View
    {
        $query = BibEjemplar::query()
            ->with(['catalogo', 'estante.ubicacion']);

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo_ejemplar', 'like', "%{$buscar}%")
                    ->orWhereHas('catalogo', fn ($cq) => $cq->where('titulo', 'like', "%{$buscar}%"));
            });
        }

        if ($request->filled('estado_disponibilidad')) {
            $query->where('estado_disponibilidad', $request->input('estado_disponibilidad'));
        }

        if ($request->filled('id_estante')) {
            $query->where('id_estante', $request->input('id_estante'));
        }

        $ejemplares = $query->orderBy('codigo_ejemplar')->paginate(20)->withQueryString();

        $catalogos = BibCatalogo::where('estado', true)->orderBy('titulo')->get();
        $estantes = BibEstante::with('ubicacion')->where('estado', true)->orderBy('codigo_estante')->get();

        return view('biblioteca.ejemplares.index', compact('ejemplares', 'catalogos', 'estantes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_catalogo' => ['required', 'exists:bib_catalogo,id'],
            'id_estante' => ['nullable', 'exists:bib_estantes,id'],
            'codigo_ejemplar' => ['required', 'string', 'max:30', 'unique:bib_ejemplares,codigo_ejemplar'],
            'numero_registro' => ['required', 'integer', 'min:1', 'unique:bib_ejemplares,numero_registro'],
            'fecha_ingreso' => ['required', 'date'],
            'procedencia' => ['nullable', 'string', 'max:150'],
            'estado_fisico' => ['required', 'in:bueno,regular,malo,baja'],
            'estado_disponibilidad' => ['required', 'in:disponible,prestado,reservado,baja'],
            'precio_adquisicion' => ['nullable', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $validated['id_usuario_registro'] = auth()->id();

        BibEjemplar::create($validated);

        return redirect()->route('biblioteca.ejemplares.index')->with('success', 'Ejemplar registrado.');
    }

    public function update(Request $request, BibEjemplar $ejemplar): RedirectResponse
    {
        $validated = $request->validate([
            'id_catalogo' => ['required', 'exists:bib_catalogo,id'],
            'id_estante' => ['nullable', 'exists:bib_estantes,id'],
            'codigo_ejemplar' => ['required', 'string', 'max:30', "unique:bib_ejemplares,codigo_ejemplar,{$ejemplar->id}"],
            'numero_registro' => ['required', 'integer', 'min:1', "unique:bib_ejemplares,numero_registro,{$ejemplar->id}"],
            'fecha_ingreso' => ['required', 'date'],
            'procedencia' => ['nullable', 'string', 'max:150'],
            'estado_fisico' => ['required', 'in:bueno,regular,malo,baja'],
            'estado_disponibilidad' => ['required', 'in:disponible,prestado,reservado,baja'],
            'precio_adquisicion' => ['nullable', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $ejemplar->update($validated);

        return redirect()->route('biblioteca.ejemplares.index')->with('success', 'Ejemplar actualizado.');
    }

    public function destroy(BibEjemplar $ejemplar): RedirectResponse
    {
        if ($ejemplar->detallePrestamos()->exists()) {
            return redirect()->route('biblioteca.ejemplares.index')->with('error', 'No se puede eliminar porque tiene préstamos asociados.');
        }

        $ejemplar->delete();

        return redirect()->route('biblioteca.ejemplares.index')->with('success', 'Ejemplar eliminado.');
    }
}
