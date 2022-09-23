<?php

namespace BrilliantPortal\Framework\Traits;

use Illuminate\Support\Str;

trait HasOpenApiDefinitions
{
    protected function addOpenApiLocations(string $packagePath = 'brilliant-portal/framework'): array
    {
        $locations = collect(config('openapi.locations', []))->map(function ($paths, $key) use ($packagePath) {
            $newPath = base_path(
                Str::of($key)
                    ->title()
                    ->replace('_', '')
                    ->prepend('vendor/'. trim($packagePath, '/').'/src/OpenApi/')
            );
            if (array_search($newPath, $paths)) {
                return $paths;
            }

            $paths[] = $newPath;
            return $paths;
        })->toArray();

        config(['openapi.locations' => $locations]);

        return $locations;
    }
}
