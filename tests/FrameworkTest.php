<?php

namespace BrilliantPortal\Framework\Tests;

use BrilliantPortal\Framework\Framework;

class FrameworkTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config([
            'openapi' => [
                'collections' => [
                    'default' => [
                        'tags' => [
                            ['name' => 'Existing tag name', 'description' => 'Existing tag description'],
                        ],
                    ],
                ],

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
            ],
        ]);
    }

    public function test_open_api_configs_can_be_merged_with_default_path()
    {
        Framework::addOpenApiLocation();

        $this->assertEquals(
            expected: [
                app_path('OpenApi/Callbacks'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/Callbacks'),
            ],
            actual: config('openapi.locations.callbacks'),
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/RequestBodies'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/RequestBodies'),
            ],
            actual: config('openapi.locations.request_bodies'),
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/Responses'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/Responses'),
            ],
            actual: config('openapi.locations.responses'),
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/Schemas'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/Schemas'),
            ],
            actual: config('openapi.locations.schemas'),
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/SecuritySchemes'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/SecuritySchemes'),
            ],
            actual: config('openapi.locations.security_schemes'),
        );
    }

    public function test_open_api_configs_can_be_merged_with_custom_path()
    {
        Framework::addOpenApiLocation();
        Framework::addOpenApiLocation('brilliant-portal/package');

        $this->assertEquals(
            expected: [
                app_path('OpenApi/Callbacks'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/Callbacks'),
                base_path('vendor/brilliant-portal/package/src/OpenApi/Callbacks'),
            ],
            actual: config('openapi.locations.callbacks'),
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/RequestBodies'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/RequestBodies'),
                base_path('vendor/brilliant-portal/package/src/OpenApi/RequestBodies'),
            ],
            actual: config('openapi.locations.request_bodies'),
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/Responses'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/Responses'),
                base_path('vendor/brilliant-portal/package/src/OpenApi/Responses'),
            ],
            actual: config('openapi.locations.responses'),
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/Schemas'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/Schemas'),
                base_path('vendor/brilliant-portal/package/src/OpenApi/Schemas'),
            ],
            actual: config('openapi.locations.schemas'),
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/SecuritySchemes'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/SecuritySchemes'),
                base_path('vendor/brilliant-portal/package/src/OpenApi/SecuritySchemes'),
            ],
            actual: config('openapi.locations.security_schemes'),
        );
    }

    public function test_open_api_tags_can_be_merged()
    {
        Framework::addOpenApiTag('New tag 1', 'New tag description 1');
        Framework::addOpenApiTag('New tag 2', 'New tag description 2');

        $this->assertEquals(
            expected: [
                ['name' => 'Existing tag name', 'description' => 'Existing tag description'],
                ['name' => 'New tag 1', 'description' => 'New tag description 1'],
                ['name' => 'New tag 2', 'description' => 'New tag description 2'],
            ],
            actual: config('openapi.collections.default.tags'),
        );
    }
}
