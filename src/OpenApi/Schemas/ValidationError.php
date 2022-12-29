<?php

namespace BrilliantPortal\Framework\OpenApi\Schemas;

use GoldSpecDigital\ObjectOrientedOAS\Contracts\SchemaContract;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\SchemaFactory;

class ValidationError extends SchemaFactory implements Reusable
{
    public function build(): SchemaContract
    {
        return Schema::object('ValidationError')
            ->properties(
                Schema::string('message')
                    ->example('The given data was invalid.'),
                Schema::object('errors')
                    ->additionalProperties(
                        Schema::array()->items(Schema::string())
                    )
                    ->example(['field' => ['Something is wrong with this field.']])
            );
    }
}
