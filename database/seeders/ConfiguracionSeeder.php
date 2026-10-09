<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        // === CONFIGURACIÓN DE ESTUDIANTES ===

        Configuracion::guardar(
            'estudiantes',
            'niveles',
            ['inicial', 'primaria', 'secundaria'],
            'Niveles educativos disponibles en el sistema'
        );

        Configuracion::guardar(
            'estudiantes',
            'grados',
            ['1', '2', '3', '4', '5', '6'],
            'Grados disponibles para los estudiantes'
        );

        Configuracion::guardar(
            'estudiantes',
            'secciones',
            ['A', 'B', 'C', 'D', 'E'],
            'Secciones disponibles para los estudiantes'
        );

        Configuracion::guardar(
            'estudiantes',
            'turnos',
            ['mañana', 'tarde'],
            'Turnos disponibles para los estudiantes'
        );

        Configuracion::guardar(
            'estudiantes',
            'campos_obligatorios',
            [
                'dni' => true,
                'nombres' => true,
                'apellidos' => true,
                'sexo' => true,
                'nivel' => true,
                'grado' => true,
                'seccion' => true,
                'codigo_estudiante' => false,
                'direccion' => false,
                'telefono' => false,
                'turno' => false,
                'id_anio_escolar' => false,
            ],
            'Campos obligatorios en el formulario de estudiantes'
        );

        Configuracion::guardar(
            'estudiantes',
            'campos_visibles',
            [
                'codigo_estudiante' => true,
                'dni' => true,
                'nombres' => true,
                'apellidos' => true,
                'sexo' => true,
                'nivel' => true,
                'grado' => true,
                'seccion' => true,
                'turno' => true,
                'direccion' => true,
                'telefono' => true,
                'id_anio_escolar' => true,
                'estado' => true,
            ],
            'Campos visibles en el formulario de estudiantes'
        );
    }
}
