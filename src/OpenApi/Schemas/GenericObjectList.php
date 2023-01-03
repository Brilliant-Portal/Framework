<?php

namespace BrilliantPortal\Framework\OpenApi\Schemas;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class GenericObjectList extends SchemaFactory implements Reusable
{
    public function build(): SchemaContract
    {
        return Schema::object('GenericObjectList')
            ->properties(
                Schema::array('data')
                    ->items(GenericObject::ref())
                    ->description('List of objects'),
                Schema::object('meta')
                    ->properties(
                        Schema::integer('count')
                            ->description('Number of objects in response')
                            ->readOnly()
                            ->example(1),
                    ),
            );
    }
}
