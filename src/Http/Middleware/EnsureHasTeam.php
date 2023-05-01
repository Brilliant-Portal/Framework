<?php

namespace BrilliantPortal\Framework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureHasTeam
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->user()?->isMemberOfATeam()) {
            return redirect()->route('brilliant-portal-framework.teams.create-first');
        }

        $this->ensureOneOfTheTeamsIsCurrent($request);

        return $next($request);
    }

    protected function ensureOneOfTheTeamsIsCurrent(Request $request): void
    {
        if (filled($request->user()?->current_team_id)) {
            return;
        }

        $request->user()->switchTeam($request->user()->allTeams()->first());
    }
}
