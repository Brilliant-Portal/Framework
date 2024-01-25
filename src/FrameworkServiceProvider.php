<?php

namespace BrilliantPortal\Framework;

use App\Models\User;
use BrilliantPortal\Framework\Commands\InstallCommand;
use BrilliantPortal\Framework\Commands\InstallTestsCommand;
use BrilliantPortal\Framework\Commands\PublishBrandingCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
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
            ->hasRoutes(array_filter([
                'teams',
                'api',
                app()->environment('local') ? 'dev' : null,
            ]))
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
        Gate::before(function (User $user, $ability) {
            if ($user->is_super_admin) {
                return true;
            }
        });

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
                // FIXME: remove conditional for https://git.luminfire.net/luminfire/products/brilliantportal/brilliant-portal-framework/-/issues/34
                Framework::addApiAuthMechanism();
            }

            if (Features::hasTeamFeatures()) {
                Framework::addOpenApiTag(
                    name: 'Admin: Teams',
                    description: 'A team is owned by a single user; zero or more additional users can be part of a team.',
                );
                Framework::addOpenApiTag(
                    name: 'Admin: Team Management',
                    description: 'Users can be invited or removed from teams.',
                );
            }
            Framework::addOpenApiTag(
                name: 'Admin: Users',
                description: 'Users can belong to zero or more teams. A user may have different roles in different teams determining what capabilities they should have.',
            );
            Framework::addOpenApiTag(
                name: 'Generic Objects',
                description: 'A general-purpose API endpoint for models in this app.',
            );
            Framework::addOpenApiLocation();
        }

        /**
         * Horizon and Telescope.
         */
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            if (filled(config('horizon')) && collect($schedule->events())->map(fn ($event) => Str::of($event->command)->contains('horizon:snapshot'))->filter()->isEmpty()) {
                $schedule->command('horizon:snapshot')->everyFiveMinutes();
            }
            if (filled(config('telescope')) && collect($schedule->events())->map(fn ($event) => Str::of($event->command)->contains('telescope:prune'))->filter()->isEmpty()) {
                $schedule->command('telescope:prune --hours=' . config('brilliant-portal-framework.telescope.prune.hours', 48))->daily();
            }
        });
    }
}
