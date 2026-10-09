<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfiguracionController extends Controller
{
    private const CATEGORIAS_ESTUDIANTES = ['niveles', 'grados', 'secciones', 'turnos'];

    private const CATEGORIAS_LIBROS = ['areas', 'editoriales', 'tipos_material', 'niveles', 'grados'];

    public function index(): View
    {
        $modulos = Configuracion::select('modulo')
            ->distinct()
            ->pluck('modulo')
            ->mapWithKeys(function ($modulo) {
                return [$modulo => Configuracion::where('modulo', $modulo)->get()];
            });

        return view('configuraciones.index', compact('modulos'));
    }

    public function estudiantes(): View
    {
        $config = [
            'niveles' => Configuracion::listaEstudiantes('niveles', ['inicial', 'primaria', 'secundaria']),
            'grados' => Configuracion::listaEstudiantes('grados', ['1', '2', '3', '4', '5', '6']),
            'secciones' => Configuracion::listaEstudiantes('secciones', ['A', 'B', 'C', 'D']),
            'turnos' => Configuracion::listaEstudiantes('turnos', ['mañana', 'tarde']),
            'campos_obligatorios' => Configuracion::obtener('estudiantes', 'campos_obligatorios', [
                'dni' => true,
                'nombres' => true,
                'apellidos' => true,
                'sexo' => true,
                'nivel' => true,
                'grado' => true,
                'seccion' => true,
                'codigo_estudiante' => false,
                'direccion' => false,
                'telefono' => false,
                'turno' => false,
                'id_anio_escolar' => false,
            ]),
            'campos_visibles' => Configuracion::obtener('estudiantes', 'campos_visibles', [
                'codigo_estudiante' => true,
                'dni' => true,
                'nombres' => true,
                'apellidos' => true,
                'sexo' => true,
                'nivel' => true,
                'grado' => true,
                'seccion' => true,
                'turno' => true,
                'direccion' => true,
                'telefono' => true,
                'id_anio_escolar' => true,
                'estado' => true,
            ]),
        ];

        return view('configuraciones.estudiantes', compact('config'));
    }

    public function libros(): View
    {
        $config = [
            'areas' => Configuracion::listaItems('libros', 'areas', [
                'MATEMÁTICA',
                'COMUNICACIÓN',
                'CIENCIA Y TECNOLOGÍA',
                'PERSONAL SOCIAL',
                'EDUCACIÓN FÍSICA',
                'ARTE Y CULTURA',
                'INGLÉS',
            ]),
            'editoriales' => Configuracion::listaItems('libros', 'editoriales', [
                'Santillana',
                'Norma',
                'Editorial SM',
                'Educando',
            ]),
            'tipos_material' => Configuracion::listaItems('libros', 'tipos_material', [
                'libro',
                'cuaderno_trabajo',
                'guia_docente',
                'material_complementario',
            ]),
            'niveles' => Configuracion::listaItems('libros', 'niveles', ['inicial', 'primaria', 'secundaria']),
            'grados' => Configuracion::listaItems('libros', 'grados', ['1', '2', '3', '4', '5', '6']),
        ];

        return view('configuraciones.libros', compact('config'));
    }

    public function agregarItem(Request $request, string $categoria): RedirectResponse
    {
        return $this->procesarAgregar($request, 'estudiantes', $categoria, self::CATEGORIAS_ESTUDIANTES);
    }

    public function agregarItemLibro(Request $request, string $categoria): RedirectResponse
    {
        return $this->procesarAgregar($request, 'libros', $categoria, self::CATEGORIAS_LIBROS);
    }

    public function actualizarItem(Request $request, string $categoria, string $id): RedirectResponse
    {
        return $this->procesarActualizar($request, 'estudiantes', $categoria, $id, self::CATEGORIAS_ESTUDIANTES);
    }

    public function actualizarItemLibro(Request $request, string $categoria, string $id): RedirectResponse
    {
        return $this->procesarActualizar($request, 'libros', $categoria, $id, self::CATEGORIAS_LIBROS);
    }

    public function toggleItem(string $categoria, string $id): RedirectResponse
    {
        return $this->procesarToggle('estudiantes', $categoria, $id, self::CATEGORIAS_ESTUDIANTES);
    }

    public function toggleItemLibro(string $categoria, string $id): RedirectResponse
    {
        return $this->procesarToggle('libros', $categoria, $id, self::CATEGORIAS_LIBROS);
    }

    public function eliminarItem(string $categoria, string $id): RedirectResponse
    {
        return $this->procesarEliminar('estudiantes', $categoria, $id, self::CATEGORIAS_ESTUDIANTES);
    }

    public function eliminarItemLibro(string $categoria, string $id): RedirectResponse
    {
        return $this->procesarEliminar('libros', $categoria, $id, self::CATEGORIAS_LIBROS);
    }

    public function updateEstudiantes(Request $request): RedirectResponse
    {
        $request->validate([
            'campos_obligatorios' => ['required', 'array'],
            'campos_visibles' => ['required', 'array'],
        ]);

        $camposObligatorios = array_map(fn ($v) => (bool) $v, $request->input('campos_obligatorios', []));
        $camposVisibles = array_map(fn ($v) => (bool) $v, $request->input('campos_visibles', []));

        Configuracion::guardar('estudiantes', 'campos_obligatorios', $camposObligatorios);
        Configuracion::guardar('estudiantes', 'campos_visibles', $camposVisibles);

        return redirect()
            ->route('configuraciones.estudiantes')
            ->with('success', 'Configuración de estudiantes actualizada correctamente.');
    }

    private function procesarAgregar(Request $request, string $modulo, string $categoria, array $categoriasValidas): RedirectResponse
    {
        abort_unless(in_array($categoria, $categoriasValidas, true), 404);

        $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
        ]);

        $items = Configuracion::listaItems($modulo, $categoria);
        $nombre = trim($request->input('nombre'));

        if ($this->existeNombre($items, $nombre)) {
            return back()->with('error', 'Ya existe un valor con ese nombre.');
        }

        $items[] = ['id' => uniqid(), 'nombre' => $nombre, 'estado' => 'activo'];
        Configuracion::guardar($modulo, $categoria, $items);

        $ruta = $modulo === 'libros' ? 'configuraciones.libros' : 'configuraciones.estudiantes';

        return back()->with('success', ucfirst($categoria).' actualizado.')->withInput();
    }

    private function procesarActualizar(Request $request, string $modulo, string $categoria, string $id, array $categoriasValidas): RedirectResponse
    {
        abort_unless(in_array($categoria, $categoriasValidas, true), 404);

        $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
        ]);

        $items = Configuracion::listaItems($modulo, $categoria);
        $nombre = trim($request->input('nombre'));

        $indice = $this->buscarIndicePorId($items, $id);
        if ($indice === null) {
            return back()->with('error', 'Valor no encontrado.');
        }

        if ($this->existeNombre($items, $nombre, $id)) {
            return back()->with('error', 'Ya existe un valor con ese nombre.');
        }

        $items[$indice]['nombre'] = $nombre;
        Configuracion::guardar($modulo, $categoria, array_values($items));

        return back()->with('success', 'Valor actualizado.');
    }

    private function procesarToggle(string $modulo, string $categoria, string $id, array $categoriasValidas): RedirectResponse
    {
        abort_unless(in_array($categoria, $categoriasValidas, true), 404);

        $items = Configuracion::listaItems($modulo, $categoria);
        $indice = $this->buscarIndicePorId($items, $id);

        if ($indice === null) {
            return back()->with('error', 'Valor no encontrado.');
        }

        $items[$indice]['estado'] = $items[$indice]['estado'] === 'activo' ? 'inactivo' : 'activo';
        Configuracion::guardar($modulo, $categoria, array_values($items));

        return back()->with('success', 'Estado actualizado.');
    }

    private function procesarEliminar(string $modulo, string $categoria, string $id, array $categoriasValidas): RedirectResponse
    {
        abort_unless(in_array($categoria, $categoriasValidas, true), 404);

        $items = Configuracion::listaItems($modulo, $categoria);
        $indice = $this->buscarIndicePorId($items, $id);

        if ($indice === null) {
            return back()->with('error', 'Valor no encontrado.');
        }

        unset($items[$indice]);
        Configuracion::guardar($modulo, $categoria, array_values($items));

        return back()->with('success', 'Valor eliminado.');
    }

    /**
     * @param  array<int, array{id: string, nombre: string, estado: string}>  $items
     */
    private function existeNombre(array $items, string $nombre, ?string $excluirId = null): bool
    {
        $nombreLower = mb_strtolower($nombre);

        foreach ($items as $item) {
            if ($excluirId !== null && ($item['id'] ?? null) === $excluirId) {
                continue;
            }
            if (mb_strtolower($item['nombre']) === $nombreLower) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, array{id: string, nombre: string, estado: string}>  $items
     */
    private function buscarIndicePorId(array $items, string $id): ?int
    {
        foreach ($items as $indice => $item) {
            if (($item['id'] ?? null) === $id) {
                return $indice;
            }
        }

        return null;
    }
}
