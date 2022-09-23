<?php

namespace BrilliantPortal\Framework\Traits;

use Illuminate\Support\Str;

trait HasOpenApiDefinitions
{
    protected function addOpenApiLocations(string $packagePath = 'brilliant-portal/framework'): array
    {
        $locations = collect(config('openapi.locations', []))->map(function ($paths, $key) use ($packagePath) {
            $paths[] = base_path(
                Str::of($key)
                    ->title()
                    ->replace('_', '')
                    ->prepend('vendor/'. trim($packagePath, '/').'/src/OpenApi/')
            );
            return $paths;
        });

        config(['openapi.locations' => $locations]);

        return $locations->toArray();
    }
}
