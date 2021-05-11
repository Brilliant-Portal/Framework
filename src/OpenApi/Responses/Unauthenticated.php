<?php

namespace BrilliantPortal\Framework\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class Unauthenticated extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::unauthorized('Unathenticated')
            ->description('Invalid or no credentials supplied.')
            ->content(
                MediaType::json()
                    ->schema(
                        Schema::string('message')->example([
                            'message' => 'Unauthenticated.',
                        ])
                    )
            )
            ->statusCode(401);
    }
}
