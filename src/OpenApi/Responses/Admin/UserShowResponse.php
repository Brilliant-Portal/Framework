<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use BrilliantPortal\Framework\OpenApi\Schemas\Admin\UserSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class UserShowResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::ok('UserShow')
            ->description('Displays the user data.')
            ->content(
                MediaType::json()
                    ->schema(UserSchema::ref())
            )
            ->statusCode(200);
    }
}
