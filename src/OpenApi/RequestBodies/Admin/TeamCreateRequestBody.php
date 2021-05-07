<?php

namespace BrilliantPortal\Framework\OpenApi\RequestBodies\Admin;

use BrilliantPortal\Framework\OpenApi\Schemas\Admin\TeamSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\RequestBody;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\RequestBodyFactory;

class TeamCreateRequestBody extends RequestBodyFactory implements Reusable
{
    public function build(): RequestBody
    {
        return RequestBody::create('TeamCreate')
            ->description('Team data')
            ->content(
                MediaType::json()
                    ->schema(TeamSchema::ref())
            );
    }
}
