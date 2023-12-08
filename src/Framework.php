<?php

namespace BrilliantPortal\Framework;

use App\Models\User;
use BrilliantPortal\Framework\OpenApi\SecuritySchemes\apiKey;
use BrilliantPortal\Framework\Traits\HasIndividualNameFields;
use GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityRequirement;
use Illuminate\Support\Str;

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
}
