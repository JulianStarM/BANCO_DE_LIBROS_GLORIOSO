<?php

namespace App\Services;

use App\Models\Entrega;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ConstanciaEntregaService
{
    private string $logoPath;

    public function __construct()
    {
        $this->logoPath = 'file://'.str_replace('\\', '/', resource_path('views/reportes/glorioso.png'));
    }

    /**
     * Genera y devuelve el PDF de constancia de entrega como stream (ver en navegador).
     */
    public function stream(Entrega $entrega): Response
    {
        $pdf = $this->buildPdf($entrega);

        return $pdf->stream("CONSTANCIA_ENTREGA_{$entrega->codigo_general}.pdf");
    }

    /**
     * Genera y descarga el PDF de constancia de entrega.
     */
    public function download(Entrega $entrega): Response
    {
        $pdf = $this->buildPdf($entrega);

        return $pdf->download("CONSTANCIA_ENTREGA_{$entrega->codigo_general}.pdf");
    }

    /**
     * Construye el PDF con DomPDF.
     */
    private function buildPdf(Entrega $entrega): \Barryvdh\DomPDF\PDF
    {
        $entrega->load([
            'estudiante',
            'padre',
            'anioEscolar',
            'usuarioRegistro',
            'detalles.libro',
        ]);

        return Pdf::loadView('pdf.constancia-entrega', [
            'entrega' => $entrega,
            'logo' => $this->logoPath,
            'fechaEmision' => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'portrait');
    }
}
