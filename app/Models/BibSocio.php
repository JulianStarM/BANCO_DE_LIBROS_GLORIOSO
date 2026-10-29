<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BibSocio extends Model
{
    use HasFactory;

    protected $table = 'bib_socios';

    protected $fillable = [
        'id_estudiante',
        'id_usuario',
        'tipo_socio',
        'codigo_socio',
        'dni',
        'nombres',
        'apellidos',
        'grado',
        'seccion',
        'telefono',
        'estado',
        'fecha_inscripcion',
        'observaciones',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_inscripcion' => 'date',
            'grado' => 'integer',
        ];
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function prestamos(): HasMany
    {
        return $this->hasMany(BibPrestamo::class, 'id_socio');
    }

    public function multas(): HasMany
    {
        return $this->hasMany(BibMulta::class, 'id_socio');
    }

    public function visitas(): HasMany
    {
        return $this->hasMany(BibVisita::class, 'id_socio');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->apellidos}, {$this->nombres}";
    }
}
