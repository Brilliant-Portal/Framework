<?php

namespace BrilliantPortal\Framework\OpenApi\Responses;

use BrilliantPortal\Framework\OpenApi\Schemas\GenericObjectListSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class GenericObjectsListResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::ok('GenericObjectsList')
            ->description('Displays a list of objects.')
            ->content(
                MediaType::json()
                    ->schema(GenericObjectListSchema::ref())
            )
            ->statusCode(200);
    }
}
