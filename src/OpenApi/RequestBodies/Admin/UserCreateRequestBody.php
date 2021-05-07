<?php

namespace BrilliantPortal\Framework\OpenApi\RequestBodies\Admin;

use BrilliantPortal\Framework\OpenApi\Schemas\Admin\UserSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;

class UserCreateRequestBody extends RequestBodyFactory implements Reusable
{
    public function build(): RequestBody
    {
        return RequestBody::create('UserCreate')
            ->description('User data')
            ->content(
                MediaType::json()
                    ->schema(UserSchema::ref())
            );
    }
}
