<?php

namespace BrilliantPortal\Framework;

use BrilliantPortal\Framework\OpenApi\SecuritySchemes\apiKey;
use GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityRequirement;

class Framework
{
    /**
     * Register API Key security scheme.
     *
     * @return void
     */
    public static function addApiAuthMechanism(): void
    {
        config([
            'openapi.collections.default.security' => [
                SecurityRequirement::create('apiKey')->securityScheme((new apiKey())->build()),
            ],
        ]);
    }
}
