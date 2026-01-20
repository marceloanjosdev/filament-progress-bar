<?php

return [
    'cache_store' => null,

    'key_prefix' => 'filament-progress-bar',

    'ttl_seconds' => 3600,

    'display' => [
        /*
         | Available options:
         | - 'percent'  → 75%
         | - 'total'    → 75/100
         | - 'both'     → 75/100 · 75%
         */
        'meta' => 'both',
    ],

    'polling' => [
        'enabled' => true,

        // When there are no bars
        'idle_interval_ms' => 5000,

        // When there is at least one bar
        'active_interval_ms' => 1000,

        // Route & middleware for the internal endpoint used by the Filament UI
        'route' => '/filament-progress-bar/progress',
        'middleware' => ['web', 'auth'],
    ],
];
