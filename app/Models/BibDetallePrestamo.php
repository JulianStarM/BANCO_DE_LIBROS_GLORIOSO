<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BibDetallePrestamo extends Model
{
    use HasFactory;

    protected $table = 'bib_detalle_prestamos';

    protected $fillable = [
        'id_prestamo',
        'id_ejemplar',
        'estado_devolucion',
        'estado_fisico_salida',
        'estado_fisico_retorno',
        'observaciones',
    ];

    public function prestamo(): BelongsTo
    {
        return $this->belongsTo(BibPrestamo::class, 'id_prestamo');
    }

    public function ejemplar(): BelongsTo
    {
        return $this->belongsTo(BibEjemplar::class, 'id_ejemplar');
    }
}
