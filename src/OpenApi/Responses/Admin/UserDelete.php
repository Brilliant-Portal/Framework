<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use BrilliantPortal\Framework\OpenApi\Schemas\Admin\User;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class UserDelete extends ResponseFactory implements Reusable
{
    public function build(): Response
    {

        return Response::ok('UserDelete')
            ->description('Displays the deleted user data object. Models are “soft-deleted” (see the `deleted_at` key) and can be restored if necessary.')
            ->content(
                MediaType::json()
                    ->schema(User::ref())
            )
            ->statusCode(200);
    }
}
