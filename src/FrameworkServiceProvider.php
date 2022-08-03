<?php

namespace BrilliantPortal\Framework;

use App\Models\User;
use BrilliantPortal\Framework\Commands\InstallCommand;
use BrilliantPortal\Framework\Commands\InstallTestsCommand;
use BrilliantPortal\Framework\Commands\PublishBrandingCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Password;
use Laravel\Jetstream\Features;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
                InstallTestsCommand::class,
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
         * Admins.
         */
        Gate::define('super-admin', function (User $user) {
            return $user->is_super_admin;
        });

        /**
         * Passwords.
         */
        Password::defaults(function () {
            $rule = Password::min(8);

            return $this->app->environment('production')
                        ? $rule->mixedCase()->uncompromised()
                        : $rule;
        });

        /**
         * API.
         */
        if (Features::hasApiFeatures()) {
            // Allow super-admins to do anything.
            Gate::before(function (User $user, $ability) {
                if ($user->is_super_admin) {
                    return true;
                }
            });

            Gate::define('see-api-docs', function (User $user) {
                if (Features::hasTeamFeatures()) {
                    return $user->is_super_admin || $user->hasTeamRole($user->currentTeam, 'admin');
                } else {
                    return $user->is_super_admin;
                }
            });

            if (! $this->app->runningInConsole()) {
                Framework::addApiAuthMechanism();
            }
        }

        /**
         * Telescope.
         */
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('telescope:prune --hours=' . config('brilliant-portal-framework.telescope.prune.hours', 48))->daily();
        });
    }
}
