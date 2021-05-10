<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use App\Http\Resources\Admin\User as UserResource;
use BrilliantPortal\Framework\OpenApi\Schemas\Admin\UserSchema;
use App\Models\User as UserModel;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Example;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Header;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class UserDeleteResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {

        return Response::ok('UserDelete')
            ->description('Displays the deleted user data object. Models are “soft-deleted” (see the `deleted_at` key) and can be restored if necessary.')
            ->content(
                MediaType::json()
                    ->schema(UserSchema::ref())
            )
            ->statusCode(200);
    }
}
