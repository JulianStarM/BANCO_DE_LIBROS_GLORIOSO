<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BibEditorial extends Model
{
    use HasFactory;

    protected $table = 'bib_editoriales';

    protected $fillable = [
        'nombre',
        'pais',
        'estado',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function catalogos(): HasMany
    {
        return $this->hasMany(BibCatalogo::class, 'id_editorial');
    }
}
