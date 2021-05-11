<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class TeamCancelInvitationUser extends ResponseFactory implements Reusable
{
    public function build(): Response
    {

        return Response::ok('TeamCancelInvitation')
            ->description('Displays the cancelled invitation object.')
            ->content(
                MediaType::json()
                    ->schema(
                        Schema::object('CancelInvitation')
                            ->properties(
                                Schema::integer('invitation_id')
                                    ->description('Invitation ID')
                                    ->default(null)
                                    ->example(123),
                                Schema::integer('team_id')
                                    ->description('Team ID')
                                    ->default(null)
                                    ->example(123),
                                Schema::string('message')
                                    ->description('Success message')
                                    ->default(null)
                                    ->example('Canceled invitation'),
                            )
                    )
            )
            ->statusCode(200);
    }
}
