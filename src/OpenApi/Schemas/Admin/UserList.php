<?php

namespace BrilliantPortal\Framework\OpenApi\Schemas\Admin;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class UserList extends SchemaFactory implements Reusable
{
    public function build(): SchemaContract
    {
        return Schema::object('UserList')
            ->properties(
                Schema::array('data')
                    ->items(User::ref())
                    ->description('List of users'),
                Schema::object('meta')
                    ->properties(
                        Schema::integer('count')
                            ->description('Number of users in response')
                            ->readOnly()
                            ->example(1),
                    ),
            );
    }
}
