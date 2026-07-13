<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $usuarios = Usuario::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('nombre', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('usuarios.index', compact('usuarios', 'q'));
    }

    public function create()
    {
        $usuario = new Usuario;

        return view('usuarios.form', compact('usuario'));
    }

    public function store(Request $request)
    {
        try {
            $data = $this->validateData($request);

            $data['password'] = Hash::make($data['password']);
            $data['estado'] = $request->boolean('estado', true);

            $usuario = Usuario::create($data);

            return redirect()
                ->route('usuarios.index')
                ->with('success', str_replace(':nombre', $usuario->nombre, config('messages.usuarios.store.success')));
        } catch (\Exception $e) {
            return redirect()
                ->route('usuarios.index')
                ->with('error', config('messages.usuarios.store.error'));
        }
    }

    public function show(Usuario $usuario)
    {
        return view('usuarios.show', compact('usuario'));
    }

    public function edit(Usuario $usuario)
    {
        return view('usuarios.form', compact('usuario'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        try {
            $data = $this->validateData($request, $usuario->id);

            if (! empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $data['estado'] = $request->boolean('estado', true);

            $usuario->update($data);

            return redirect()
                ->route('usuarios.index')
                ->with('success', config('messages.usuarios.update.success'));
        } catch (\Exception $e) {
            return redirect()
                ->route('usuarios.index')
                ->with('error', config('messages.usuarios.update.error'));
        }
    }

    public function destroy(Usuario $usuario)
    {
        if (auth()->id() === $usuario->id) {
            return back()->with('warning', config('messages.usuarios.destroy.warning'));
        }

        try {
            $usuario->delete();

            return redirect()
                ->route('usuarios.index')
                ->with('success', config('messages.usuarios.destroy.success'));
        } catch (\Exception $e) {
            return redirect()
                ->route('usuarios.index')
                ->with('error', config('messages.usuarios.destroy.error'));
        }
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'max:150', Rule::unique('usuarios', 'email')->ignore($id)],
            'rol' => ['required', 'in:admin,docente,auxiliar,directivo,readonly'],
            'password' => [$id ? 'nullable' : 'required', 'string', 'min:6', 'max:50'],
        ]);
    }
}
