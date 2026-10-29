<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BibCatalogo extends Model
{
    use HasFactory;

    protected $table = 'bib_catalogo';

    protected $fillable = [
        'id_tipo_material',
        'id_area',
        'id_editorial',
        'titulo',
        'subtitulo',
        'isbn',
        'edicion',
        'anio_publicacion',
        'numero_paginas',
        'idioma',
        'descripcion',
        'imagen_portada',
        'estado',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
            'anio_publicacion' => 'integer',
            'numero_paginas' => 'integer',
        ];
    }

    public function tipoMaterial(): BelongsTo
    {
        return $this->belongsTo(BibTipoMaterial::class, 'id_tipo_material');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(BibArea::class, 'id_area');
    }

    public function editorial(): BelongsTo
    {
        return $this->belongsTo(BibEditorial::class, 'id_editorial');
    }

    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(BibAutor::class, 'bib_catalogo_autores', 'id_catalogo', 'id_autor')
            ->withPivot('rol', 'orden')
            ->withTimestamps()
            ->orderByPivot('orden');
    }

    public function ejemplares(): HasMany
    {
        return $this->hasMany(BibEjemplar::class, 'id_catalogo');
    }

    public function ejemplaresDisponibles(): HasMany
    {
        return $this->ejemplares()->where('estado_disponibilidad', 'disponible');
    }
}
