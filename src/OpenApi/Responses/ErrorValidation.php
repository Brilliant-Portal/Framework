<?php

namespace BrilliantPortal\Framework\OpenApi\Responses;

use BrilliantPortal\Framework\OpenApi\Schemas\ValidationError;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class ErrorValidation extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::create('ErrorValidation')
            ->description('The request contained bad data.')
            ->content(
                MediaType::json()
                    ->schema(ValidationError::ref())
            )
            ->statusCode(422);
    }
}
