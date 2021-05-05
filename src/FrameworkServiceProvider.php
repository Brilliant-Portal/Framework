<?php

namespace BrilliantPortal\Framework;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use BrilliantPortal\Framework\Commands\FrameworkCommand;

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
            ->name('framework')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_framework_table')
            ->hasCommand(FrameworkCommand::class);
    }
}
