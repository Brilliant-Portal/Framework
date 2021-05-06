<?php

namespace BrilliantPortal\Framework\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureHasNoTeam
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::user()->isMemberOfATeam()) {
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
