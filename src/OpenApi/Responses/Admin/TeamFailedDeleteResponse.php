<?php

namespace BrilliantPortal\Framework\OpenApi\Responses\Admin;

use GoldSpecDigital\ObjectOrientedOAS\Objects\MediaType;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Response;
use GoldSpecDigital\ObjectOrientedOAS\Objects\Schema;
use Vyuldashev\LaravelOpenApi\Factories\ResponseFactory;
use Vyuldashev\LaravelOpenApi\Contracts\Reusable;

class TeamFailedDeleteResponse extends ResponseFactory implements Reusable
{
    public function build(): Response
    {
        return Response::unprocessableEntity('TeamFailedDelete')
            ->description('Failed to delete team because it contains undeleted child objects.')
            ->content(
                MediaType::json()
                    ->schema(
                        Schema::object()
                            ->properties(
                                Schema::string('message')
                                    ->readOnly()
                                    ->example('This team contains child objects. Please delete those before deleting the team.'),
                            )
                    )
            )
            ->statusCode(422);
    }
}
