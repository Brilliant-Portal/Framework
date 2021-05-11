<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use BrilliantPortal\Framework\OpenApi\Schemas\Admin\TeamList;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class TeamsList extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::ok('TeamsList')
            ->description('Displays a list of all teams.')
            ->content(
                MediaType::json()
                    ->schema(TeamList::ref())
            )
            ->statusCode(200);
    }
}
