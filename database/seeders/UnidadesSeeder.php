<?php

namespace Database\Seeders;

use App\Models\Catalogos\Unidades;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catalogo = [
            [
                'nombre' => 'Kilogramo',
                'Abreviatura' => 'kg',
                'tipo_unidad_id' => 1
            ],
            [
                'nombre' => 'Distancia',
                'Abreviatura' => 'm',
                'tipo_unidad_id' => 2
            ]
        ];

        foreach ($catalogo as $unidad) {
            Unidades::firstOrCreate(
                ['nombre' => $unidad['nombre'], 'abreviatura' => $unidad['Abreviatura'], 'tipo_unidad_id' => $unidad['tipo_unidad_id']]
            );
        }
    }
}
