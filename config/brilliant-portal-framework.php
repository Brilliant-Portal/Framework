<?php

return [

    'api' => [
        'version' => env('BPORTAL_FRAMEWORK_API_VERSION', 'v1'), // API versioning.
    ],

    'seo' => [
        'should-index' => env('SEARCH_ENGINES_SHOULD_INDEX', env('APP_ENV') === 'production'),
        'block-route-patterns' => array_filter(explode(',', env('SEARCH_ENGINES_BLOCK_PATTERNS', ''))),
    ],

    'stack' => [
        'inertia' => env('BPORTAL_FRAMEWORK_STACK_INERTIA', null),
        'livewire' => env('BPORTAL_FRAMEWORK_STACK_LIVEWIRE', null),
    ],

    'telescope' => [
        'prune' => [
            'hours' => env('TELESCOPE_PRUNE_HOURS', 48), // Prune entries older than this many hours.
        ],
    ],
];
