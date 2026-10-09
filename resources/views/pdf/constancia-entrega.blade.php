<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Constancia de Entrega de Textos - {{ $entrega->codigo_general }}</title>
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
                <h1>Constancia de Entrega de Textos</h1>
                <p class="codigo">Código de constancia: <strong>{{ $entrega->codigo_general }}</strong></p>
            </div>

            {{-- Texto introductorio --}}
            <p class="intro">
                Conste por el presente documento que el/la estudiante
                <strong class="uppercase">{{ $entrega->estudiante->apellidos ?? '' }}, {{ $entrega->estudiante->nombres ?? '' }}</strong>,
                ha recibido los textos educativos del Banco de Libros correspondientes al año escolar
                <strong>{{ $entrega->anioEscolar->anio ?? '' }}</strong>.
            </p>

            {{-- Datos del estudiante --}}
            <div class="data-box">
                <p class="box-title">Datos del Estudiante</p>
                <div class="box-body">
                    <table class="data-table">
                        <tr>
                            <td class="label">Código de estudiante:</td>
                            <td class="value">{{ $entrega->estudiante->codigo_estudiante ?? '—' }}</td>
                            <td class="label">DNI:</td>
                            <td class="value">{{ $entrega->estudiante->dni ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Apellidos y nombres:</td>
                            <td class="value uppercase">{{ $entrega->estudiante->apellidos ?? '' }}, {{ $entrega->estudiante->nombres ?? '' }}</td>
                            <td class="label">Año escolar:</td>
                            <td class="value">{{ $entrega->anioEscolar->anio ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Nivel:</td>
                            <td class="value">{{ ucfirst($entrega->estudiante->nivel ?? '—') }}</td>
                            <td class="label">Grado:</td>
                            <td class="value">{{ $entrega->estudiante->grado ?? '—' }}°</td>
                        </tr>
                        <tr>
                            <td class="label">Sección:</td>
                            <td class="value">{{ strtoupper($entrega->estudiante->seccion ?? '—') }}</td>
                            <td class="label">Fecha de entrega:</td>
                            <td class="value">{{ optional($entrega->fecha_entrega)->format('d/m/Y') }} {{ $entrega->hora_entrega ? 'a las ' . \Carbon\Carbon::parse($entrega->hora_entrega)->format('H:i') : '' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Apoderado --}}
            <div class="data-box">
                <p class="box-title">Recepción</p>
                <div class="box-body">
                    <table class="data-table">
                        <tr>
                            <td class="label">Recibido por:</td>
                            <td class="value">
                                @if($entrega->tipo_firmante === 'apoderado' && $entrega->padre)
                                    <strong class="uppercase">{{ $entrega->padre->nombre_completo }}</strong>
                                    (DNI: {{ $entrega->padre->dni ?? '—' }})
                                @else
                                    <strong>El propio estudiante</strong>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Tabla de libros entregados --}}
            <div class="data-box">
                <p class="box-title">Relación de Textos Entregados</p>
                <div class="box-body">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">N°</th>
                                <th style="width: 16%;">Código del libro</th>
                                <th style="width: 49%;">Nombre del libro</th>
                                <th style="width: 14%;" class="text-center">Estado</th>
                                <th style="width: 16%;">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entrega->detalles as $index => $detalle)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $detalle->libro->codigo_libro ?? '—' }}</td>
                                    <td>{{ $detalle->libro->nombre ?? 'Libro no encontrado' }}</td>
                                    <td class="text-center">{{ ucfirst($detalle->estado_entrega ?? 'entregado') }}</td>
                                    <td>{{ $detalle->observaciones ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No se registraron libros en esta entrega.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="intro" style="font-size: 8.5pt; margin-top: 1mm;">
                El estudiante y/o apoderado declara haber recibido los textos en el estado indicado y se compromete a devolverlos en las condiciones establecidas por la institución al finalizar el año escolar.
            </p>

            {{-- Firmas --}}
            <div class="signatures">
                <div class="signature">
                    <div class="line"></div>
                    <p class="name">Firma del estudiante / apoderado</p>
                    <p class="role">DNI: {{ $entrega->tipo_firmante === 'apoderado' ? ($entrega->padre->dni ?? '—') : ($entrega->estudiante->dni ?? '—') }}</p>
                </div>
                <div class="signature">
                    <div class="line"></div>
                    <p class="name">Firma del responsable de entrega</p>
                    <p class="role">{{ $entrega->usuarioRegistro->nombres ?? '—' }} {{ $entrega->usuarioRegistro->apellidos ?? '' }}</p>
                </div>
            </div>

            {{-- Sello --}}
            <div style="text-align: center; margin-top: 2mm;">
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
