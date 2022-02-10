<?php

namespace BrilliantPortal\Framework\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class ErrorNotFound extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::notFound('NotFound')
            ->description('An object matching the record ID was not found.')
            ->content(
                MediaType::json()
                    ->schema(
                        Schema::object()
                            ->properties(
                                Schema::string('message')->example('No query results for model with that ID.')
                            )
                    )
            )
            ->statusCode(404);
    }
}
