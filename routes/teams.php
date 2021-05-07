<?php

use BrilliantPortal\Framework\Http\Controllers\Web\TeamController;
use BrilliantPortal\Framework\Http\Middleware\EnsureHasNoTeam;
use Illuminate\Support\Facades\Route;

Route::name('brilliant-portal-framework.teams.')
    ->middleware(['web', EnsureHasNoTeam::class])
    ->group(function () {
        Route::get('teams/create-first', [TeamController::class, 'create'])->name('create-first');
        Route::post('teams/create-first', [TeamController::class, 'store'])->name('store-first');

        Route::get('teams/already-invited', [TeamController::class, 'alreadyInvited'])->name('already-invited');
    });
