<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BibPrestamo extends Model
{
    use HasFactory;

    protected $table = 'bib_prestamos';

    protected $fillable = [
        'id_socio',
        'id_usuario_registro',
        'id_usuario_devolucion',
        'codigo_prestamo',
        'tipo_prestamo',
        'fecha_prestamo',
        'hora_prestamo',
        'fecha_limite',
        'fecha_devolucion',
        'hora_devolucion',
        'estado',
        'renovaciones',
        'observaciones',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_prestamo' => 'date',
            'fecha_limite' => 'date',
            'fecha_devolucion' => 'date',
            'renovaciones' => 'integer',
        ];
    }

    public function socio(): BelongsTo
    {
        return $this->belongsTo(BibSocio::class, 'id_socio');
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario_registro');
    }

    public function usuarioDevolucion(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario_devolucion');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(BibDetallePrestamo::class, 'id_prestamo');
    }

    public function multas(): HasMany
    {
        return $this->hasMany(BibMulta::class, 'id_prestamo');
    }

    public function estaVencido(): bool
    {
        if ($this->estado === 'devuelto') {
            return false;
        }

        return $this->fecha_limite !== null && $this->fecha_limite->isPast();
    }
}
