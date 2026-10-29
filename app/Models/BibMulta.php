<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BibMulta extends Model
{
    use HasFactory;

    protected $table = 'bib_multas';

    protected $fillable = [
        'id_prestamo',
        'id_socio',
        'tipo_multa',
        'dias_atraso',
        'monto',
        'estado',
        'fecha_multa',
        'fecha_pago',
        'id_usuario',
        'observaciones',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_multa' => 'date',
            'fecha_pago' => 'date',
            'monto' => 'decimal:2',
            'dias_atraso' => 'integer',
        ];
    }

    public function prestamo(): BelongsTo
    {
        return $this->belongsTo(BibPrestamo::class, 'id_prestamo');
    }

    public function socio(): BelongsTo
    {
        return $this->belongsTo(BibSocio::class, 'id_socio');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
