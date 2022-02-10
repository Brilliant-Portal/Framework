<?php

namespace BrilliantPortal\Framework\OpenApi\Schemas\Admin;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class TeamList extends SchemaFactory implements Reusable
{
    /**
     * @return AllOf|OneOf|AnyOf|Not|Schema
     */
    public function build(): SchemaContract
    {
        return Schema::object('TeamList')
            ->properties(
                Schema::array('data')
                    ->items((new Team)->ref())
                    ->description('List of teams'),
                Schema::object('meta')
                    ->properties(
                        Schema::integer('count')
                            ->description('Number of teams in response')
                            ->readOnly()
                            ->example(1),
                    ),
            );
    }
}
