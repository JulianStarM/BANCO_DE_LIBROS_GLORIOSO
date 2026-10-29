<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BibEjemplar extends Model
{
    use HasFactory;

    protected $table = 'bib_ejemplares';

    protected $fillable = [
        'id_catalogo',
        'id_estante',
        'id_usuario_registro',
        'numero_registro',
        'codigo_ejemplar',
        'fecha_ingreso',
        'procedencia',
        'estado_fisico',
        'estado_disponibilidad',
        'precio_adquisicion',
        'observaciones',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_ingreso' => 'date',
            'precio_adquisicion' => 'decimal:2',
            'numero_registro' => 'integer',
        ];
    }

    public function catalogo(): BelongsTo
    {
        return $this->belongsTo(BibCatalogo::class, 'id_catalogo');
    }

    public function estante(): BelongsTo
    {
        return $this->belongsTo(BibEstante::class, 'id_estante');
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario_registro');
    }

    public function detallePrestamos(): HasMany
    {
        return $this->hasMany(BibDetallePrestamo::class, 'id_ejemplar');
    }

    public function estaDisponible(): bool
    {
        return $this->estado_disponibilidad === 'disponible' && $this->estado_fisico !== 'baja';
    }
}
