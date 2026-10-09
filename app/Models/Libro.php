<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Libro extends Model
{
    protected $table = 'libros';

    protected $fillable = [
        'codigo_libro',
        'isbn',
        'nombre',
        'tipo_material',
        'area',
        'grado',
        'nivel',
        'editorial',
        'anio_edicion',
        'anio_escolar',
        'id_anio_escolar',
        'cantidad_total',
        'cantidad_disponible',
        'cantidad_entregada',
        'cantidad_devuelta',
        'cantidad_perdida',
        'cantidad_deficiente',
        'cantidad_deteriorado',
        'estado',
        'imagen_portada',
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
            'cantidad_total' => 'integer',
            'cantidad_disponible' => 'integer',
            'cantidad_entregada' => 'integer',
            'cantidad_devuelta' => 'integer',
            'cantidad_perdida' => 'integer',
            'cantidad_deficiente' => 'integer',
            'cantidad_deteriorado' => 'integer',
        ];
    }

    public function anioEscolar(): BelongsTo
    {
        return $this->belongsTo(AnioEscolar::class, 'id_anio_escolar');
    }

    /**
     * Genera un código único para el libro basado en su nombre.
     *
     * Ejemplos:
     *  - Matemática            -> MAT-001
     *  - Comunicación          -> COM-001
     *  - Ciencia y Tecnología  -> CTA-001
     */
    public static function generarCodigo(string $nombre): string
    {
        $prefijo = self::generarPrefijo($nombre);

        $maximo = (int) self::where('codigo_libro', 'like', "{$prefijo}-%")
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(codigo_libro, "-", -1) AS UNSIGNED)) as maximo')
            ->value('maximo');

        $secuencia = $maximo + 1;
        $codigo = sprintf('%s-%03d', $prefijo, $secuencia);

        while (self::where('codigo_libro', $codigo)->exists()) {
            $secuencia++;
            $codigo = sprintf('%s-%03d', $prefijo, $secuencia);
        }

        return $codigo;
    }

    /**
     * Genera un prefijo de 3 letras a partir del nombre del libro.
     */
    private static function generarPrefijo(string $nombre): string
    {
        $normalizado = self::normalizarTexto($nombre);

        $mapa = [
            'MATEMATICA' => 'MAT',
            'COMUNICACION' => 'COM',
            'CIENCIA Y TECNOLOGIA' => 'CTA',
            'CIENCIA Y AMBIENTE' => 'CYA',
            'PERSONAL SOCIAL' => 'PSO',
            'EDUCACION FISICA' => 'EDF',
            'ARTE Y CULTURA' => 'ARC',
            'INGLES' => 'ING',
            'TUTORIA Y ORIENTACION EDUCATIVA' => 'TOE',
            'DESARROLLO PERSONAL CIUDADANIA Y CIVICA' => 'DPC',
            'EDUCACION RELIGIOSA' => 'ERL',
            'CIENCIAS SOCIALES' => 'CSO',
        ];

        if (isset($mapa[$normalizado])) {
            return $mapa[$normalizado];
        }

        $palabras = self::extraerPalabrasSignificativas($normalizado);

        if ($palabras === []) {
            return 'MAT';
        }

        if (count($palabras) === 1) {
            return str_pad(substr($palabras[0], 0, 3), 3, 'X');
        }

        if (count($palabras) === 2) {
            $iniciales = $palabras[0][0].$palabras[1][0];

            return str_pad($iniciales, 3, substr($palabras[1], 1, 2).'X');
        }

        return substr($palabras[0][0].$palabras[1][0].$palabras[2][0], 0, 3);
    }

    /**
     * Extrae palabras significativas del nombre normalizado.
     *
     * @return array<int, string>
     */
    private static function extraerPalabrasSignificativas(string $normalizado): array
    {
        $stopwords = ['Y', 'E', 'O', 'U', 'DE', 'DEL', 'LA', 'LOS', 'LAS', 'EL', 'AL', 'CON', 'POR', 'PARA', 'A', 'EN'];
        $palabras = preg_split('/[^A-Z]+/', $normalizado, -1, PREG_SPLIT_NO_EMPTY);

        return array_values(array_filter($palabras, fn ($palabra) => ! in_array($palabra, $stopwords, true) && strlen($palabra) >= 2));
    }

    /**
     * Normaliza un texto: mayúsculas, sin acentos y sin espacios extra.
     */
    private static function normalizarTexto(string $texto): string
    {
        $texto = Str::ascii(trim($texto));

        return preg_replace('/\s+/', ' ', mb_strtoupper($texto, 'UTF-8'));
    }

    public function detalleEntregas(): HasMany
    {
        return $this->hasMany(DetalleEntrega::class, 'id_libro');
    }

    public function detalleDevoluciones(): HasMany
    {
        return $this->hasMany(DetalleDevolucion::class, 'id_libro');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(InventarioMovimiento::class, 'id_libro');
    }

    public function reposiciones(): HasMany
    {
        return $this->hasMany(ReposicionLibro::class, 'id_libro');
    }

    /**
     * Scope: Libros activos.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope: Con stock disponible.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeConStock($query)
    {
        return $query->where('cantidad_disponible', '>', 0);
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
     * Scope: Filtrar por nivel.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeDeNivel($query, string $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    /**
     * Verifica si hay stock disponible.
     */
    public function tieneStock(int $cantidad = 1): bool
    {
        return $this->cantidad_disponible >= $cantidad;
    }

    /**
     * Porcentaje de libros entregados.
     */
    public function getPorcentajeEntregadoAttribute(): float
    {
        if ($this->cantidad_total === 0) {
            return 0;
        }

        return round(($this->cantidad_entregada / $this->cantidad_total) * 100, 2);
    }
}
