<?php

return [

    'api' => [
        'version' => env('BPORTAL_FRAMEWORK_API_VERSION', 'v1'), // API versioning.
    ],

    'seo' => [
        'should-index' => env('SEARCH_ENGINES_SHOULD_INDEX', 'production' === env('APP_ENV')),
        'block-route-patterns' => array_filter(explode(',', env('SEARCH_ENGINES_BLOCK_PATTERNS', ''))),
    ],

    'telescope' => [
        'prune' => [
            'hours' => env('TELESCOPE_PRUNE_HOURS', 48), // Prune entries older than this many hours.
        ],
    ],
];
