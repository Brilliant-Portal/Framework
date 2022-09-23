<?php

namespace BrilliantPortal\Framework\Tests;

use BrilliantPortal\Framework\Traits\HasOpenApiDefinitions;

class ExampleTest extends TestCase
{
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }

    public function test_open_api_configs_can_be_merged()
    {
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

        $provider = new Provider();
        $locations = $provider->test();

        $this->assertContains(
            needle: base_path('vendor/brilliant-portal/framework/src/OpenApi/Callbacks'),
            haystack: $locations['callbacks'],
        );
        $this->assertContains(
            needle: base_path('vendor/brilliant-portal/framework/src/OpenApi/RequestBodies'),
            haystack: $locations['request_bodies'],
        );
        $this->assertContains(
            needle: base_path('vendor/brilliant-portal/framework/src/OpenApi/Responses'),
            haystack: $locations['responses'],
        );
        $this->assertContains(
            needle: base_path('vendor/brilliant-portal/framework/src/OpenApi/Schemas'),
            haystack: $locations['schemas'],
        );
        $this->assertContains(
            needle: base_path('vendor/brilliant-portal/framework/src/OpenApi/SecuritySchemes'),
            haystack: $locations['security_schemes'],
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
