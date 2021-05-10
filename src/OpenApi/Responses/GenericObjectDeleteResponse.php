<?php

namespace BrilliantPortal\Framework\OpenApi\Responses;

use BrilliantPortal\Framework\OpenApi\Schemas\GenericObjectSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Example;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class GenericObjectDeleteResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {

        return Response::ok('GenericObjectDelete')
            ->description('Displays the deleted object.')
            ->content(
                MediaType::json()
                    ->schema(GenericObjectSchema::ref())
            )
            ->statusCode(200);
    }
}
