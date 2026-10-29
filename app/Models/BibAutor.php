<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BibAutor extends Model
{
    use HasFactory;

    protected $table = 'bib_autores';

    protected $fillable = [
        'nombres',
        'apellidos',
        'seudonimo',
        'nacionalidad',
    ];

    public function catalogos(): BelongsToMany
    {
        return $this->belongsToMany(BibCatalogo::class, 'bib_catalogo_autores', 'id_autor', 'id_catalogo')
            ->withPivot('rol', 'orden')
            ->withTimestamps();
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->apellidos}, {$this->nombres}";
    }
}
