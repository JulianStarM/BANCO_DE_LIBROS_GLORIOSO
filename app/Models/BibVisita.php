<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BibVisita extends Model
{
    use HasFactory;

    protected $table = 'bib_visitas';

    protected $fillable = [
        'id_socio',
        'tipo_visitante',
        'nombres',
        'grado',
        'seccion',
        'motivo',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'id_usuario',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'grado' => 'integer',
        ];
    }

    public function socio(): BelongsTo
    {
        return $this->belongsTo(BibSocio::class, 'id_socio');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
