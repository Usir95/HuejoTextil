<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;


class UpdateUserProfileInformation implements UpdatesUserProfileInformation {
    public function update(User $user, array $input): void {

        Validator::make($input, [
            'nombre' => ['required', 'string', 'max:255'],
            'correo' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('usuarios', 'correo')->ignore($user->id),
            ],
            'usuario' => [
                'required',
                'string',
                'max:255',
                Rule::unique('usuarios', 'usuario')->ignore($user->id),
            ],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        $campos = [
            'nombre' => $input['nombre'],
            'correo' => $input['correo'],
            'usuario' => $input['usuario'],
        ];

        if (
            $input['correo'] !== $user->correo &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $campos);
        } else {
            $user->forceFill($campos)->save();
        }
    }

    protected function updateVerifiedUser(User $user, array $campos): void {
        $campos['correo_verificado_en'] = null;

        $user->forceFill($campos)->save();

        $user->sendEmailVerificationNotification();
    }
}
