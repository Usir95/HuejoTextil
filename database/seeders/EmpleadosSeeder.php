<?php

namespace Database\Seeders;

use App\Models\RecursosHumanos\Empleados;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmpleadosSeeder extends Seeder {

    public function run(): void {
        // Asegúrate de que el rol 'Administrador' exista
        $rolAdministrador = Role::firstOrCreate(['name' => 'Administrador']);

        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'nombre' => "Administrador {$i}",
                'usuario' => "admin{$i}",
                'correo' => "admin{$i}@huejotextil.com",
                'password' => Hash::make('1234'),
                'correo_verificado_en' => now(),
            ]);

            // Asignar el rol
            $user->assignRole($rolAdministrador);

            Empleados::create([
                'nombre' => "Administrador {$i}",
                'apellido_paterno' => "Apellido{$i}",
                'apellido_materno' => "Materno{$i}",
                'telefono' => '222123456' . $i,
                'correo' => $user->correo,
                'usuario_id' => $user->id,
            ]);
        }
    }
}
