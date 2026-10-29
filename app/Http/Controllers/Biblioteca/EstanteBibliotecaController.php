<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibEstante;
use App\Models\BibUbicacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EstanteBibliotecaController extends Controller
{
    public function index(Request $request): View
    {
        $estantes = BibEstante::query()
            ->with('ubicacion')
            ->when($request->filled('buscar'), fn ($q) => $q->where('nombre', 'like', "%{$request->input('buscar')}%"))
            ->orderBy('codigo_estante')
            ->paginate(20)
            ->withQueryString();

        $ubicaciones = BibUbicacion::where('estado', true)->orderBy('nombre')->get();

        return view('biblioteca.estantes.index', compact('estantes', 'ubicaciones'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_ubicacion' => ['required', 'exists:bib_ubicaciones,id'],
            'codigo_estante' => ['required', 'string', 'max:20', 'unique:bib_estantes,codigo_estante'],
            'nombre' => ['required', 'string', 'max:100'],
            'capacidad_maxima' => ['nullable', 'integer', 'min:0'],
            'estado' => ['required', 'boolean'],
        ]);

        BibEstante::create($validated);

        return redirect()->route('biblioteca.estantes.index')->with('success', 'Estante registrado.');
    }

    public function update(Request $request, BibEstante $estante): RedirectResponse
    {
        $validated = $request->validate([
            'id_ubicacion' => ['required', 'exists:bib_ubicaciones,id'],
            'codigo_estante' => ['required', 'string', 'max:20', "unique:bib_estantes,codigo_estante,{$estante->id}"],
            'nombre' => ['required', 'string', 'max:100'],
            'capacidad_maxima' => ['nullable', 'integer', 'min:0'],
            'estado' => ['required', 'boolean'],
        ]);

        $estante->update($validated);

        return redirect()->route('biblioteca.estantes.index')->with('success', 'Estante actualizado.');
    }

    public function destroy(BibEstante $estante): RedirectResponse
    {
        if ($estante->ejemplares()->exists()) {
            return redirect()->route('biblioteca.estantes.index')->with('error', 'No se puede eliminar porque tiene ejemplares asociados.');
        }

        $estante->delete();

        return redirect()->route('biblioteca.estantes.index')->with('success', 'Estante eliminado.');
    }
}
