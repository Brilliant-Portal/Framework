<?php

namespace BrilliantPortal\Framework\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Verify team membership.
     *
     * @since 1.1.0
     *
     * @param string $verb One of read, create, update, delete
     */
    protected function checkTeamOwnership(User $user, ?Model $model = null, string $verb = null): bool
    {
        // Super-admins can do anything.
        if ($user->can('super-admin')) {
            return true;
        }

        // If model is not set, respect super-admin.
        if (! $model) {
            return $user->can('super-admin');
        }

        switch (get_class($model)) {
            case Team::class:
            case User::class:
                $permission = 'admin:' . $verb;
                break;

            default:
                $permission = $verb;
                break;
        }


        if (empty($model->getAttributes()) || ! isset($model->team)) {
            // Team won’t be set for new models, so we’ll check the user’s current team instead.
            $teamPermission = $user->hasTeamPermission($user->currentTeam, $permission);
        } elseif (is_a($model, User::class)) {
            // Users are a special case because they aren’t owned by teams.
            // Admin users can modify other users in their team(s).
            $teamPermission = $user->allTeams()->contains($user->currentTeam);
        } else {
            $teamPermission = $user->belongsToTeam($model->team)
                && $user->hasTeamPermission($model->team, $permission);
        }

        // Don’t check API tokens for Nova requests.
        if (class_exists(Laravel\Nova::class) && Str::contains(get_class(Route::current()->getController()), Laravel\Nova::class)) {
            return $teamPermission;
        }

        if ($user->currentAccessToken()) {
            return $teamPermission && $user->tokenCan($permission);
        }

        return $teamPermission;
    }
}
