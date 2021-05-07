<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use BrilliantPortal\Framework\OpenApi\Schemas\Admin\UserSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Example;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class UserCreateResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::created('UserShow')
            ->description('Displays the new user data object.')
            ->content(
                MediaType::json()
                    ->schema(UserSchema::ref())
                    ->example((new Example())->value([
                        'id' => 123,
                        'name' => 'John Doe',
                        'deleted_at' => null,
                    ]))
            )
            ->statusCode(201);
    }
}
