<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use BrilliantPortal\Framework\OpenApi\Schemas\Admin\TeamInvitation;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class TeamInviteUser extends ResponseFactory implements Reusable
{
    public function build(): Response
    {

        return Response::created('TeamInviteUser')
            ->description('Displays the invitation data object.')
            ->content(
                MediaType::json()
                    ->schema(TeamInvitation::ref())
            )
            ->statusCode(200);
    }
}
