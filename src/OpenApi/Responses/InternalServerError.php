<?php

namespace BrilliantPortal\Framework\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class InternalServerError extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::unprocessableEntity('InternalServerError')
            ->description('Generic failure message due to a server-side error.')
            ->content(
                MediaType::json()
                    ->schema(
                        Schema::object()
                            ->properties(
                                Schema::string('message')
                                    ->readOnly(),
                            )
                    )
            )
            ->statusCode(500);
    }
}
