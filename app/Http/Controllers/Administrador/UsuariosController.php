<?php

namespace App\Http\Controllers\Administrador;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Controllers\Controller;
use App\Models\RecursosHumanos\Empleados;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UsuariosController extends Controller {

    use PasswordValidationRules;

    public function index() {
        $Usuarios = User::with('roles', 'Empleado')
        ->orderBy('id', 'desc')
        ->get();

        $Roles = $this->Roles();

        return Inertia::render('Administrador/Usuarios', compact('Usuarios', 'Roles'));
    }

    public function Roles() {
        return Role::with('permissions')->get();
    }

    public function store(Request $request) {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:15',
            'correo' => 'nullable|email|max:100|unique:usuarios,correo',
        ]);

        $Usuario = $this->GeneraUsuario();
        $Password = $this->ContraseñaDefault();

        $usuario = User::create([
            'nombre' => Str::upper($request->nombre . ' ' . $request->apellido_paterno . ' ' . $request->apellido_materno),
            'usuario' => $Usuario,
            'correo' => $request->correo,
            'password' => $Password,
        ]);

        // Crear empleado y asociar usuario
        $empleado = Empleados::create([
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'usuario_id' => $usuario->id,
        ]);

        return redirect()->back()->with('success', 'Empleado y usuario creados');
    }

    public function update(Request $request, $id) {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:15',
            'correo' => 'nullable|email|max:100|unique:usuarios,correo,' . $id,
        ]);

        $usuario = User::findOrFail($id);
        $empleado = Empleados::where('usuario_id', $id)->firstOrFail();

        $usuario->update([
            'nombre' => Str::upper($request->nombre . ' ' . $request->apellido_paterno . ' ' . $request->apellido_materno),
            'correo' => $request->correo,
        ]);

        $empleado->update([
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
        ]);

        return redirect()->back()->with('success', 'Empleado y usuario actualizados');
    }

    public function destroy($id) {
        $usuario = User::findOrFail($id);
        $empleado = Empleados::where('usuario_id', $id)->first();

        // Desactivar empleado si existe
        if ($empleado) {
            $empleado->activo = false;
            $empleado->save();
        }

        // Desactivar usuario
        $usuario->activo = false;
        $usuario->save();

        return redirect()->back()->with('success', 'Empleado y usuario desactivados');
    }

    private function ContraseñaDefault($Password = null) {
        $Key = $Password ?? '12345';
        return Hash::make($Key);
    }

    private function GeneraUsuario() {
        // Obtener el último empleado real
        $Empleado = Empleados::with('usuario')
            ->where('id', '>', 10)
            ->orderByDesc('id')
            ->first();

        // Si no hay empleados válidos, comenzamos con Tex01
        if (!$Empleado) {
            return 'TEX01';
        }

        // Si no tiene usuario asociado aún, no se puede continuar
        if (!$Empleado->usuario || !$Empleado->usuario->usuario) {
            return response()->json(['error' => 'El último empleado no tiene usuario asignado'], 400);
        }

        // Extraer el número del último usuario
        $UsuarioAnterior = $Empleado->usuario->usuario;

        if (!preg_match('/^TEX(\d{2,})$/', $UsuarioAnterior, $matches)) {
            return response()->json(['error' => 'Formato de usuario inválido: ' . $UsuarioAnterior], 400);
        }

        $Numero = (int) $matches[1];

        do {
            $Numero++;
            $NuevoUsuario = 'TEX' . ($Numero < 100 ? str_pad($Numero, 2, '0', STR_PAD_LEFT) : $Numero);
        } while (User::where('usuario', $NuevoUsuario)->exists());

        return $NuevoUsuario;
    }

    public function RestartPassword(User $usuario) {
        try {
            User::where('id', $usuario->id)->update([
                'password' => $this->ContraseñaDefault(),
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
        $user = User::findOrFail($validated['id']);
        $role = Role::findById($validated['rol']);
        $user->syncRoles($role);

        return redirect()->back();
    }
}
