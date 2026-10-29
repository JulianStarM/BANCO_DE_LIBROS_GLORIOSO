<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BibEstante extends Model
{
    use HasFactory;

    protected $table = 'bib_estantes';

    protected $fillable = [
        'id_ubicacion',
        'codigo_estante',
        'nombre',
        'capacidad_maxima',
        'estado',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
            'capacidad_maxima' => 'integer',
        ];
    }

    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(BibUbicacion::class, 'id_ubicacion');
    }

    public function ejemplares(): HasMany
    {
        return $this->hasMany(BibEjemplar::class, 'id_estante');
    }
}
