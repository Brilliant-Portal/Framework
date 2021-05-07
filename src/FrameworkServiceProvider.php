<?php

namespace BrilliantPortal\Framework;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use BrilliantPortal\Framework\Commands\InstallCommand;
use Illuminate\Console\Scheduling\Schedule;

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
     * Schedule commands.
     *
     * @return void
     * @since 1.0.0
     */
    public function packageBooted()
    {
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('telescope:prune --hours='.config('telescope.prune.hours'))->daily();
        });
    }
}
