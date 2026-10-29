<?php

namespace App\Http\Controllers\Biblioteca;

use App\Http\Controllers\Controller;
use App\Models\BibMulta;
use App\Models\BibSocio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MultaBibliotecaController extends Controller
{
    public function index(Request $request): View
    {
        $query = BibMulta::query()->with(['socio', 'prestamo']);

        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->whereHas('socio', function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        $multas = $query->orderByDesc('fecha_multa')->paginate(20)->withQueryString();
        $socios = BibSocio::where('estado', 'activo')->orderBy('apellidos')->get();
        $prestamos = BibPrestamo::with('socio')
            ->whereIn('estado', ['activo', 'vencido'])
            ->orderByDesc('fecha_prestamo')
            ->get();

        return view('biblioteca.multas.index', compact('multas', 'socios', 'prestamos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_prestamo' => ['required', 'exists:bib_prestamos,id'],
            'id_socio' => ['required', 'exists:bib_socios,id'],
            'tipo_multa' => ['required', 'in:mora,perdida,dano,otro'],
            'dias_atraso' => ['nullable', 'integer', 'min:0'],
            'monto' => ['required', 'numeric', 'min:0'],
            'fecha_multa' => ['required', 'date'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $validated['estado'] = 'pendiente';
        $validated['id_usuario'] = auth()->id();

        BibMulta::create($validated);

        return redirect()->route('biblioteca.multas.index')->with('success', 'Multa registrada.');
    }

    public function update(Request $request, BibMulta $multa): RedirectResponse
    {
        $validated = $request->validate([
            'tipo_multa' => ['required', 'in:mora,perdida,dano,otro'],
            'dias_atraso' => ['nullable', 'integer', 'min:0'],
            'monto' => ['required', 'numeric', 'min:0'],
            'estado' => ['required', 'in:pendiente,pagada,exonerada'],
            'fecha_pago' => ['nullable', 'date'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $multa->update($validated);

        return redirect()->route('biblioteca.multas.index')->with('success', 'Multa actualizada.');
    }

    public function destroy(BibMulta $multa): RedirectResponse
    {
        $multa->delete();

        return redirect()->route('biblioteca.multas.index')->with('success', 'Multa eliminada.');
    }
}
