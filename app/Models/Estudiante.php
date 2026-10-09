<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    protected $table = 'estudiantes';

    protected $fillable = [
        'id_institucion',
        'id_anio_escolar',
        'codigo_estudiante',
        'dni',
        'nombres',
        'apellidos',
        'sexo',
        'grado',
        'seccion',
        'turno',
        'nivel',
        'direccion',
        'telefono',
        'estado',
        'foto',
        'codigo_qr',
        'observaciones',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'grado' => 'integer',
        ];
    }

    /**
     * Nombre completo del estudiante.
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->apellidos}, {$this->nombres}";
    }

    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class, 'id_institucion');
    }

    public function anioEscolar(): BelongsTo
    {
        return $this->belongsTo(AnioEscolar::class, 'id_anio_escolar');
    }

    public function padres(): BelongsToMany
    {
        return $this->belongsToMany(PadreApoderado::class, 'estudiante_padre', 'id_estudiante', 'id_padre')
            ->withPivot('parentesco', 'es_principal')
            ->withTimestamps();
    }

    public function entregas(): HasMany
    {
        return $this->hasMany(Entrega::class, 'id_estudiante');
    }

    public function devoluciones(): HasMany
    {
        return $this->hasMany(Devolucion::class, 'id_estudiante');
    }

    public function observacionesGenerales(): HasMany
    {
        return $this->hasMany(ObservacionGeneral::class, 'id_estudiante');
    }

    public function reposiciones(): HasMany
    {
        return $this->hasMany(ReposicionLibro::class, 'id_estudiante');
    }

    /**
     * Scope: Filtrar por grado.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeDeGrado($query, int $grado)
    {
        return $query->where('grado', $grado);
    }

    /**
     * Scope: Filtrar por sección.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeDeSeccion($query, string $seccion)
    {
        return $query->where('seccion', $seccion);
    }

    /**
     * Scope: Solo estudiantes activos.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope: Filtrar por año escolar.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeDelAnio($query, int $idAnioEscolar)
    {
        return $query->where('id_anio_escolar', $idAnioEscolar);
    }

    /**
     * Verifica si tiene entregas pendientes de devolución.
     */
    public function tieneEntregasPendientes(): bool
    {
        return $this->entregas()
            ->completadas()
            ->conPendientes()
            ->exists();
    }
}
