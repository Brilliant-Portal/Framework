<?php

namespace BrilliantPortal\Framework\OpenApi\Parameters\Admin;

use GoldSpecDigital\ObjectOrientedOAS\Objects\Parameter;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ParametersFactory;

class TeamInviteUserParameters extends ParametersFactory implements Reusable
{
    public function build(): array
    {
        return [
            Parameter::path()
                ->name('teamId')
                ->description('Team ID')
                ->required()
                ->schema(Schema::integer())
                ->example(123),
        ];
    }
}
