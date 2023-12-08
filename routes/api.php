<?php

use BrilliantPortal\Framework\Framework;
use BrilliantPortal\Framework\Http\Controllers\Api\Admin\TeamController;
use BrilliantPortal\Framework\Http\Controllers\Api\Admin\UserController;
use BrilliantPortal\Framework\Http\Controllers\Api\GenericController;
use BrilliantPortal\Framework\Http\Middleware\EnsureHasTeam;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Jetstream\Features;
use Vyuldashev\LaravelOpenApi\Generator;

/**
 * API routes.
 */
Route::name('api.')
    ->middleware(['api', 'auth:sanctum'])
    ->prefix('api/'.config('brilliant-portal-framework.api.version'))
    ->group(function () {

        /**
         * Admin routes.
         */
        Route::name('admin.')
            ->prefix('admin/')
            ->group(function () {

                Route::apiResource('users', UserController::class);

                if (Features::hasTeamFeatures()) {
                    Route::apiResource('teams', TeamController::class)
                        ->names([
                            'index' => 'teams.api.index',
                            'store' => 'teams.api.store',
                            'destroy' => 'teams.api.destroy',
                            'update' => 'teams.api.update',
                            'show' => 'teams.api.show',
                        ]);

                    Route::post('teams/{teamId}/invitations/', [TeamController::class, 'inviteTeamMember'])->name('teams.invitations.create');
                    Route::delete('teams/{teamId}/invitations/{invitationId}', [TeamController::class, 'cancelTeamMemberInvitation'])->name('teams.invitations.cancel');
                    Route::put('teams/{teamId}/remove/{userId}', [TeamController::class, 'removeUser'])->name('teams.invitations.remove');
                }
            });

        /**
         * Generic routes.
         */
        Route::apiResource('generic-object', GenericController::class)
            ->parameter('generic-object', 'genericObject');
    });

/**
 * OpenAPI documentation.
 */
Route::middleware(['web', 'auth:sanctum', EnsureHasTeam::class, 'can:see-api-docs'])
    ->get('/dashboard/api-documentation', function (Generator $openApi) {
        return Framework::renderWithInertia()
            ? Inertia::render('API/Documentation', ['spec' => $openApi->generate()])
            : view('brilliant-portal-framework::api.documentation', ['spec' => $openApi->generate()]);
    })
    ->name('api.documentation');
