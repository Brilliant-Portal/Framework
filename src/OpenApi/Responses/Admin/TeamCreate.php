<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use BrilliantPortal\Framework\OpenApi\Schemas\Admin\Team;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class TeamCreate extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::created('TeamShow')
            ->description('Displays the new team data object.')
            ->content(
                MediaType::json()
                    ->schema(Team::ref())
            )
            ->statusCode(201);
    }
}
