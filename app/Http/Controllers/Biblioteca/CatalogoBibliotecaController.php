<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibArea;
use App\Models\BibAutor;
use App\Models\BibCatalogo;
use App\Models\BibEditorial;
use App\Models\BibTipoMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogoBibliotecaController extends Controller
{
    public function index(Request $request): View
    {
        $query = BibCatalogo::query()
            ->with(['tipoMaterial', 'area', 'editorial', 'autores', 'ejemplares']);

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('isbn', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('tipo_material')) {
            $query->where('id_tipo_material', $request->input('tipo_material'));
        }

        if ($request->filled('area')) {
            $query->where('id_area', $request->input('area'));
        }

        if ($request->filled('editorial')) {
            $query->where('id_editorial', $request->input('editorial'));
        }

        $catalogo = $query->orderBy('titulo')->paginate(20)->withQueryString();

        $tipos = BibTipoMaterial::where('estado', true)->orderBy('nombre')->get();
        $areas = BibArea::where('estado', true)->orderBy('nombre')->get();
        $editoriales = BibEditorial::where('estado', true)->orderBy('nombre')->get();
        $autores = BibAutor::orderBy('apellidos')->orderBy('nombres')->get();

        return view('biblioteca.catalogo.index', compact('catalogo', 'tipos', 'areas', 'editoriales', 'autores'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->reglas());

        $catalogo = BibCatalogo::create($validated);
        $this->sincronizarAutores($catalogo, $request);

        return redirect()->route('biblioteca.catalogo.index')->with('success', 'Título registrado en el catálogo.');
    }

    public function update(Request $request, BibCatalogo $catalogo): RedirectResponse
    {
        $validated = $request->validate($this->reglas($catalogo->id));

        $catalogo->update($validated);
        $this->sincronizarAutores($catalogo, $request);

        return redirect()->route('biblioteca.catalogo.index')->with('success', 'Título actualizado.');
    }

    public function destroy(BibCatalogo $catalogo): RedirectResponse
    {
        if ($catalogo->ejemplares()->exists()) {
            return redirect()->route('biblioteca.catalogo.index')->with('error', 'No se puede eliminar porque tiene ejemplares registrados.');
        }

        $catalogo->autores()->detach();
        $catalogo->delete();

        return redirect()->route('biblioteca.catalogo.index')->with('success', 'Título eliminado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function reglas(?int $id = null): array
    {
        return [
            'id_tipo_material' => ['required', 'exists:bib_tipos_material,id'],
            'id_area' => ['nullable', 'exists:bib_areas,id'],
            'id_editorial' => ['nullable', 'exists:bib_editoriales,id'],
            'titulo' => ['required', 'string', 'max:300'],
            'subtitulo' => ['nullable', 'string', 'max:300'],
            'isbn' => ['nullable', 'string', 'max:20', $id ? "unique:bib_catalogo,isbn,{$id}" : 'unique:bib_catalogo,isbn'],
            'edicion' => ['nullable', 'string', 'max:50'],
            'anio_publicacion' => ['nullable', 'integer', 'min:1000', 'max:'.(date('Y') + 1)],
            'numero_paginas' => ['nullable', 'integer', 'min:1'],
            'idioma' => ['nullable', 'string', 'max:50'],
            'descripcion' => ['nullable', 'string'],
            'estado' => ['required', 'boolean'],
        ];
    }

    private function sincronizarAutores(BibCatalogo $catalogo, Request $request): void
    {
        $autores = $request->input('autores', []);
        $sync = [];

        foreach ($autores as $index => $autorId) {
            if ($autorId) {
                $sync[$autorId] = ['rol' => 'autor', 'orden' => $index + 1];
            }
        }

        $catalogo->autores()->sync($sync);
    }
}
