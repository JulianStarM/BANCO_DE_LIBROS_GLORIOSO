<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Constancia de Devolución de Textos - {{ $devolucion->codigo_general }}</title>
    @include('pdf.partials.estilos-constancia')
</head>
<body>
    <div class="page">
        <div class="border-outer"></div>
        <div class="border-inner"></div>

        <div class="content-wrapper">
            {{-- Encabezado institucional --}}
            <div class="header">
                <div class="logo-area">
                    <img src="{{ $logo }}" alt="Logo Institución">
                </div>
                <div class="institution">
                    <p class="name">Institución Educativa Emblemática</p>
                    <p class="subname">Glorioso Colegio Nacional San Carlos</p>
                    <p class="meta">UGEL Puno / DRE Puno / MINEDU</p>
                </div>
            </div>

            {{-- Título --}}
            <div class="title-area">
                <h1>Constancia de Devolución de Textos</h1>
                <p class="codigo">Código de constancia: <strong>{{ $devolucion->codigo_general }}</strong></p>
            </div>

            {{-- Texto introductorio --}}
            <p class="intro">
                Conste por el presente documento que el/la estudiante
                <strong class="uppercase">{{ $devolucion->estudiante->apellidos ?? '' }}, {{ $devolucion->estudiante->nombres ?? '' }}</strong>,
                ha procedido a la devolución de los textos educativos del Banco de Libros correspondientes al año escolar
                <strong>{{ $devolucion->anioEscolar->anio ?? '' }}</strong>.
            </p>

            {{-- Datos del estudiante --}}
            <div class="data-box">
                <p class="box-title">Datos del Estudiante</p>
                <div class="box-body">
                    <table class="data-table">
                        <tr>
                            <td class="label">Código de estudiante:</td>
                            <td class="value">{{ $devolucion->estudiante->codigo_estudiante ?? '—' }}</td>
                            <td class="label">DNI:</td>
                            <td class="value">{{ $devolucion->estudiante->dni ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Apellidos y nombres:</td>
                            <td class="value uppercase">{{ $devolucion->estudiante->apellidos ?? '' }}, {{ $devolucion->estudiante->nombres ?? '' }}</td>
                            <td class="label">Año escolar:</td>
                            <td class="value">{{ $devolucion->anioEscolar->anio ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Nivel:</td>
                            <td class="value">{{ ucfirst($devolucion->estudiante->nivel ?? '—') }}</td>
                            <td class="label">Grado:</td>
                            <td class="value">{{ $devolucion->estudiante->grado ?? '—' }}°</td>
                        </tr>
                        <tr>
                            <td class="label">Sección:</td>
                            <td class="value">{{ strtoupper($devolucion->estudiante->seccion ?? '—') }}</td>
                            <td class="label">Fecha de devolución:</td>
                            <td class="value">{{ optional($devolucion->fecha_devolucion)->format('d/m/Y') }} {{ $devolucion->hora_devolucion ? 'a las ' . \Carbon\Carbon::parse($devolucion->hora_devolucion)->format('H:i') : '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Apoderado --}}
            <div class="data-box">
                <p class="box-title">Devolución realizada por</p>
                <div class="box-body">
                    <table class="data-table">
                        <tr>
                            <td class="label">Devuelto por:</td>
                            <td class="value">
                                @if($devolucion->tipo_firmante === 'apoderado' && $devolucion->padre)
                                    <strong class="uppercase">{{ $devolucion->padre->nombre_completo }}</strong>
                                    (DNI: {{ $devolucion->padre->dni ?? '—' }})
                                @else
                                    <strong>El propio estudiante</strong>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Tabla de libros devueltos --}}
            <div class="data-box">
                <p class="box-title">Relación de Textos Devueltos</p>
                <div class="box-body">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 8%;" class="text-center">N°</th>
                                <th style="width: 18%;">Código del libro</th>
                                <th style="width: 38%;">Nombre del libro</th>
                                <th style="width: 14%;" class="text-center">Estado</th>
                                <th style="width: 22%;">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($devolucion->detalles as $index => $detalle)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $detalle->libro->codigo_libro ?? '—' }}</td>
                                    <td>{{ $detalle->libro->nombre ?? 'Libro no encontrado' }}</td>
                                    <td class="text-center">{{ ucfirst(str_replace('_', ' ', $detalle->estado_libro ?? '—')) }}</td>
                                    <td>{{ $detalle->observaciones ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No se registraron libros en esta devolución.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Resumen numérico --}}
            <div class="summary-box">
                <div class="summary-item">
                    <span class="number">{{ $devolucion->total_devueltos ?? 0 }}</span>
                    <span class="label">Devueltos</span>
                </div>
                <div class="summary-item">
                    <span class="number">{{ $devolucion->total_no_devueltos ?? 0 }}</span>
                    <span class="label">No devueltos</span>
                </div>
                <div class="summary-item">
                    <span class="number">{{ $devolucion->total_deficientes ?? 0 }}</span>
                    <span class="label">Deteriorados</span>
                </div>
                <div class="summary-item">
                    <span class="number">{{ $devolucion->total_perdidos ?? 0 }}</span>
                    <span class="label">Perdidos</span>
                </div>
            </div>

            {{-- Nota legal --}}
            <div class="legal-note">
                OJO: Solo es válido para efecto de matrícula y recojo de libreta, más NO para traslado.
            </div>

            {{-- Firmas --}}
            <div class="signatures">
                <div class="signature">
                    <div class="line"></div>
                    <p class="name">Firma del estudiante / apoderado</p>
                    <p class="role">DNI: {{ $devolucion->tipo_firmante === 'apoderado' ? ($devolucion->padre->dni ?? '—') : ($devolucion->estudiante->dni ?? '—') }}</p>
                </div>
                <div class="signature">
                    <div class="line"></div>
                    <p class="name">Firma del responsable de devolución</p>
                    <p class="role">{{ $devolucion->usuarioRegistro->nombres ?? '—' }} {{ $devolucion->usuarioRegistro->apellidos ?? '' }}</p>
                </div>
            </div>

            {{-- Sello --}}
            <div style="text-align: center; margin-top: 4mm;">
                <p style="font-size: 7.5pt; color: #555; margin: 0;">Espacio para sello institucional</p>
            </div>

            {{-- Pie de página --}}
            <div class="footer">
                <p class="mb-0">
                    Emitido el <strong>{{ $fechaEmision }}</strong> |
                    Constancia generada desde el Sistema de Banco de Libros |
                    Puno – Perú
                </p>
            </div>
        </div>
    </div>
</body>
</html>
