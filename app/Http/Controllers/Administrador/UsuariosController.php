<?php

namespace App\Http\Controllers\Administrador;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UsuariosController extends Controller {

    use PasswordValidationRules;

    public function index() {
        $Usuarios = User::get();
        return Inertia::render('Administrador/Usuarios', compact('Usuarios'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => $this->passwordRules(),
        ]);


        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Usuario registrado con éxito');
    }

    public function update(Request $request, User $usuario) {
        // return $request;
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|',
            'username' => 'required|string|max:255|',
        ]);

        User::find($request->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
        ]);

        return redirect()->back()->with('success', 'Usuario actualizado con éxito');
    }

    public function destroy($id){
        User::find($id)->delete();
        return redirect()->back()->with('success', 'Usuario eliminado con éxito');
    }

    public function resetPassword(User $usuario) {
        try {
            User::where('id', $usuario->id)->update([
                'password' => Hash::make('12345')
            ]);

            return redirect()->back()->with('success', 'Contraseña restablecida con éxito');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al restablecer la contraseña');
        }
    }

    public function AsignarRolUsuario(Request $request) {
        $validated = $request->validate([
            'id' => 'required',
            'rol' => ['required']
        ]);
        // return $request;
        $user = User::findOrFail($validated['id']);
        $role = Role::findById($validated['rol']);
        $user->syncRoles($role);

        return redirect()->back()->with('success', 'Dato asignado correctamente');
    }
}
