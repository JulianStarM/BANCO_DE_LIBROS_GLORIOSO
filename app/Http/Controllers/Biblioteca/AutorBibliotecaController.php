<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibAutor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutorBibliotecaController extends Controller
{
    public function index(Request $request): View
    {
        $autores = BibAutor::query()
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $q->where('nombres', 'like', "%{$request->input('buscar')}%")
                    ->orWhere('apellidos', 'like', "%{$request->input('buscar')}%");
            })
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(20)
            ->withQueryString();

        return view('biblioteca.autores.index', compact('autores'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombres' => ['required', 'string', 'max:150'],
            'apellidos' => ['required', 'string', 'max:150'],
            'seudonimo' => ['nullable', 'string', 'max:100'],
            'nacionalidad' => ['nullable', 'string', 'max:100'],
        ]);

        BibAutor::create($validated);

        return redirect()->route('biblioteca.autores.index')->with('success', 'Autor registrado.');
    }

    public function update(Request $request, BibAutor $autor): RedirectResponse
    {
        $validated = $request->validate([
            'nombres' => ['required', 'string', 'max:150'],
            'apellidos' => ['required', 'string', 'max:150'],
            'seudonimo' => ['nullable', 'string', 'max:100'],
            'nacionalidad' => ['nullable', 'string', 'max:100'],
        ]);

        $autor->update($validated);

        return redirect()->route('biblioteca.autores.index')->with('success', 'Autor actualizado.');
    }

    public function destroy(BibAutor $autor): RedirectResponse
    {
        if ($autor->catalogos()->exists()) {
            return redirect()->route('biblioteca.autores.index')->with('error', 'No se puede eliminar porque tiene títulos asociados.');
        }

        $autor->delete();

        return redirect()->route('biblioteca.autores.index')->with('success', 'Autor eliminado.');
    }
}
