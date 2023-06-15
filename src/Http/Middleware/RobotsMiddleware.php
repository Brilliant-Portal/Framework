<?php

namespace BrilliantPortal\Framework\Http\Middleware;

use Illuminate\Http\Request;
use Spatie\RobotsMiddleware\RobotsMiddleware as SpatieRobotsMiddleware;

class RobotsMiddleware extends SpatieRobotsMiddleware
{
    protected function shouldIndex(Request $request)
    {
        $shouldIndex = config('brilliant-portal-framework.seo.should-index', 'production' === config('app.env'));

        if (! $shouldIndex) {
            return false;
        }

        $routePatterns = collect(config('brilliant-portal-framework.seo.block-route-patterns', []));

        if ($routePatterns->isEmpty()) {
            return true;
        }

        $shouldIndexRoutePattern = $routePatterns
            ->filter(fn (string $path) => $request->route()->named($path))
            ->isEmpty();

        if (! $shouldIndexRoutePattern) {
            return false;
        }

        return true;
    }
}
