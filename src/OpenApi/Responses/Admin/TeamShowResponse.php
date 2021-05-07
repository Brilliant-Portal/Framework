<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use BrilliantPortal\Framework\OpenApi\Schemas\Admin\TeamSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class TeamShowResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::ok('TeamShow')
            ->description('Displays the team data.')
            ->content(
                MediaType::json()
                    ->schema(TeamSchema::ref())
            )
            ->statusCode(200);
    }
}
