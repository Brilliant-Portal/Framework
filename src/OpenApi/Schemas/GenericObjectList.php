<?php

namespace BrilliantPortal\Framework\OpenApi\Schemas;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AllOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\AnyOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Not;
use GoldSpecDigital\ObjectOrientedOAS\Objects\OneOf;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class GenericObjectList extends SchemaFactory implements Reusable
{
    /**
     * @return AllOf|OneOf|AnyOf|Not|Schema
     */
    public function build(): SchemaContract
    {
        return Schema::object('GenericObjectList')
            ->properties(
                Schema::array('data')
                    ->items((new GenericObject)->ref())
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
