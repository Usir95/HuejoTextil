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

        $moduloDashboard = Modulos::create([
            'nombre' => 'Dashboards',
            'ruta' => 'dashboard',
            'descripcion' => 'Panel principal de la aplicación',
            'icono' => 'fa-solid fa-house',
            'categoria_modulo_id' => $rolAdministrador->id,
            'modulo_padre_id' => null,
        ]);

        Permission::create(['name' => 'Dashboards'])->assignRole($rolAdministrador);

        $moduloModulos = Modulos::create([
            'nombre' => 'Bitacora',
            'ruta' => 'Bitacora.index',
            'descripcion' => 'Bitacora del sistema',
            'icono' => 'fa-solid fa-list',
            'categoria_modulo_id' => $rolAdministrador->id,
            'modulo_padre_id' => null,
        ]);

        Permission::create(['name' => 'Bitacora'])->assignRole($rolAdministrador);

        $moduloRolesPermisos = Modulos::create([
            'nombre' => 'Roles y permisos',
            'ruta' => 'RolesPermisos.index',
            'descripcion' => 'Gestión de roles y permisos del sistema',
            'icono' => 'fa-solid fa-user-shield',
            'categoria_modulo_id' => $rolAdministrador->id,
            'modulo_padre_id' => null,
        ]);

        Permission::create(['name' => 'Roles y permisos'])->assignRole($rolAdministrador);

        $moduloUsuarios = Modulos::create([
            'nombre' => 'Usuarios',
            'ruta' => 'Usuarios.index',
            'descripcion' => 'Gestión de usuarios del sistema',
            'icono' => 'fa-solid fa-user-gear',
            'categoria_modulo_id' => $rolAdministrador->id,
            'modulo_padre_id' => null,
        ]);

        Permission::create(['name' => 'Usuarios'])->assignRole($rolAdministrador);
    }
}
