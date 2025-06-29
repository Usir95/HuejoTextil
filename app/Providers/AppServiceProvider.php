<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider {
    public function register(): void {
        //
    }

    public function boot(): void {
        Password::defaults(function () {
            $config = config('password');

            $rule = Password::min($config['min']);

            if (!empty($config['max'])) {
                $rule->max($config['max']);
            }

            if (!empty($config['mixed_case'])) {
                $rule->mixedCase();
            }

            if (!empty($config['letters'])) {
                $rule->letters();
            }

            if (!empty($config['numbers'])) {
                $rule->numbers();
            }

            if (!empty($config['symbols'])) {
                $rule->symbols();
            }

            if (!empty($config['uncompromised'])) {
                $rule->uncompromised($config['threshold'] ?? 0);
            }

            return $rule;
        });
    }
}

