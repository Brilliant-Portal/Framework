<?php

namespace BrilliantPortal\Framework\Tests;

use BrilliantPortal\Framework\Traits\HasOpenApiDefinitions;

class FrameworkServiceProviderTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config(['openapi.locations' => [
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
        ]]);
    }

    public function test_open_api_configs_can_be_merged_simple()
    {
        $provider = new Provider();
        $locations = $provider->test();

        $this->assertEquals(
            expected: [
                app_path('OpenApi/Callbacks'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/Callbacks'),
            ],
            actual: $locations['callbacks'],
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/RequestBodies'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/RequestBodies'),
            ],
            actual: $locations['request_bodies'],
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/Responses'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/Responses'),
            ],
            actual: $locations['responses'],
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/Schemas'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/Schemas'),
            ],
            actual: $locations['schemas'],
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/SecuritySchemes'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/SecuritySchemes'),
            ],
            actual: $locations['security_schemes'],
        );
    }

    public function test_open_api_configs_can_be_merged_with_path()
    {
        $provider = new Provider();
        $locations = $provider->test();
        $locations = $provider->testWithPath('brilliant-portal/package');

        $this->assertEquals(
            expected: [
                app_path('OpenApi/Callbacks'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/Callbacks'),
                base_path('vendor/brilliant-portal/package/src/OpenApi/Callbacks'),
            ],
            actual: $locations['callbacks'],
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/RequestBodies'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/RequestBodies'),
                base_path('vendor/brilliant-portal/package/src/OpenApi/RequestBodies'),
            ],
            actual: $locations['request_bodies'],
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/Responses'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/Responses'),
                base_path('vendor/brilliant-portal/package/src/OpenApi/Responses'),
            ],
            actual: $locations['responses'],
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/Schemas'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/Schemas'),
                base_path('vendor/brilliant-portal/package/src/OpenApi/Schemas'),
            ],
            actual: $locations['schemas'],
        );
        $this->assertEquals(
            expected: [
                app_path('OpenApi/SecuritySchemes'),
                base_path('vendor/brilliant-portal/framework/src/OpenApi/SecuritySchemes'),
                base_path('vendor/brilliant-portal/package/src/OpenApi/SecuritySchemes'),
            ],
            actual: $locations['security_schemes'],
        );
    }
}

class Provider
{
    use HasOpenApiDefinitions;

    public function test(): array
    {
        return $this->addOpenApiLocations();
    }

    public function testWithPath(string $path): array
    {
        return $this->addOpenApiLocations($path);
    }
}
