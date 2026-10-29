<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AreaBibliotecaController extends Controller
{
    public function index(Request $request): View
    {
        $areas = BibArea::query()
            ->when($request->filled('buscar'), fn ($q) => $q->where('nombre', 'like', "%{$request->input('buscar')}%"))
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return view('biblioteca.areas.index', compact('areas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150', 'unique:bib_areas,nombre'],
            'codigo' => ['nullable', 'string', 'max:20'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'boolean'],
        ]);

        BibArea::create($validated);

        return redirect()->route('biblioteca.areas.index')->with('success', 'Área temática registrada.');
    }

    public function update(Request $request, BibArea $area): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150', "unique:bib_areas,nombre,{$area->id}"],
            'codigo' => ['nullable', 'string', 'max:20'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'boolean'],
        ]);

        $area->update($validated);

        return redirect()->route('biblioteca.areas.index')->with('success', 'Área temática actualizada.');
    }

    public function destroy(BibArea $area): RedirectResponse
    {
        if ($area->catalogos()->exists()) {
            return redirect()->route('biblioteca.areas.index')->with('error', 'No se puede eliminar porque tiene títulos asociados.');
        }

        $area->delete();

        return redirect()->route('biblioteca.areas.index')->with('success', 'Área temática eliminada.');
    }
}
