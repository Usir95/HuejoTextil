<?php

return [

    // Longitud mínima requerida para la contraseña
    'min' => env('PASSWORD_MIN_LENGTH', 4),

    // Longitud máxima permitida (null = sin límite)
    'max' => env('PASSWORD_MAX_LENGTH', null),

    // Requiere al menos una letra mayúscula y una minúscula
    'mixed_case' => env('PASSWORD_MIXED_CASE', false),

    // Requiere al menos una letra (de cualquier tipo)
    'letters' => env('PASSWORD_LETTERS', false),

    // Requiere al menos un número
    'numbers' => env('PASSWORD_NUMBERS', false),

    // Requiere al menos un símbolo (como @, #, !, etc.)
    'symbols' => env('PASSWORD_SYMBOLS', false),

    // Verifica si la contraseña ha sido comprometida en filtraciones de datos (usa el servicio Have I Been Pwned)
    'uncompromised' => env('PASSWORD_UNCOMPROMISED', false),

    // Número de veces que puede aparecer una contraseña en filtraciones antes de considerarse comprometida
    'threshold' => env('PASSWORD_UNCOMPROMISED_THRESHOLD', 0),

];
