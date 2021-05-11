<?php

namespace BrilliantPortal\Framework\OpenApi\RequestBodies;

use BrilliantPortal\Framework\OpenApi\Schemas\GenericObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;

class GenericObjectCreate extends RequestBodyFactory implements Reusable
{
    public function build(): RequestBody
    {
        return RequestBody::create('GenericObjectCreate')
            ->description('Generic object data')
            ->content(
                MediaType::json()
                    ->schema(GenericObject::ref())
            );
    }
}
