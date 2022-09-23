<?php

namespace BrilliantPortal\Framework\OpenApi\Parameters;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class GenericObjectTypeWithoutId extends ParametersFactory implements Reusable
{
    public function build(): array
    {
        return [
            Parameter::query()
                ->name('type')
                ->description('Object type')
                ->required()
                ->schema(Schema::string())
                ->example('asset'),
        ];
    }
}
