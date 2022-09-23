<?php

use Illuminate\Auth\Middleware\Authenticate;

return [

    'collections' => [

        'default' => [

            'info' => [
                'title' => config('app.name'),
                'description' => null,
                'version' => '1.0.0',
                'contact' => [],
            ],

            'servers' => [
                [
                    'url' => config('app.url'),
                    'description' => null,
                    'variables' => [],
                ],
            ],

            'tags' => [

                [
                    'name' => 'Admin: Team',
                    'description' => 'A team is owned by a single user; zero or more additional users can be part of a team.',
                ],

                [
                    'name' => 'Admin: Team Management',
                    'description' => 'Users can be invited or removed from teams.',
                ],

                [
                    'name' => 'Admin: User',
                    'description' => 'Users can belong to zero or more teams. A user may have different roles in different teams determining what capabilities they should have.',
                ],

                [
                    'name' => 'Generic Object',
                    'description' => 'A general-purpose API endpoint for models in this app.',
                ],

            ],

            'security' => [
                [
                    'apiKey' => [],
                ],
            ],

            // Route for exposing specification.
            // Leave uri null to disable.
            'route' => [
                'uri' => '/openapi',
                'middleware' => [
                    'web',
                    Authenticate::class,
                    'can:see-api-docs',
                ],
            ],

            // Register custom middlewares for different objects.
            'middlewares' => [
                'paths' => [
                    //
                ],
                'components' => [
                    //
                ],
            ],

        ],

    ],

    // Directories to use for locating OpenAPI object definitions.
    'locations' => [
        'callbacks' => [
            app_path('OpenApi/Callbacks'),
        ],

        'request_bodies' => [
            app_path('OpenApi/RequestBodies'),
        ],

        'responses' => [
            app_path('OpenApi/Responses'),
        ],

        'schemas' => [
            app_path('OpenApi/Schemas'),
        ],

        'security_schemes' => [
            app_path('OpenApi/SecuritySchemes'),
        ],
    ],

];
