<?php

use BrilliantPortal\Framework\Http\Controllers\Api\Admin\TeamController;
use BrilliantPortal\Framework\Http\Controllers\Api\Admin\UserController;
use Illuminate\Support\Facades\Route;

/**
 * API routes.
 */
Route::name('api.')
    ->middleware(['api', 'auth:sanctum'])
    ->prefix('api/'.config('brilliant-portal-framework.api.version'))
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
