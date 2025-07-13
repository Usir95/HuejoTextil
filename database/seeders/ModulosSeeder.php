<?php

namespace Database\Seeders;

use App\Models\Administrador\Sistema\Modulos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ModulosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $rolInicio = Role::findByName('Inicio');
        $rolAdministrador = Role::findByName('Administrador');
        $rolConfAlmacen = Role::findByName('Configuracion Almacen');

        Modulos::create([
            'nombre' => 'Dashboards',
            'ruta' => 'dashboard',
            'descripcion' => 'Panel principal de la aplicación',
            'icono' => 'fa-solid fa-house',
            'categoria_modulo_id' => $rolAdministrador->id,
            'modulo_padre_id' => null,
        ]);

        Permission::create(['name' => 'Dashboards'])->assignRole($rolAdministrador);

        Modulos::create([
            'nombre' => 'Modulos',
            'ruta' => 'Modulos.index',
            'descripcion' => 'Gestión de los módulos del sistema',
            'icono' => 'fa-solid fa-box',
            'categoria_modulo_id' => $rolAdministrador->id,
            'modulo_padre_id' => null,
        ]);

        Permission::create(['name' => 'Modulos'])->assignRole($rolAdministrador);

        Modulos::create([
            'nombre' => 'Bitacora',
            'ruta' => 'Bitacora.index',
            'descripcion' => 'Bitacora del sistema',
            'icono' => 'fa-solid fa-list',
            'categoria_modulo_id' => $rolAdministrador->id,
            'modulo_padre_id' => null,
        ]);

        Permission::create(['name' => 'Bitacora'])->assignRole($rolAdministrador);

        Modulos::create([
            'nombre' => 'Roles y permisos',
            'ruta' => 'RolesPermisos.index',
            'descripcion' => 'Gestión de roles y permisos del sistema',
            'icono' => 'fa-solid fa-user-shield',
            'categoria_modulo_id' => $rolAdministrador->id,
            'modulo_padre_id' => null,
        ]);

        Permission::create(['name' => 'Roles y permisos'])->assignRole($rolAdministrador);

        Modulos::create([
            'nombre' => 'Usuarios',
            'ruta' => 'Usuarios.index',
            'descripcion' => 'Gestión de usuarios del sistema',
            'icono' => 'fa-solid fa-user-gear',
            'categoria_modulo_id' => $rolAdministrador->id,
            'modulo_padre_id' => null,
        ]);

        Permission::create(['name' => 'Usuarios'])->assignRole($rolAdministrador);

        Modulos::create([
            'nombre' => 'Clientes',
            'ruta' => 'Clientes.index',
            'descripcion' => 'Catalogo de clientes',
            'icono' => 'fa-solid fa-users',
            'categoria_modulo_id' => $rolConfAlmacen->id,
            'modulo_padre_id' => null,
        ]);

        Permission::create(['name' => 'Usuarios'])->assignRole($rolConfAlmacen);

        Modulos::create([
            'nombre' => 'Productos',
            'ruta' => 'Productos.index',
            'descripcion' => 'Catalogo de tipos productos',
            'icono' => 'fa-solid fa-boxes-stacked',
            'categoria_modulo_id' => $rolConfAlmacen->id,
            'modulo_padre_id' => null,
        ]);

        Permission::create(['name' => 'Usuarios'])->assignRole($rolConfAlmacen);

    }
}
