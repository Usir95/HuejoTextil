<?php

namespace App\Http\Controllers\Administrador\Sistema;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;


class RolesPermisosController extends Controller {

    public function index() {
        $Roles = $this->Roles();
        $Permisos = Permission::get();
        $Usuarios = User::get();
        return Inertia::render('Administrador/Sistema/Roles&Permisos', compact('Roles', 'Permisos', 'Usuarios'));
    }

    public function Roles() {
        return Role::with('permissions')->get();
    }
}
