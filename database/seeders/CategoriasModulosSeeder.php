<?php

namespace Database\Seeders;

use App\Models\Administrador\Sistema\CategoriasModulos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;

class CategoriasModulosSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        // Crear las categorías y asignarlas como roles
        $categoriaInicio = CategoriasModulos::create([
            'nombre' => 'Inicio',
            'icono' => 'fa-solid fa-house',
            'descripcion' => strtoupper('Sección principal de la aplicacion'),
        ]);

        // Crear la categoría como un rol
        Role::create(['name' => $categoriaInicio->nombre]);

        $categoriaAdministracion = CategoriasModulos::create([
            'nombre' => 'Administrador',
            'icono' => 'fa-solid fa-user-secret',
            'descripcion' => strtoupper('Sección para gestionar la administracion del sistema'),
        ]);

        // Crear la categoría como un rol
        Role::create(['name' => $categoriaAdministracion->nombre]);

        $CatAlmacen = CategoriasModulos::create([
            'nombre' => 'Almacenes',
            'icono' => 'fa-solid fa-warehouse',
            'descripcion' => strtoupper('Seccion para la administraccion de almacenes'),
        ]);

        // Crear la categoría como un rol
        Role::create(['name' => $CatAlmacen->nombre]);
    }
}
