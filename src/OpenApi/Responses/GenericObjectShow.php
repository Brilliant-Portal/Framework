<?php

namespace BrilliantPortal\Framework\OpenApi\Responses;

use BrilliantPortal\Framework\OpenApi\Schemas\GenericObject;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class GenericObjectShow extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::ok('GenericObjectShow')
            ->description('Displays the object.')
            ->content(
                MediaType::json()
                    ->schema(GenericObject::ref())
            )
            ->statusCode(200);
    }
}
