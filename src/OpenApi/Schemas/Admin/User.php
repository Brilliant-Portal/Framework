<?php

namespace BrilliantPortal\Framework\OpenApi\Schemas\Admin;

use BrilliantPortal\Framework\Framework;
use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class User extends SchemaFactory implements Reusable
{
    public function build(): SchemaContract
    {
        return Schema::object('User')
            ->properties(...array_merge([
                Schema::integer('id')
                    ->description('User ID')
                    ->readOnly()
                    ->default(null)
                    ->example(123),
                Schema::string('external_id')
                    ->description('Optional ID for an external system')
                    ->default(null)
                    ->example('ABC123'),
                ],
                (Framework::userHasIndividualNameFields()
                    ? [
                        Schema::string('first_name')
                            ->description('First name')
                            ->required()
                            ->default(null)
                            ->example('John'),
                        Schema::string('last_name')
                            ->description('Last name')
                            ->required()
                            ->default(null)
                            ->example('Doe'),
                        Schema::string('name')
                            ->description('Full name')
                            ->readOnly()
                            ->default(null)
                            ->example('John Doe'),
                        ]
                    : [Schema::string('name')
                        ->description('First and last name')
                        ->required()
                        ->default(null)
                        ->example('John Doe')]),
                [
                Schema::string('email')
                    ->format('email')
                    ->description('Email address')
                    ->required()
                    ->default(null)
                    ->example('john.doe@example.com'),
                Schema::string('email_verified_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->description('Timestamp when email was verified')
                    ->readOnly()
                    ->default(null)
                    ->example(now()->subMonth()),
                Schema::string('created_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->description('Timestamp when user was created')
                    ->readOnly()
                    ->default(null)
                    ->example(now()->subMonth()),
                Schema::string('updated_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->description('Timestamp when user was updated')
                    ->readOnly()
                    ->default(null)
                    ->example(now()->subWeek()),
                Schema::string('deleted_at')
                    ->format(Schema::FORMAT_DATE_TIME)
                    ->description('Timestamp when user was deleted')
                    ->readOnly()
                    ->default(null)
                    ->example(null),
                Schema::string('profile_photo_url')
                    ->format('url')
                    ->description('Profile photo URL')
                    ->readOnly()
                    ->default(null)
                    ->example('https://ui-avatars.com/api/?name=John+Doe&color=7F9CF5&background=EBF4FF'),
            ]));
    }
}
