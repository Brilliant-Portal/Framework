<?php

namespace BrilliantPortal\Framework\OpenApi\Responses;

use BrilliantPortal\Framework\OpenApi\Schemas\GenericObjectSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class GenericObjectCreateResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::created('GenericObjectShow')
            ->description('Displays the new object.')
            ->content(
                MediaType::json()
                    ->schema(GenericObjectSchema::ref())
            )
            ->statusCode(201);
    }
}
