<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supported Languages
    |--------------------------------------------------------------------------
    |
    | This value determines which languages are supported by the application.
    | The locale code should match the route prefix (e.g., 'fr' for '/lfr').
    |
    */
    'supported' => [
        'fr' => [
            'name' => 'Français',
            'native' => 'Français',
            'flag' => '🇫🇷',
        ],
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'flag' => '🇬🇧',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Language
    |--------------------------------------------------------------------------
    |
    | This value determines the default language for the application.
    |
    */
    'default' => 'fr',

    /*
    |--------------------------------------------------------------------------
    | Route Prefix Pattern
    |--------------------------------------------------------------------------
    |
    | This value determines the pattern for localized route prefixes.
    | The {locale} placeholder will be replaced with the language code.
    |
    */
    'route_prefix' => '{locale}',
]; 