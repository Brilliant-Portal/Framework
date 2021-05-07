<?php

use BrilliantPortal\Framework\Http\Controllers\Api\Admin\TeamController;
use BrilliantPortal\Framework\Http\Controllers\Api\Admin\UserController;
use BrilliantPortal\Framework\Http\Controllers\Api\GenericController;
use Illuminate\Support\Facades\Route;
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
                Route::apiResource('teams', TeamController::class)
                    ->names([
                        'index' => 'teams.api.index',
                        'store' => 'teams.api.store',
                        'destroy' => 'teams.api.destroy',
                        'update' => 'teams.api.update',
                        'show' => 'teams.api.show',
                    ]);
            });

        /**
         * Generic routes.
         */
        Route::apiResource('generic-object', GenericController::class);
    });

/**
 * OpenAPI documentation.
 */
Route::middleware(['web', 'auth:sanctum', 'can:see-api-docs'])
    ->get('/dashboard/api-documentation', function (Generator $generator) {
        return view('brilliant-portal-framework::api.documentation', ['spec' => $generator->generate()]);
    })
    ->name('api.documentation');
