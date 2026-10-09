<?php

namespace App\Http\Controllers;

use App\Models\AnioEscolar;
use App\Models\DetalleDevolucion;
use App\Models\Devolucion;
use App\Models\Entrega;
use App\Models\Estudiante;
use App\Models\Libro;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function index(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);

        $consultaLibros = Libro::query();
        if ($anio['id']) {
            $consultaLibros->where('id_anio_escolar', $anio['id']);
        }

        $totalLibros = (int) $consultaLibros->clone()->sum('cantidad_total');
        $entregados = (int) $consultaLibros->clone()->sum('cantidad_entregada');
        $devueltos = (int) $consultaLibros->clone()->sum('cantidad_devuelta');
        $perdidos = (int) $consultaLibros->clone()->sum('cantidad_perdida');
        $deteriorados = (int) $consultaLibros->clone()->sum('cantidad_deficiente');
        $disponibles = (int) $consultaLibros->clone()->sum('cantidad_disponible');
        $pendientes = max(0, $entregados - $devueltos - $perdidos - $deteriorados);

        $resumen = compact('totalLibros', 'entregados', 'devueltos', 'pendientes', 'perdidos', 'deteriorados', 'disponibles');

        return view('reportes.index', [
            'resumen' => $resumen,
            'anioEscolar' => $anio['texto'],
            'anios' => $this->listaAniosEscolares(),
        ]);
    }

    public function porGrado(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);

        $datos = DB::table('v_reporte_por_grado_seccion')
            ->when($anio['anio'], fn ($q) => $q->where('anio_escolar', $anio['anio']))
            ->when($request->filled('grado'), fn ($q) => $q->where('grado', $request->input('grado')))
            ->when($request->filled('seccion'), fn ($q) => $q->where('seccion', $request->input('seccion')))
            ->when($request->filled('nivel'), fn ($q) => $q->where('nivel', $request->input('nivel')))
            ->orderBy('grado')
            ->orderBy('seccion')
            ->get();

        return view('reportes.por_grado', [
            'datos' => $datos,
            'titulo' => 'Entregas y devoluciones por grado y sección',
            'subtitulo' => $this->subtituloFiltros($request),
            'anioEscolar' => $anio['texto'],
            'anios' => $this->listaAniosEscolares(),
            'grados' => $this->listaGrados(),
            'secciones' => $this->listaSecciones(),
            'niveles' => $this->listaNiveles(),
        ]);
    }

    public function stockLibros(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);

        $datos = DB::table('v_stock_libros')
            ->when($anio['anio'], fn ($q) => $q->where('anio_escolar', $anio['anio']))
            ->when($request->filled('grado'), fn ($q) => $q->where('grado', $request->input('grado')))
            ->when($request->filled('nivel'), fn ($q) => $q->where('nivel', $request->input('nivel')))
            ->when($request->filled('area'), fn ($q) => $q->where('area', 'like', '%'.$request->input('area').'%'))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->input('estado')))
            ->orderBy('grado')
            ->orderBy('area')
            ->orderBy('nombre')
            ->get();

        return view('reportes.stock', [
            'datos' => $datos,
            'titulo' => 'Stock de libros',
            'subtitulo' => $this->subtituloFiltros($request),
            'anioEscolar' => $anio['texto'],
            'anios' => $this->listaAniosEscolares(),
            'grados' => $this->listaGrados(),
            'niveles' => $this->listaNiveles(),
        ]);
    }

    public function pendientes(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);

        $datos = DB::table('v_estudiantes_sin_devolucion')
            ->when($anio['anio'], fn ($q) => $q->where('anio_escolar', $anio['anio']))
            ->when($request->filled('grado'), fn ($q) => $q->where('grado', $request->input('grado')))
            ->when($request->filled('seccion'), fn ($q) => $q->where('seccion', $request->input('seccion')))
            ->when($request->filled('nivel'), fn ($q) => $q->where('nivel', $request->input('nivel')))
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = $request->input('buscar');
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('estudiante', 'like', "%{$buscar}%")
                        ->orWhere('dni', 'like', "%{$buscar}%")
                        ->orWhere('codigo_estudiante', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('grado')
            ->orderBy('seccion')
            ->orderBy('estudiante')
            ->get();

        return view('reportes.pendientes', [
            'datos' => $datos,
            'titulo' => 'Estudiantes con devoluciones pendientes',
            'subtitulo' => $this->subtituloFiltros($request),
            'anioEscolar' => $anio['texto'],
            'anios' => $this->listaAniosEscolares(),
            'grados' => $this->listaGrados(),
            'secciones' => $this->listaSecciones(),
            'niveles' => $this->listaNiveles(),
        ]);
    }

    public function porEstudiante(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);

        $datos = DB::table('v_entregas_completas')
            ->select(
                'codigo_estudiante',
                'dni_estudiante',
                'estudiante',
                'grado',
                'seccion',
                'nivel',
                'turno',
                DB::raw('COUNT(*) as total_entregas'),
                DB::raw('SUM(total_libros) as libros_entregados')
            )
            ->when($anio['anio'], fn ($q) => $q->where('anio_escolar', $anio['anio']))
            ->when($request->filled('grado'), fn ($q) => $q->where('grado', $request->input('grado')))
            ->when($request->filled('seccion'), fn ($q) => $q->where('seccion', $request->input('seccion')))
            ->when($request->filled('nivel'), fn ($q) => $q->where('nivel', $request->input('nivel')))
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = $request->input('buscar');
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('estudiante', 'like', "%{$buscar}%")
                        ->orWhere('dni_estudiante', 'like', "%{$buscar}%")
                        ->orWhere('codigo_estudiante', 'like', "%{$buscar}%");
                });
            })
            ->groupBy('codigo_estudiante', 'dni_estudiante', 'estudiante', 'grado', 'seccion', 'nivel', 'turno')
            ->orderBy('grado')
            ->orderBy('seccion')
            ->orderBy('estudiante')
            ->get();

        return view('reportes.por_estudiante', [
            'datos' => $datos,
            'titulo' => 'Entregas por estudiante',
            'subtitulo' => $this->subtituloFiltros($request),
            'anioEscolar' => $anio['texto'],
            'anios' => $this->listaAniosEscolares(),
            'grados' => $this->listaGrados(),
            'secciones' => $this->listaSecciones(),
            'niveles' => $this->listaNiveles(),
        ]);
    }

    public function porLibro(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);

        $datos = DB::table('detalle_entregas')
            ->join('libros', 'detalle_entregas.id_libro', '=', 'libros.id')
            ->join('entregas', 'detalle_entregas.id_entrega', '=', 'entregas.id')
            ->select(
                'libros.codigo_libro',
                'libros.nombre',
                'libros.tipo_material',
                'libros.area',
                'libros.grado',
                'libros.nivel',
                DB::raw('SUM(detalle_entregas.cantidad) as total_entregado'),
                DB::raw('COUNT(DISTINCT detalle_entregas.id_entrega) as total_entregas')
            )
            ->when($anio['id'], fn ($q) => $q->where('entregas.id_anio_escolar', $anio['id']))
            ->when($request->filled('grado'), fn ($q) => $q->where('libros.grado', $request->input('grado')))
            ->when($request->filled('nivel'), fn ($q) => $q->where('libros.nivel', $request->input('nivel')))
            ->when($request->filled('area'), fn ($q) => $q->where('libros.area', 'like', '%'.$request->input('area').'%'))
            ->when($request->filled('buscar'), fn ($q) => $q->where('libros.nombre', 'like', '%'.$request->input('buscar').'%'))
            ->groupBy('libros.id', 'libros.codigo_libro', 'libros.nombre', 'libros.tipo_material', 'libros.area', 'libros.grado', 'libros.nivel')
            ->orderBy('libros.grado')
            ->orderBy('libros.nombre')
            ->get();

        return view('reportes.por_libro', [
            'datos' => $datos,
            'titulo' => 'Entregas por libro',
            'subtitulo' => $this->subtituloFiltros($request),
            'anioEscolar' => $anio['texto'],
            'anios' => $this->listaAniosEscolares(),
            'grados' => $this->listaGrados(),
            'niveles' => $this->listaNiveles(),
        ]);
    }

    public function entregasRealizadas(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);

        $datos = DB::table('v_entregas_completas')
            ->when($anio['anio'], fn ($q) => $q->where('anio_escolar', $anio['anio']))
            ->when($request->filled('grado'), fn ($q) => $q->where('grado', $request->input('grado')))
            ->when($request->filled('seccion'), fn ($q) => $q->where('seccion', $request->input('seccion')))
            ->when($request->filled('nivel'), fn ($q) => $q->where('nivel', $request->input('nivel')))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado_entrega', $request->input('estado')))
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('fecha_entrega', '>=', $request->input('desde')))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('fecha_entrega', '<=', $request->input('hasta')))
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = $request->input('buscar');
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('estudiante', 'like', "%{$buscar}%")
                        ->orWhere('dni_estudiante', 'like', "%{$buscar}%")
                        ->orWhere('codigo_general', 'like', "%{$buscar}%");
                });
            })
            ->orderByDesc('fecha_entrega')
            ->orderByDesc('hora_entrega')
            ->get();

        return view('reportes.entregas', [
            'datos' => $datos,
            'titulo' => 'Entregas realizadas',
            'subtitulo' => $this->subtituloFiltros($request),
            'anioEscolar' => $anio['texto'],
            'anios' => $this->listaAniosEscolares(),
            'grados' => $this->listaGrados(),
            'secciones' => $this->listaSecciones(),
            'niveles' => $this->listaNiveles(),
        ]);
    }

    public function devolucionesRealizadas(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);

        $datos = DB::table('v_devoluciones_completas')
            ->when($anio['anio'], fn ($q) => $q->where('anio_escolar', $anio['anio']))
            ->when($request->filled('grado'), fn ($q) => $q->where('grado', $request->input('grado')))
            ->when($request->filled('seccion'), fn ($q) => $q->where('seccion', $request->input('seccion')))
            ->when($request->filled('nivel'), fn ($q) => $q->where('nivel', $request->input('nivel')))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado_devolucion', $request->input('estado')))
            ->when($request->filled('desde'), fn ($q) => $q->whereDate('fecha_devolucion', '>=', $request->input('desde')))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('fecha_devolucion', '<=', $request->input('hasta')))
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = $request->input('buscar');
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('estudiante', 'like', "%{$buscar}%")
                        ->orWhere('dni_estudiante', 'like', "%{$buscar}%")
                        ->orWhere('codigo_general', 'like', "%{$buscar}%");
                });
            })
            ->orderByDesc('fecha_devolucion')
            ->orderByDesc('hora_devolucion')
            ->get();

        return view('reportes.devoluciones', [
            'datos' => $datos,
            'titulo' => 'Devoluciones realizadas',
            'subtitulo' => $this->subtituloFiltros($request),
            'anioEscolar' => $anio['texto'],
            'anios' => $this->listaAniosEscolares(),
            'grados' => $this->listaGrados(),
            'secciones' => $this->listaSecciones(),
            'niveles' => $this->listaNiveles(),
        ]);
    }

    public function inventarioGeneral(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);

        $datos = DB::table('v_stock_libros')
            ->when($anio['anio'], fn ($q) => $q->where('anio_escolar', $anio['anio']))
            ->when($request->filled('grado'), fn ($q) => $q->where('grado', $request->input('grado')))
            ->when($request->filled('nivel'), fn ($q) => $q->where('nivel', $request->input('nivel')))
            ->when($request->filled('area'), fn ($q) => $q->where('area', 'like', '%'.$request->input('area').'%'))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->input('estado')))
            ->orderBy('grado')
            ->orderBy('nombre')
            ->get();

        return view('reportes.inventario', [
            'datos' => $datos,
            'titulo' => 'Inventario general',
            'subtitulo' => $this->subtituloFiltros($request),
            'anioEscolar' => $anio['texto'],
            'anios' => $this->listaAniosEscolares(),
            'grados' => $this->listaGrados(),
            'niveles' => $this->listaNiveles(),
        ]);
    }

    public function stockCritico(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);
        $umbral = (int) $request->input('umbral', 5);

        $datos = DB::table('v_stock_libros')
            ->where('cantidad_disponible', '<=', $umbral)
            ->when($anio['anio'], fn ($q) => $q->where('anio_escolar', $anio['anio']))
            ->when($request->filled('grado'), fn ($q) => $q->where('grado', $request->input('grado')))
            ->when($request->filled('nivel'), fn ($q) => $q->where('nivel', $request->input('nivel')))
            ->orderBy('cantidad_disponible')
            ->orderBy('grado')
            ->orderBy('nombre')
            ->get();

        return view('reportes.stock_critico', [
            'datos' => $datos,
            'titulo' => 'Stock crítico',
            'subtitulo' => $this->subtituloFiltros($request),
            'anioEscolar' => $anio['texto'],
            'umbral' => $umbral,
            'anios' => $this->listaAniosEscolares(),
            'grados' => $this->listaGrados(),
            'niveles' => $this->listaNiveles(),
        ]);
    }

    public function situacionEstudiantes(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);

        $consulta = Estudiante::query()
            ->select(
                'estudiantes.id',
                'estudiantes.codigo_estudiante',
                'estudiantes.dni',
                'estudiantes.nombres',
                'estudiantes.apellidos',
                'estudiantes.grado',
                'estudiantes.seccion',
                'estudiantes.nivel',
                'estudiantes.turno',
                'estudiantes.estado'
            )
            ->addSelect([
                'total_entregas' => Entrega::selectRaw('COUNT(*)')
                    ->whereColumn('entregas.id_estudiante', 'estudiantes.id')
                    ->where('entregas.estado', '!=', 'anulada'),
                'total_devoluciones' => Devolucion::selectRaw('COUNT(*)')
                    ->whereColumn('devoluciones.id_estudiante', 'estudiantes.id')
                    ->where('devoluciones.estado', '!=', 'anulada'),
                'libros_entregados' => Entrega::selectRaw('COALESCE(SUM(total_libros), 0)')
                    ->whereColumn('entregas.id_estudiante', 'estudiantes.id')
                    ->where('entregas.estado', '!=', 'anulada'),
            ])
            ->when($anio['id'], fn ($q) => $q->where('estudiantes.id_anio_escolar', $anio['id']))
            ->when($request->filled('grado'), fn ($q) => $q->where('estudiantes.grado', $request->input('grado')))
            ->when($request->filled('seccion'), fn ($q) => $q->where('estudiantes.seccion', $request->input('seccion')))
            ->when($request->filled('nivel'), fn ($q) => $q->where('estudiantes.nivel', $request->input('nivel')))
            ->when($request->filled('estado'), fn ($q) => $q->where('estudiantes.estado', $request->input('estado')))
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = $request->input('buscar');
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('estudiantes.nombres', 'like', "%{$buscar}%")
                        ->orWhere('estudiantes.apellidos', 'like', "%{$buscar}%")
                        ->orWhere('estudiantes.dni', 'like', "%{$buscar}%")
                        ->orWhere('estudiantes.codigo_estudiante', 'like', "%{$buscar}%");
                });
            })
            ->groupBy(
                'estudiantes.id',
                'estudiantes.codigo_estudiante',
                'estudiantes.dni',
                'estudiantes.nombres',
                'estudiantes.apellidos',
                'estudiantes.grado',
                'estudiantes.seccion',
                'estudiantes.nivel',
                'estudiantes.turno',
                'estudiantes.estado'
            )
            ->orderBy('estudiantes.grado')
            ->orderBy('estudiantes.seccion')
            ->orderBy('estudiantes.apellidos')
            ->get();

        return view('reportes.situacion_estudiantes', [
            'datos' => $consulta,
            'titulo' => 'Situación de estudiantes',
            'subtitulo' => $this->subtituloFiltros($request),
            'anioEscolar' => $anio['texto'],
            'anios' => $this->listaAniosEscolares(),
            'grados' => $this->listaGrados(),
            'secciones' => $this->listaSecciones(),
            'niveles' => $this->listaNiveles(),
        ]);
    }

    public function historialEstudiante(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);
        $estudianteId = $request->input('estudiante_id');
        $estudiante = null;
        $entregas = collect();
        $devoluciones = collect();

        if ($estudianteId) {
            $estudiante = Estudiante::with('anioEscolar')->find($estudianteId);

            $entregas = Entrega::with('devoluciones', 'detalles.libro')
                ->where('id_estudiante', $estudianteId)
                ->when($anio['id'], fn ($q) => $q->where('id_anio_escolar', $anio['id']))
                ->orderByDesc('fecha_entrega')
                ->get();

            $devoluciones = Devolucion::with('detalles.libro')
                ->where('id_estudiante', $estudianteId)
                ->when($anio['id'], fn ($q) => $q->where('id_anio_escolar', $anio['id']))
                ->orderByDesc('fecha_devolucion')
                ->get();
        }

        $estudiantes = Estudiante::activos()
            ->when($anio['id'], fn ($q) => $q->where('id_anio_escolar', $anio['id']))
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->get(['id', 'codigo_estudiante', 'nombres', 'apellidos', 'grado', 'seccion']);

        return view('reportes.historial_estudiante', [
            'datos' => $entregas,
            'devoluciones' => $devoluciones,
            'estudiante' => $estudiante,
            'estudiantes' => $estudiantes,
            'titulo' => 'Historial por estudiante',
            'subtitulo' => $estudiante ? $estudiante->nombre_completo : 'Seleccione un estudiante',
            'anioEscolar' => $anio['texto'],
            'anios' => $this->listaAniosEscolares(),
        ]);
    }

    public function perdidos(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);

        $datos = DetalleDevolucion::with(['libro', 'devolucion.estudiante', 'detalleEntrega'])
            ->where('estado_libro', 'perdido')
            ->whereHas('devolucion', function ($q) use ($anio) {
                if ($anio['id']) {
                    $q->where('id_anio_escolar', $anio['id']);
                }
            })
            ->when($request->filled('grado'), function ($q) use ($request) {
                $q->whereHas('devolucion.estudiante', fn ($sub) => $sub->where('grado', $request->input('grado')));
            })
            ->when($request->filled('nivel'), function ($q) use ($request) {
                $q->whereHas('devolucion.estudiante', fn ($sub) => $sub->where('nivel', $request->input('nivel')));
            })
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = $request->input('buscar');
                $q->whereHas('libro', fn ($sub) => $sub->where('nombre', 'like', "%{$buscar}%"));
            })
            ->orderByDesc('created_at')
            ->get();

        return view('reportes.perdidos', [
            'datos' => $datos,
            'titulo' => 'Libros perdidos',
            'subtitulo' => $this->subtituloFiltros($request),
            'anioEscolar' => $anio['texto'],
            'anios' => $this->listaAniosEscolares(),
            'grados' => $this->listaGrados(),
            'niveles' => $this->listaNiveles(),
        ]);
    }

    public function deteriorados(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);

        $datos = DetalleDevolucion::with(['libro', 'devolucion.estudiante', 'detalleEntrega'])
            ->whereIn('estado_libro', ['deteriorado', 'deficiente'])
            ->whereHas('devolucion', function ($q) use ($anio) {
                if ($anio['id']) {
                    $q->where('id_anio_escolar', $anio['id']);
                }
            })
            ->when($request->filled('grado'), function ($q) use ($request) {
                $q->whereHas('devolucion.estudiante', fn ($sub) => $sub->where('grado', $request->input('grado')));
            })
            ->when($request->filled('nivel'), function ($q) use ($request) {
                $q->whereHas('devolucion.estudiante', fn ($sub) => $sub->where('nivel', $request->input('nivel')));
            })
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = $request->input('buscar');
                $q->whereHas('libro', fn ($sub) => $sub->where('nombre', 'like', "%{$buscar}%"));
            })
            ->orderByDesc('created_at')
            ->get();

        return view('reportes.deteriorados', [
            'datos' => $datos,
            'titulo' => 'Libros deteriorados',
            'subtitulo' => $this->subtituloFiltros($request),
            'anioEscolar' => $anio['texto'],
            'anios' => $this->listaAniosEscolares(),
            'grados' => $this->listaGrados(),
            'niveles' => $this->listaNiveles(),
        ]);
    }

    public function bajas(Request $request): View
    {
        $anio = $this->obtenerAnioEscolar($request);

        $datos = Libro::query()
            ->where('estado', '!=', 'activo')
            ->when($anio['id'], fn ($q) => $q->where('id_anio_escolar', $anio['id']))
            ->when($request->filled('grado'), fn ($q) => $q->where('grado', $request->input('grado')))
            ->when($request->filled('nivel'), fn ($q) => $q->where('nivel', $request->input('nivel')))
            ->when($request->filled('buscar'), fn ($q) => $q->where('nombre', 'like', '%'.$request->input('buscar').'%'))
            ->orderBy('estado')
            ->orderBy('grado')
            ->orderBy('nombre')
            ->get();

        return view('reportes.bajas', [
            'datos' => $datos,
            'titulo' => 'Libros dados de baja',
            'subtitulo' => $this->subtituloFiltros($request),
            'anioEscolar' => $anio['texto'],
            'anios' => $this->listaAniosEscolares(),
            'grados' => $this->listaGrados(),
            'niveles' => $this->listaNiveles(),
        ]);
    }

    public function porAnio(Request $request): View
    {
        $datos = DB::table('v_reporte_por_grado_seccion')
            ->select(
                'anio_escolar',
                DB::raw('SUM(total_estudiantes) as total_estudiantes'),
                DB::raw('SUM(total_entregas) as total_entregas'),
                DB::raw('SUM(total_libros_entregados) as total_libros_entregados'),
                DB::raw('SUM(total_devoluciones) as total_devoluciones'),
                DB::raw('SUM(total_libros_devueltos) as total_libros_devueltos'),
                DB::raw('SUM(total_libros_perdidos) as total_libros_perdidos'),
                DB::raw('SUM(total_libros_deficientes) as total_libros_deficientes'),
                DB::raw('SUM(estudiantes_sin_entrega) as estudiantes_sin_entrega')
            )
            ->groupBy('anio_escolar')
            ->orderByDesc('anio_escolar')
            ->get();

        return view('reportes.por_anio', [
            'datos' => $datos,
            'titulo' => 'Reporte por año escolar',
            'subtitulo' => $this->subtituloFiltros($request),
            'anioEscolar' => 'Todos',
        ]);
    }

    public function comparativo(Request $request): View
    {
        $anioUno = $this->obtenerAnioEscolar($request, 'anio_uno');
        $anioDos = $this->obtenerAnioEscolar($request, 'anio_dos');

        $consultaBase = fn (?int $anio) => DB::table('v_reporte_por_grado_seccion')
            ->when($anio, fn ($q) => $q->where('anio_escolar', $anio))
            ->select(
                DB::raw('COALESCE(SUM(total_estudiantes), 0) as total_estudiantes'),
                DB::raw('COALESCE(SUM(total_entregas), 0) as total_entregas'),
                DB::raw('COALESCE(SUM(total_libros_entregados), 0) as total_libros_entregados'),
                DB::raw('COALESCE(SUM(total_devoluciones), 0) as total_devoluciones'),
                DB::raw('COALESCE(SUM(total_libros_devueltos), 0) as total_libros_devueltos'),
                DB::raw('COALESCE(SUM(total_libros_perdidos), 0) as total_libros_perdidos'),
                DB::raw('COALESCE(SUM(total_libros_deficientes), 0) as total_libros_deficientes'),
                DB::raw('COALESCE(SUM(estudiantes_sin_entrega), 0) as estudiantes_sin_entrega')
            )
            ->first();

        $datosAnioUno = $consultaBase($anioUno['anio']);
        $datosAnioDos = $consultaBase($anioDos['anio']);

        return view('reportes.comparativo', [
            'datosAnioUno' => $datosAnioUno,
            'datosAnioDos' => $datosAnioDos,
            'titulo' => 'Comparativo entre años',
            'subtitulo' => "{$anioUno['texto']} vs {$anioDos['texto']}",
            'anioEscolar' => 'Comparativo',
            'anios' => $this->listaAniosEscolares(),
            'anioUno' => $anioUno['id'],
            'anioDos' => $anioDos['id'],
        ]);
    }

    /**
     * Obtiene el año escolar seleccionado en el request.
     *
     * @return array<string, mixed>
     */
    private function obtenerAnioEscolar(Request $request, string $campo = 'anio'): array
    {
        $id = $request->input($campo);
        $anio = null;
        $texto = 'Todos';

        if ($id) {
            $registro = AnioEscolar::find($id);
            if ($registro) {
                $anio = (int) $registro->anio;
                $texto = (string) $anio;
            }
        }

        return ['id' => $id, 'anio' => $anio, 'texto' => $texto];
    }

    /**
     * @return Collection<int, int>
     */
    private function listaAniosEscolares(): Collection
    {
        return AnioEscolar::orderByDesc('anio')->pluck('anio', 'id');
    }

    /**
     * @return Collection<int, int>
     */
    private function listaGrados(): Collection
    {
        return Estudiante::distinct()->orderBy('grado')->pluck('grado');
    }

    /**
     * @return Collection<int, string>
     */
    private function listaSecciones(): Collection
    {
        return Estudiante::distinct()->orderBy('seccion')->pluck('seccion');
    }

    /**
     * @return Collection<int, string>
     */
    private function listaNiveles(): Collection
    {
        return collect(['inicial', 'primaria', 'secundaria']);
    }

    /**
     * Genera un subtítulo descriptivo con los filtros aplicados.
     */
    private function subtituloFiltros(Request $request): string
    {
        $filtros = [];

        if ($request->filled('grado')) {
            $filtros[] = 'Grado: '.$request->input('grado');
        }

        if ($request->filled('seccion')) {
            $filtros[] = 'Sección: '.$request->input('seccion');
        }

        if ($request->filled('nivel')) {
            $filtros[] = 'Nivel: '.$request->input('nivel');
        }

        if ($request->filled('estado')) {
            $filtros[] = 'Estado: '.$request->input('estado');
        }

        if ($request->filled('desde')) {
            $filtros[] = 'Desde: '.$request->input('desde');
        }

        if ($request->filled('hasta')) {
            $filtros[] = 'Hasta: '.$request->input('hasta');
        }

        if ($request->filled('buscar')) {
            $filtros[] = 'Búsqueda: '.$request->input('buscar');
        }

        return empty($filtros) ? 'Todos los registros' : implode(' | ', $filtros);
    }
}
