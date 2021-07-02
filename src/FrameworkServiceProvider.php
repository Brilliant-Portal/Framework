<?php

namespace BrilliantPortal\Framework;

use App\Models\User;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use BrilliantPortal\Framework\Commands\InstallCommand;
use BrilliantPortal\Framework\Commands\PublishBrandingCommand;
use BrilliantPortal\Framework\OpenApi\SecuritySchemes\apiKey;
use GoldSpecDigital\ObjectOrientedOAS\Objects\SecurityRequirement;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Laravel\Jetstream\Features;

class FrameworkServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('brilliant-portal-framework')
            ->hasConfigFile()
            ->hasRoutes([
                'teams',
                'api',
            ])
            ->hasViews()
            ->hasCommands(
                InstallCommand::class,
                PublishBrandingCommand::class,
            );
    }

    /**
     * Run after package has booted.
     *
     * @since 0.1.0
     *
     * @return void
     */
    public function packageBooted()
    {

        /**
         * Teams.
         */
        if (Features::hasTeamFeatures()) {
            Gate::define('super-admin', function (User $user) {
                return $user->is_super_admin;
            });
        }

        /**
         * API.
         */
        if (Features::hasApiFeatures()) {
            Gate::define('see-api-docs', function (User $user) {
                if (Features::teams()) {
                    return $user->is_super_admin || $user->hasTeamRole($user->currentTeam, 'admin');
                } else {
                    return $user->is_super_admin;
                }
            });

            if (! $this->app->runningInConsole()) {
                config(['openapi.collections.default.security' => [
                    SecurityRequirement::create('apiKey')->securityScheme(apiKey::class),
                ]]);
            }
        }

        /**
         * Telescope.
         */
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('telescope:prune --hours='.config('brilliant-portal-framework.telescope.prune.hours', 48))->daily();
        });
    }
}
