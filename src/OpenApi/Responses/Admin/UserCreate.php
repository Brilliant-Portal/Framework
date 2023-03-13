<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use BrilliantPortal\Framework\OpenApi\Schemas\Admin\User;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Example;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class UserCreate extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::created('UserCreate')
            ->description('Displays the new user data object.')
            ->content(
                MediaType::json()
                    ->schema(User::ref())
                    ->example((new Example())->value([
                        'id' => 123,
                        'name' => 'John Doe',
                        'deleted_at' => null,
                    ]))
            )
            ->statusCode(201);
    }
}
