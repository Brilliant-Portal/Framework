<?php

namespace BrilliantPortal\Framework;

use App\Models\User;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use BrilliantPortal\Framework\Commands\InstallCommand;
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
            ->hasCommand(InstallCommand::class);
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
        if (Features::teams()) {
            Gate::define('super-admin', function (User $user) {
                return $user->is_super_admin;
            });
        }

        /**
         * API.
         */
        if (Features::api()) {
            Gate::define('see-api-docs', function (User $user) {
                return $user->hasTeamRole($user->currentTeam, 'admin');
            });

            config(['openapi.collections.default.security' => [SecurityRequirement::create()->securityScheme('apiKey')]]);
        }

        /**
         * Telescope.
         */
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('telescope:prune --hours='.config('telescope.prune.hours'))->daily();
        });
    }
}
