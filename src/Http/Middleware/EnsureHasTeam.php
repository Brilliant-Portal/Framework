<?php

namespace BrilliantPortal\Framework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureHasTeam
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::user()->isMemberOfATeam()) {
            return redirect()->route('brilliant-portal-framework.teams.create-first');
        }

        $this->ensureOneOfTheTeamsIsCurrent();

        return $next($request);
    }

    protected function ensureOneOfTheTeamsIsCurrent(): void
    {
        if (! is_null(Auth::user()->current_team_id)) {
            return;
        }

        $firstTeamId = Auth::user()->allTeams()->first()->id;
        Auth::user()->update(['current_team_id' => $firstTeamId]);
    }
}
