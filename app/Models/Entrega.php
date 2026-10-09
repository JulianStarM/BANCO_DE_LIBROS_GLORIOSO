<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Entrega extends Model
{
    protected $table = 'entregas';

    protected $fillable = [
        'codigo_general',
        'id_estudiante',
        'id_padre',
        'id_anio_escolar',
        'id_usuario_registro',
        'tipo_firmante',
        'fecha_entrega',
        'hora_entrega',
        'total_libros',
        'firma_estudiante',
        'firma_apoderado',
        'huella_digital',
        'constancia_pdf',
        'codigo_qr',
        'estado',
        'observaciones',
        'ip_registro',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_entrega' => 'date',
            'total_libros' => 'integer',
        ];
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante');
    }

    public function padre(): BelongsTo
    {
        return $this->belongsTo(PadreApoderado::class, 'id_padre');
    }

    public function anioEscolar(): BelongsTo
    {
        return $this->belongsTo(AnioEscolar::class, 'id_anio_escolar');
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_registro');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleEntrega::class, 'id_entrega');
    }

    public function devoluciones(): HasMany
    {
        return $this->hasMany(Devolucion::class, 'id_entrega');
    }

    /**
     * Scope: Entregas completadas.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    /**
     * Scope: Entregas que tienen al menos un libro pendiente de devolución.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeConPendientes($query)
    {
        return $query->whereHas('detalles', function ($q) {
            $q->pendientes();
        });
    }

    /**
     * Verifica si la entrega tiene al menos una devolución registrada.
     */
    public function tieneDevolucion(): bool
    {
        return $this->devoluciones()->exists();
    }

    /**
     * Verifica si la entrega aún tiene libros pendientes de devolver.
     */
    public function tienePendientes(): bool
    {
        return $this->detalles()->whereHas('devolucionesDetalle', function ($q) {
            $q->where('estado_libro', '!=', 'no_devuelto');
        }, '<', DB::raw('cantidad'))->exists();
    }

    /**
     * Genera un código general único por año escolar.
     *
     * Formato: ENT-{año}-{correlativo de 3 dígitos}
     */
    public static function generarCodigo(int $anio): string
    {
        $maximo = (int) static::where('codigo_general', 'like', "ENT-{$anio}-%")
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(codigo_general, "-", -1) AS UNSIGNED)) as maximo')
            ->value('maximo');

        $secuencia = $maximo + 1;
        $codigo = sprintf('ENT-%d-%03d', $anio, $secuencia);

        while (static::where('codigo_general', $codigo)->exists()) {
            $secuencia++;
            $codigo = sprintf('ENT-%d-%03d', $anio, $secuencia);
        }

        return $codigo;
    }
}
