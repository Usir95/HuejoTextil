<?php

namespace Database\Seeders;

use App\Models\Administrador\Sistema\CategoriasModulos;
use App\Models\Administrador\Sistema\Modulos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ModulosSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $admin = Role::findByName('Administrador');

        // Obtener categorías por nombre
        $categoriaAdministrador = CategoriasModulos::where('nombre', 'Administrador')->first();
        $categoriaConfAlmacen = CategoriasModulos::where('nombre', 'Configuracion Almacen')->first();

        $modulos = [
            [
                'nombre' => 'Dashboards',
                'ruta' => 'dashboard',
                'descripcion' => 'Panel principal de la aplicación',
                'icono' => 'fa-solid fa-house',
                'categoria_id' => $categoriaAdministrador->id,
                'roles' => ['Administrador']
            ],
            [
                'nombre' => 'Modulos',
                'ruta' => 'Modulos.index',
                'descripcion' => 'Gestión de los módulos del sistema',
                'icono' => 'fa-solid fa-box',
                'categoria_id' => $categoriaAdministrador->id,
                'roles' => ['Administrador']
            ],
            [
                'nombre' => 'Bitacora',
                'ruta' => 'Bitacora.index',
                'descripcion' => 'Bitacora del sistema',
                'icono' => 'fa-solid fa-list',
                'categoria_id' => $categoriaAdministrador->id,
                'roles' => ['Administrador']
            ],
            [
                'nombre' => 'Roles y permisos',
                'ruta' => 'RolesPermisos.index',
                'descripcion' => 'Gestión de roles y permisos del sistema',
                'icono' => 'fa-solid fa-user-shield',
                'categoria_id' => $categoriaAdministrador->id,
                'roles' => ['Administrador']
            ],
            [
                'nombre' => 'Usuarios',
                'ruta' => 'Usuarios.index',
                'descripcion' => 'Gestión de usuarios del sistema',
                'icono' => 'fa-solid fa-user-gear',
                'categoria_id' => $categoriaAdministrador->id,
                'roles' => ['Administrador']
            ],
            [
                'nombre' => 'Clientes',
                'ruta' => 'Clientes.index',
                'descripcion' => 'Catalogo de clientes',
                'icono' => 'fa-solid fa-users',
                'categoria_id' => $categoriaConfAlmacen->id,
                'roles' => ['Configuracion Almacen']
            ],
            [
                'nombre' => 'Tipos Productos',
                'ruta' => 'TiposProductos.index',
                'descripcion' => 'Catalogo de tipos productos',
                'icono' => 'fa-solid fa-box-open',
                'categoria_id' => $categoriaConfAlmacen->id,
                'roles' => ['Configuracion Almacen']
            ],
            [
                'nombre' => 'Productos',
                'ruta' => 'Productos.index',
                'descripcion' => 'Catalogo de productos',
                'icono' => 'fa-solid fa-box',
                'categoria_id' => $categoriaConfAlmacen->id,
                'roles' => ['Configuracion Almacen']
            ],

            [
                'nombre' => 'Colores',
                'ruta' => 'Colores.index',
                'descripcion' => 'Catalogo de Colores',
                'icono' => 'fa-solid fa-palette',
                'categoria_id' => $categoriaConfAlmacen->id,
                'roles' => ['Configuracion Almacen']
            ],
        ];

        foreach ($modulos as $modulo) {
            $moduloDB = Modulos::create([
                'nombre' => $modulo['nombre'],
                'ruta' => $modulo['ruta'],
                'descripcion' => $modulo['descripcion'],
                'icono' => $modulo['icono'],
                'categoria_modulo_id' => $modulo['categoria_id'],
                'modulo_padre_id' => null,
            ]);

            $permiso = Permission::firstOrCreate(['name' => $modulo['nombre']]);

            foreach ($modulo['roles'] as $rolNombre) {
                Role::findByName($rolNombre)->givePermissionTo($permiso);
            }

            $admin->givePermissionTo($permiso);
        }

    }
}
