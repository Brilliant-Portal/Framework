<?php

namespace BrilliantPortal\Framework;

use App\Models\User;
use BrilliantPortal\Framework\OpenApi\SecuritySchemes\apiKey;
use BrilliantPortal\Framework\Traits\HasIndividualNameFields;
use GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityRequirement;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Nova\Nova;
use Livewire\Livewire;

class Framework
{
    private static bool $userHasIndividualNameFields;

    public static function addApiAuthMechanism(): void
    {
        config([
            'openapi.collections.default.security' => array_merge(
                config('openapi.collections.default.security', []),
                [SecurityRequirement::create('apiKey')->securityScheme((new apiKey())->build())]
            ),
        ]);
    }

    public static function addOpenApiLocation(string $packagePath = 'brilliant-portal/framework'): array
    {
        $locations = collect(config('openapi.locations', []))->map(function ($paths, $key) use ($packagePath) {
            $newPath = base_path(
                Str::of($key)
                    ->title()
                    ->replace('_', '')
                    ->prepend('vendor/'.trim($packagePath, '/').'/src/OpenApi/')
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

    public static function addOpenApiTag(string $name, string $description)
    {
        $tags = array_merge(
            config('openapi.collections.default.tags', []),
            [
                [
                    'name' => $name,
                    'description' => $description,
                ],
            ],
        );

        config(['openapi.collections.default.tags' => $tags]);

        return $tags;
    }

    public static function userHasIndividualNameFields(): bool
    {
        if (! isset(self::$userHasIndividualNameFields)) {
            self::$userHasIndividualNameFields = array_key_exists(
                HasIndividualNameFields::class,
                class_uses_recursive(User::class)
            );
        }

        return self::$userHasIndividualNameFields;
    }

    public static function renderWithInertia(): bool
    {
        $wantsInertiaOverride = config('brilliant-portal-framework.stack.inertia', false);

        // If both Nova and Livewire are installed, assume that the app is using Livewire.
        if (class_exists(Nova::class) && class_exists(Livewire::class)) {
            return false || $wantsInertiaOverride;
        }

        return class_exists(Inertia::class) || $wantsInertiaOverride;
    }

    public static function renderWithLivewire(): bool
    {
        $wantsLivewireOverride = config('brilliant-portal-framework.stack.livewire', false);

        // If both Nova and Livewire are installed, assume that the app is using Livewire.
        if (class_exists(Nova::class)) {
            $wantsLivewireOverride = true;
        }

        return class_exists(Livewire::class) || $wantsLivewireOverride;
    }
}
