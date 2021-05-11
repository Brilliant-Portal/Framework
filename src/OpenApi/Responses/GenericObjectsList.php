<?php

namespace BrilliantPortal\Framework\OpenApi\Responses;

use BrilliantPortal\Framework\OpenApi\Schemas\GenericObjectList;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class GenericObjectsList extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::ok('GenericObjectsList')
            ->description('Displays a list of objects.')
            ->content(
                MediaType::json()
                    ->schema(GenericObjectList::ref())
            )
            ->statusCode(200);
    }
}
