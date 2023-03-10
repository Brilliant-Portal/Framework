<?php

namespace BrilliantPortal\Framework\OpenApi\Schemas;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class GenericObject extends SchemaFactory implements Reusable
{
    public function build(): SchemaContract
    {
        return Schema::object('GenericObject')
            ->properties(
                Schema::string('type')
                    ->description('Object type')
                    ->readOnly()
                    ->default(null)
                    ->example('Asset'),
                Schema::object('data')
                    ->description('Object data')
                    ->default(null)
                    ->example((object) [
                        'id' => 1,
                        'name' => 'ACME Inc.',
                        'description' => 'A sample description',
                    ]),
            );
    }
}
