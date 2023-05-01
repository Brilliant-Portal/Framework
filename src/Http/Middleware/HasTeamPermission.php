<?php

namespace BrilliantPortal\Framework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HasTeamPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (! $request->user()) {
            abort(401, 'Unauthenticated');
        }

        if (! $request->user()->hasTeamPermission($request->user()->currentTeam, $permission)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
