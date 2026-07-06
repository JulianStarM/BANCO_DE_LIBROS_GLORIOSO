<?php

namespace App\Services;

use App\Models\Devolucion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ConstanciaDevolucionService
{
    private string $logoPath;

    public function __construct()
    {
        $this->logoPath = 'file://'.str_replace('\\', '/', resource_path('views/reportes/glorioso.png'));
    }

    /**
     * Genera y devuelve el PDF de constancia de devolución como stream (ver en navegador).
     */
    public function stream(Devolucion $devolucion): Response
    {
        $pdf = $this->buildPdf($devolucion);

        return $pdf->stream("CONSTANCIA_DEVOLUCION_{$devolucion->codigo_general}.pdf");
    }

    /**
     * Genera y descarga el PDF de constancia de devolución.
     */
    public function download(Devolucion $devolucion): Response
    {
        $pdf = $this->buildPdf($devolucion);

        return $pdf->download("CONSTANCIA_DEVOLUCION_{$devolucion->codigo_general}.pdf");
    }

    /**
     * Construye el PDF con DomPDF.
     */
    private function buildPdf(Devolucion $devolucion): \Barryvdh\DomPDF\PDF
    {
        $devolucion->load([
            'estudiante',
            'padre',
            'anioEscolar',
            'usuarioRegistro',
            'entrega',
            'detalles.libro',
        ]);

        return Pdf::loadView('pdf.constancia-devolucion', [
            'devolucion' => $devolucion,
            'logo' => $this->logoPath,
            'fechaEmision' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'portrait');
    }
}
