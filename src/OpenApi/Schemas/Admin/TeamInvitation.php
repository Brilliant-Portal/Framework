<?php

namespace BrilliantPortal\Framework\OpenApi\Schemas\Admin;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Laravel\Jetstream\Jetstream;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class TeamInvitation extends SchemaFactory implements Reusable
{
    /**
     * @return AllOf|OneOf|AnyOf|Not|Schema
     */
    public function build(): SchemaContract
    {
        return Schema::object('TeamInvitation')
            ->properties(
                Schema::integer('id')
                    ->description('Invitation ID')
                    ->readOnly()
                    ->default(null)
                    ->example(123),
                Schema::integer('team_id')
                    ->description('Team ID')
                    ->default(null)
                    ->example(123),
                Schema::string('email')
                    ->description('Email address')
                    ->default(null)
                    ->example('john.doe@example.com'),
                Schema::string('role')
                    ->enum(collect(Jetstream::$roles)->pluck('key'))
                    ->description('Role')
                    ->default(null)
                    ->example('editor'),
                Schema::string('message')
                    ->description('Success message')
                    ->default(null)
                    ->example('Invited john.doe@example.com to ACME with role editor'),
            );
    }
}
