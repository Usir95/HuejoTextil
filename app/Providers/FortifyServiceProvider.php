<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider {

    public function register(): void {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        // Registra las acciones personalizadas de Fortify para crear, actualizar y resetear usuarios
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        // Obtiene el campo de login desde config/fortify.php (usualmente 'usuario' o 'correo')
        $username = config('fortify.username');

        // Indica a Fortify que ese será el campo usado para identificar al usuario al iniciar sesión
        Fortify::username($username);

        /* ========================= lOGIN DEL SISTEMA ========================= */
        Fortify::authenticateUsing(function (Request $request) use ($username) {
            Log::info($request->usuario);
            if (Auth::attempt([
                $username => $request->usuario,
                'password' => $request->password
            ], $request->boolean('remember'))) {
                return Auth::user();
            }
        });

        /* ========================= LIMITADOR DE INTENTOS DE LOGIN ========================= */
        RateLimiter::for('login', function (Request $request) use ($username) {
            $loginValue = $request->input($username) ?? '';

            // Genera una clave única para limitar intentos por usuario + IP
            $throttleKey = Str::transliterate($loginValue).'|'.$request->ip();

            // Permite hasta 5 intentos por minuto por combinación de usuario + IP
            return Limit::perMinute(5)->by($throttleKey);
        });

        /* ========================= LIMITADOR DE 2FA ========================= */
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }

}
