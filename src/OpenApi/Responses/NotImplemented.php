<?php

namespace BrilliantPortal\Framework\OpenApi\Responses;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class NotImplemented extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::forbidden('NotImplemented')
            ->description('The requested action is not implemented')
            ->content(
                MediaType::json()
                    ->schema(
                        Schema::string('message')->example([
                            'message' => 'Not implemented',
                        ])
                    )
            )
            ->statusCode(501);
    }
}
