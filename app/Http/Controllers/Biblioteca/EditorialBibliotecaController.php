<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibEditorial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EditorialBibliotecaController extends Controller
{
    public function index(Request $request): View
    {
        $editoriales = BibEditorial::query()
            ->when($request->filled('buscar'), fn ($q) => $q->where('nombre', 'like', "%{$request->input('buscar')}%"))
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return view('biblioteca.editoriales.index', compact('editoriales'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:200', 'unique:bib_editoriales,nombre'],
            'pais' => ['nullable', 'string', 'max:100'],
            'estado' => ['required', 'boolean'],
        ]);

        BibEditorial::create($validated);

        return redirect()->route('biblioteca.editoriales.index')->with('success', 'Editorial registrada.');
    }

    public function update(Request $request, BibEditorial $editorial): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:200', "unique:bib_editoriales,nombre,{$editorial->id}"],
            'pais' => ['nullable', 'string', 'max:100'],
            'estado' => ['required', 'boolean'],
        ]);

        $editorial->update($validated);

        return redirect()->route('biblioteca.editoriales.index')->with('success', 'Editorial actualizada.');
    }

    public function destroy(BibEditorial $editorial): RedirectResponse
    {
        if ($editorial->catalogos()->exists()) {
            return redirect()->route('biblioteca.editoriales.index')->with('error', 'No se puede eliminar porque tiene títulos asociados.');
        }

        $editorial->delete();

        return redirect()->route('biblioteca.editoriales.index')->with('success', 'Editorial eliminada.');
    }
}
