<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibTipoMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TipoMaterialBibliotecaController extends Controller
{
    public function index(Request $request): View
    {
        $tipos = BibTipoMaterial::query()
            ->when($request->filled('buscar'), fn ($q) => $q->where('nombre', 'like', "%{$request->input('buscar')}%"))
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return view('biblioteca.tipos-material.index', compact('tipos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', 'unique:bib_tipos_material,nombre'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'boolean'],
        ]);

        BibTipoMaterial::create($validated);

        return redirect()->route('biblioteca.tipos-material.index')->with('success', 'Tipo de material registrado.');
    }

    public function update(Request $request, BibTipoMaterial $tipo): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100', "unique:bib_tipos_material,nombre,{$tipo->id}"],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'boolean'],
        ]);

        $tipo->update($validated);

        return redirect()->route('biblioteca.tipos-material.index')->with('success', 'Tipo de material actualizado.');
    }

    public function destroy(BibTipoMaterial $tipo): RedirectResponse
    {
        if ($tipo->catalogos()->exists()) {
            return redirect()->route('biblioteca.tipos-material.index')->with('error', 'No se puede eliminar porque tiene títulos asociados.');
        }

        $tipo->delete();

        return redirect()->route('biblioteca.tipos-material.index')->with('success', 'Tipo de material eliminado.');
    }
}
