<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;

class TeamRemoveUserResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {

        return Response::ok('TeamRemoveUser')
            ->description('Displays information about the removed user.')
            ->content(
                MediaType::json()
                    ->schema(
                        Schema::object('CancelInvitation')
                            ->properties(
                                Schema::integer('team_id')
                                    ->description('Team ID')
                                    ->default(null)
                                    ->example(123),
                                Schema::integer('user_id')
                                    ->description('User ID')
                                    ->default(null)
                                    ->example(123),
                                Schema::string('message')
                                    ->description('Success message')
                                    ->default(null)
                                    ->example('Removed user'),
                            )
                    )
            )
            ->statusCode(200);
    }
}
