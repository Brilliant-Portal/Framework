<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use BrilliantPortal\Framework\OpenApi\Schemas\Admin\TeamSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class TeamDeleteResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {

        return Response::ok('TeamDelete')
            ->description('Displays the the deleted team data object.')
            ->content(
                MediaType::json()
                    ->schema(TeamSchema::ref())
            )
            ->statusCode(200);
    }
}
