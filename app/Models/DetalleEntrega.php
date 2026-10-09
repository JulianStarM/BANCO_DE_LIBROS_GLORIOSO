<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetalleEntrega extends Model
{
    protected $table = 'detalle_entregas';

    protected $fillable = [
        'id_entrega',
        'id_libro',
        'cantidad',
        'estado_entrega',
        'numero_orden',
        'observaciones',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cantidad' => 'integer',
            'numero_orden' => 'integer',
        ];
    }

    public function entrega(): BelongsTo
    {
        return $this->belongsTo(Entrega::class, 'id_entrega');
    }

    public function libro(): BelongsTo
    {
        return $this->belongsTo(Libro::class, 'id_libro');
    }

    public function detalleDevolucion(): BelongsTo
    {
        return $this->belongsTo(DetalleDevolucion::class, 'id', 'id_detalle_entrega');
    }

    public function devolucionesDetalle(): HasMany
    {
        return $this->hasMany(DetalleDevolucion::class, 'id_detalle_entrega');
    }

    /**
     * Cantidad de ejemplares devueltos (excluyendo los marcados como no devueltos).
     */
    public function cantidadDevuelta(): int
    {
        return $this->devolucionesDetalle()
            ->where('estado_libro', '!=', 'no_devuelto')
            ->count();
    }

    /**
     * Determina si aún falta devolver algún ejemplar.
     */
    public function estaPendiente(): bool
    {
        return $this->cantidadDevuelta() < $this->cantidad;
    }

    /**
     * Scope: detalles de entrega que aún tienen ejemplares pendientes.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopePendientes($query)
    {
        return $query->whereDoesntHave('devolucionesDetalle', function ($q) {
            $q->where('estado_libro', '!=', 'no_devuelto');
        });
    }
}
