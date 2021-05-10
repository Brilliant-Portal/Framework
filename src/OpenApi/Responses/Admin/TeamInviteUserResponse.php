<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use BrilliantPortal\Framework\OpenApi\Schemas\Admin\TeamInvitationSchema;
use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class TeamInviteUserResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {

        return Response::created('TeamInviteUser')
            ->description('Displays the invitation data object.')
            ->content(
                MediaType::json()
                    ->schema(TeamInvitationSchema::ref())
            )
            ->statusCode(200);
    }
}
