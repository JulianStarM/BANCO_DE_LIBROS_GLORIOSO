<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibUbicacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UbicacionBibliotecaController extends Controller
{
    public function index(Request $request): View
    {
        $ubicaciones = BibUbicacion::query()
            ->when($request->filled('buscar'), fn ($q) => $q->where('nombre', 'like', "%{$request->input('buscar')}%"))
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return view('biblioteca.ubicaciones.index', compact('ubicaciones'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'boolean'],
        ]);

        BibUbicacion::create($validated);

        return redirect()->route('biblioteca.ubicaciones.index')->with('success', 'Ubicación registrada.');
    }

    public function update(Request $request, BibUbicacion $ubicacion): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'boolean'],
        ]);

        $ubicacion->update($validated);

        return redirect()->route('biblioteca.ubicaciones.index')->with('success', 'Ubicación actualizada.');
    }

    public function destroy(BibUbicacion $ubicacion): RedirectResponse
    {
        if ($ubicacion->estantes()->exists()) {
            return redirect()->route('biblioteca.ubicaciones.index')->with('error', 'No se puede eliminar porque tiene estantes asociados.');
        }

        $ubicacion->delete();

        return redirect()->route('biblioteca.ubicaciones.index')->with('success', 'Ubicación eliminada.');
    }
}
