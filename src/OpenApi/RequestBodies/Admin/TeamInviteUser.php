<?php

namespace BrilliantPortal\Framework\OpenApi\RequestBodies\Admin;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Laravel\Jetstream\Jetstream;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;

class TeamInviteUser extends RequestBodyFactory implements Reusable
{
    public function build(): RequestBody
    {
        return RequestBody::create('TeamInviteUser')
            ->description('Team data')
            ->content(
                MediaType::json()
                    ->schema(
                        Schema::object('Team')
                            ->properties(
                                Schema::string('email')
                                    ->description('Email address')
                                    ->default(null)
                                    ->example('john.doe@example.com'),
                                Schema::string('role')
                                    ->enum(collect(Jetstream::$roles)->pluck('key'))
                                    ->description('Role')
                                    ->default(null)
                                    ->example('editor'),
                            )
                    )
            );
    }
}
