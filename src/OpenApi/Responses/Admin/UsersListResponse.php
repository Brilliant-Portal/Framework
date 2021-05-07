<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use BrilliantPortal\Framework\OpenApi\Schemas\Admin\UserListSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class UsersListResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::ok('UsersList')
            ->description('Displays a list of all users.')
            ->content(
                MediaType::json()
                    ->schema(UserListSchema::ref())
            )
            ->statusCode(200);
    }
}
