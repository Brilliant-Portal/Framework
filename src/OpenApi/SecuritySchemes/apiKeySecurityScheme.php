<?php

namespace BrilliantPortal\Framework\OpenApi\SecuritySchemes;

use GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityScheme;
use Vyuldashev\LaravelOpenApi\Factories\SecuritySchemeFactory;

class apiKeySecurityScheme extends SecuritySchemeFactory
{
    public function build(): SecurityScheme
    {
        return SecurityScheme::create('apiKey')
            ->type(SecurityScheme::TYPE_HTTP)
            ->description('API token')
            ->scheme('bearer')
            ->bearerFormat('bearer');
    }
}
