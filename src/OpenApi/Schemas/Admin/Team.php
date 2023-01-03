<?php

namespace BrilliantPortal\Framework\OpenApi\Schemas\Admin;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class Team extends SchemaFactory implements Reusable
{
    public function build(): SchemaContract
    {
        return Schema::object('Team')
            ->properties(
                Schema::integer('id')
                    ->description('Team ID')
                    ->readOnly()
                    ->default(null)
                    ->example(123),
                Schema::integer('user_id')
                    ->description('User ID of the team owner')
                    ->default(null)
                    ->example(123),
                Schema::string('name')
                    ->description('Team name')
                    ->default(null)
                    ->example('John\'s Team'),
                Schema::boolean('personal_team')
                    ->description('Whether this is a personal team or not')
                    ->default(null)
                    ->example(true),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->description('Timestamp when team was created')
                    ->default(null)
                    ->example(now()->subMonth()),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->description('Timestamp when team was updated')
                    ->default(null)
                    ->example(now()->subWeek()),
            );
    }
}
