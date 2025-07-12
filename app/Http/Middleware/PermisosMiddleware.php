<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Administrador\CategoriasModulos;
use App\Models\Administrador\Sistema\CategoriasModulos as SistemaCategoriasModulos;

class PermisosMiddleware {
    public function handle(Request $request, Closure $next) {
        if (Auth::check()) {
            $User = User::find(Auth::user()->id)->first();
            $Permisos = User::find(Auth::user()->id)->getAllPermissions()->pluck('name');

            // Obtener las categorías y módulos permitidos
            $categoriasModulos = SistemaCategoriasModulos::with(['Modulos' => function ($query) use ($Permisos) {
                $query->whereIn('nombre', $Permisos)->orderBy('nombre');
            }])->get()->filter(fn($cat) => $cat->Modulos->isNotEmpty());

            $rutaActual = $request->route()?->getName();
            $modulosPermitidos = $categoriasModulos->pluck('Modulos')->flatten();

            // ====================== Verificar si puede acceder a la ruta actual ========================
            $accesoPermitido = true;
            if (str_ends_with($rutaActual, 'index')) {
                $accesoPermitido = $modulosPermitidos->contains(fn($modulo) => $modulo->ruta === $rutaActual);
                if (!$accesoPermitido) { //PANTALLA DE BLOQUEO DE PERMISOS
                    // abort(403, 'No tienes permisos de acceso. Contacta al administrador.');
                }
            }

            // Permisos con subniveles como 'modulo.accion'
            $PermisosGranulares = $Permisos->filter(fn($permiso) => str_contains($permiso, '.'));

            // Compartir con Inertia
            Inertia::share([
                'PermisosModulos' => $categoriasModulos,
                'PermisosGranulares' => $PermisosGranulares,
                'modulosPermitidos' => $accesoPermitido,
            ]);
        }

        return $next($request);
    }
}

