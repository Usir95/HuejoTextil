<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers {
    use PasswordValidationRules;

    public function create(array $input): User {
        Validator::make($input, [
            'nombre' => ['required', 'string', 'max:255'],
            'usuario' => ['required', 'string', 'max:255', 'unique:usuarios,usuario'],
            'correo' => ['required', 'string', 'email', 'max:255', 'unique:usuarios,correo'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        return User::create([
            'nombre' => $input['nombre'],
            'usuario' => $input['usuario'],
            'correo' => $input['correo'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
