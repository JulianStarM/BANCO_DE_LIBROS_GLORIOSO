<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BibUbicacion extends Model
{
    use HasFactory;

    protected $table = 'bib_ubicaciones';

    protected $fillable = [
        'nombre',
        'descripcion',
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

    public function estantes(): HasMany
    {
        return $this->hasMany(BibEstante::class, 'id_ubicacion');
    }
}
