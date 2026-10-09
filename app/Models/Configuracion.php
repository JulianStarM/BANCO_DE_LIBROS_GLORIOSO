<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';

    protected $fillable = [
        'modulo',
        'clave',
        'valor',
        'descripcion',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'valor' => 'array',
        ];
    }

    /**
     * Obtener configuración por módulo y clave.
     */
    public static function obtener(string $modulo, string $clave, mixed $default = null): mixed
    {
        $config = self::where('modulo', $modulo)->where('clave', $clave)->first();

        return $config?->valor ?? $default;
    }

    /**
     * Guardar o actualizar configuración.
     */
    public static function guardar(string $modulo, string $clave, mixed $valor, ?string $descripcion = null): self
    {
        return self::updateOrCreate(
            ['modulo' => $modulo, 'clave' => $clave],
            ['valor' => $valor, 'descripcion' => $descripcion]
        );
    }

    /**
     * Obtiene una lista de items normalizada con id, nombre y estado.
     *
     * @return array<int, array{id: string, nombre: string, estado: string}>
     */
    public static function listaItems(string $modulo, string $clave, array $default = []): array
    {
        $raw = self::obtener($modulo, $clave, $default);

        if (! is_array($raw)) {
            $raw = $default;
        }

        $needsSave = false;
        $items = collect($raw)->map(function ($item) use (&$needsSave) {
            if (is_string($item)) {
                $needsSave = true;

                return ['id' => uniqid(), 'nombre' => $item, 'estado' => 'activo'];
            }

            $item['estado'] ??= 'activo';

            return $item;
        })->values()->all();

        if ($needsSave) {
            self::guardar($modulo, $clave, $items);
        }

        return $items;
    }

    /**
     * Obtiene solo los nombres activos de una lista de items.
     *
     * @return array<int, string>
     */
    public static function nombresActivos(string $modulo, string $clave, array $default = []): array
    {
        return collect(self::listaItems($modulo, $clave, $default))
            ->where('estado', 'activo')
            ->pluck('nombre')
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Obtiene una lista de estudiantes normalizada como items con id, nombre y estado.
     *
     * @return array<int, array{id: string, nombre: string, estado: string}>
     */
    public static function listaEstudiantes(string $clave, array $default = []): array
    {
        return self::listaItems('estudiantes', $clave, $default);
    }

    /**
     * Obtiene solo los nombres activos de una lista de estudiantes.
     *
     * @return array<int, string>
     */
    public static function nombresActivosEstudiantes(string $clave, array $default = []): array
    {
        return self::nombresActivos('estudiantes', $clave, $default);
    }
}
