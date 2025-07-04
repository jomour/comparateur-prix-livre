<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Analytics Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for various analytics and tracking services
    |
    */

    'google_analytics' => [
        'enabled' => env('GOOGLE_ANALYTICS_ENABLED', false),
        'tracking_id' => env('GOOGLE_ANALYTICS_ID'),
        'gtag_id' => env('GOOGLE_ANALYTICS_GTAG_ID'),
    ],

    'google_tag_manager' => [
        'enabled' => env('GOOGLE_TAG_MANAGER_ENABLED', false),
        'container_id' => env('GOOGLE_TAG_MANAGER_ID'),
    ],

    'facebook_pixel' => [
        'enabled' => env('FACEBOOK_PIXEL_ENABLED', false),
        'pixel_id' => env('FACEBOOK_PIXEL_ID'),
    ],

    'hotjar' => [
        'enabled' => env('HOTJAR_ENABLED', false),
        'site_id' => env('HOTJAR_SITE_ID'),
    ],

    'clarity' => [
        'enabled' => env('CLARITY_ENABLED', false),
        'project_id' => env('CLARITY_PROJECT_ID'),
    ],
]; 