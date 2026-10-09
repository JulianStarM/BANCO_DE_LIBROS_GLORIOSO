<?php

use App\Models\Configuracion;
use App\Models\Libro;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $areas = Configuracion::nombresActivos('libros', 'areas', [
            'MATEMÁTICA', 'COMUNICACIÓN', 'CIENCIA Y TECNOLOGÍA', 'PERSONAL SOCIAL',
            'EDUCACIÓN FÍSICA', 'ARTE Y CULTURA', 'INGLÉS',
        ]);

        $editoriales = Configuracion::nombresActivos('libros', 'editoriales', [
            'Santillana', 'Norma', 'Editorial SM', 'Educando',
        ]);

        $tiposMaterial = Configuracion::nombresActivos('libros', 'tipos_material', [
            'libro', 'cuaderno_trabajo', 'guia_docente', 'material_complementario',
        ]);

        $niveles = Configuracion::nombresActivos('libros', 'niveles', ['inicial', 'primaria', 'secundaria']);
        $grados = Configuracion::nombresActivos('libros', 'grados', ['1', '2', '3', '4', '5', '6']);

        $normalizar = function (?string $valor, array $opciones): ?string {
            if ($valor === null) {
                return null;
            }

            $buscado = Str::lower(Str::ascii($valor));

            foreach ($opciones as $opcion) {
                if (Str::lower(Str::ascii($opcion)) === $buscado) {
                    return $opcion;
                }
            }

            return $valor;
        };

        Libro::query()->chunk(200, function ($libros) use ($areas, $editoriales, $tiposMaterial, $niveles, $grados, $normalizar) {
            foreach ($libros as $libro) {
                $cambios = [];

                $nuevoArea = $normalizar($libro->area, $areas);
                if ($nuevoArea !== $libro->area) {
                    $cambios['area'] = $nuevoArea;
                }

                $nuevoEditorial = $normalizar($libro->editorial, $editoriales);
                if ($nuevoEditorial !== $libro->editorial) {
                    $cambios['editorial'] = $nuevoEditorial;
                }

                $nuevoTipo = $normalizar($libro->tipo_material, $tiposMaterial);
                if ($nuevoTipo !== $libro->tipo_material) {
                    $cambios['tipo_material'] = $nuevoTipo;
                }

                $nuevoNivel = $normalizar($libro->nivel, $niveles);
                if ($nuevoNivel !== $libro->nivel) {
                    $cambios['nivel'] = $nuevoNivel;
                }

                $nuevoGrado = $normalizar((string) $libro->grado, $grados);
                if ($nuevoGrado !== (string) $libro->grado) {
                    $cambios['grado'] = $nuevoGrado;
                }

                if ($cambios !== []) {
                    DB::table('libros')->where('id', $libro->id)->update($cambios);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // La normalización de datos existentes no es reversible de forma fiable.
    }
};
