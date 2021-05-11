<?php

namespace BrilliantPortal\Framework\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class Forbidden extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::forbidden('Unauthorized')
            ->description('The user has insufficient privileges to complete the requested action.')
            ->content(
                MediaType::json()
                    ->schema(
                        Schema::string('message')->example([
                            'message' => 'Forbidden.',
                        ])
                    )
            )
            ->statusCode(403);
    }
}
