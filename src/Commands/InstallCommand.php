<?php

namespace BrilliantPortal\Framework\Commands;

use ErrorException;
use Illuminate\Support\Arr;
use PDOException;
use Symfony\Component\Process\Process;

class InstallCommand extends BaseCommand
{
    public $signature = 'brilliant-portal:install
                                        {--stack=livewire : The development stack that should be installed}
                                        {--api : Indicates if API support should be installed};
                                        {--teams : Indicates if team support should be installed}
                                        {--with-airdrop=true : Indicates if the Airdrop package should be installed (recommended when using Vite)}';

    public $description = 'Install all of the resources and components';

    public function handle()
    {
        /**
         * Verify setup.
         */
        try {
            $this->callSilently('db:show');
            $databaseIsConfigured = true;
        } catch (PDOException) {
            $databaseIsConfigured = false;
        }

        /**
         * Jetstream.
         */
        $jetstreamArgs = [
            'stack' => $this->option('stack'),
        ];

        if ($this->option('teams')) {
            $jetstreamArgs['--teams'] = true;
        }

        $this->callSilent('jetstream:install', $jetstreamArgs);

        copy(__DIR__.'/../../stubs/database/migrations/2015_01_01_000000_add_super_admins.stub.php', base_path('database/migrations/2015_01_01_000000_add_super_admins.php'));

        if ($this->option('teams')) {
            // Actions.
            $this->checkFileHash('vendor/laravel/jetstream/stubs/app/Actions/Fortify/CreateNewUser.php', 'de57e52100d4f356a8d98c9e5c56a7c93bcba0e20f8e9b782a2d5574e7249347');
            copy(__DIR__.'/../../stubs/app/Actions/Fortify/CreateNewUser.stub.php', app_path('Actions/Fortify/CreateNewUser.php'));

            // Migrations and Models.
            $this->checkFileHash('vendor/laravel/jetstream/stubs/app/Models/UserWithTeams.php', 'e7aafa6757545b8e757e952e528d03b577395bff2f979452defdd7fbb332a2b7');
            copy(__DIR__.'/../../stubs/app/Models/UserWithTeams.stub.php', app_path('Models/User.php'));

            // Providers.
            copy(__DIR__.'/../../stubs/app/Providers/AuthServiceProvider.stub.php', app_path('Providers/AuthServiceProvider.php'));

            // Views.
            if ($this->option('stack') === 'livewire') {
                $this->replaceInFile('@if (Laravel\Jetstream\Jetstream::hasTeamFeatures())', '@if (Laravel\Jetstream\Jetstream::hasTeamFeatures() && Auth::user()->isMemberOfATeam())', resource_path('views/navigation-menu.blade.php'));
            } elseif ($this->option('stack') === 'inertia') {
                copy(__DIR__.'/../../stubs/resources/js/Pages/Teams/AlreadyInvited.vue', base_path('resources/js/Pages/Teams/AlreadyInvited.vue'));
                copy(__DIR__.'/../../stubs/resources/js/Pages/Teams/CreateFirst.vue', base_path('resources/js/Pages/Teams/CreateFirst.vue'));
                copy(__DIR__.'/../../stubs/resources/js/Pages/Teams/Partials/CreateTeamForm.vue', base_path('resources/js/Pages/Teams/Partials/CreateTeamForm.vue'));
            }
        }

        if ($this->option('api')) {
            $this->replaceInFile('// Features::api(),', 'Features::api(),', config_path('jetstream.php'));

            if ($this->option('stack') === 'inertia') {
                copy(__DIR__.'/../../stubs/resources/js/Pages/API/Documentation.vue', base_path('resources/js/Pages/API/Documentation.vue'));
            }
        }

        /**
         * Tailwind config.
         */
        $this->replaceInFile(
            search: "'./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',",
            replace: "'./vendor/brilliant-portal/framework/resources/views/**/*.blade.php',".PHP_EOL."        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',",
            path: base_path('tailwind.config.js'),
        );

        /**
         * Telescope.
         */
        $this->callSilent('telescope:install');

        $this->replaceInFile('        Gate::define(\'viewTelescope\', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
', '        Gate::define(\'viewTelescope\', function ($user) {
            return $user->can(\'super-admin\');
        });
', app_path('Providers/TelescopeServiceProvider.php'));

        /**
         * Migrations.
         */
        if ($databaseIsConfigured && $this->confirm('Would you like to run php artisan migrate now?', true)) {
            $this->call('migrate');
        }

        /**
         * Assets.
         */
        if ($this->confirm('Would you like to run npm install now?', true)) {
            $install = new Process(['npm', 'install']);
            $install->run();
            $this->info($install->getOutput());
        }

        /**
         * Code style and quality.
         */
        copy(__DIR__.'/../../stubs/phpcs.xml.dist', base_path('phpcs.xml.dist'));

        /**
         * OpenAPI docs.
         */
        if ($this->option('api')) {
            $this->replaceInFile('Jetstream::role(\'admin\', __(\'Administrator\'), [', 'Jetstream::role(\'admin\', __(\'Administrator\'), [
            \'see-api-docs\',', app_path('Providers/JetstreamServiceProvider.php'));

            try {
                mkdir(base_path('tests/Feature/Api'), 0755);
            } catch (ErrorException $e) {
                if ($e->getMessage() !== 'mkdir(): File exists') {
                    throw $e;
                }
            }

            $this->call('brilliant-portal:install-tests', array_filter([
                '--api' => $this->option('api'),
                '--teams' => $this->option('teams'),
            ]));
        }

        /**
         * Recommended PHP dependencies.
         */
        $recommendedDependencies = $this->choice(
            'Choose the dependencies you would like to install separated by commas',
            [
                'None',
                'brilliant-packages/betteruptime-laravel',
                'brilliant-portal/forms',
                'hammerstone/airdrop',
                'league/flysystem-aws-s3-v3',
                'vemcogroup/laravel-sparkpost-driver',
            ],
            null,
            null,
            true
        );

        if ($this->option('with-airdrop') && ! Arr::has(array_flip($recommendedDependencies), 'hammerstone/airdrop')) {
            $recommendedDependencies[] = 'hammerstone/airdrop';
        }

        if (filled($recommendedDependencies) && $recommendedDependencies !== ['None']) {
            $this->info('Installing dependencies…');
            $composer = new Process(array_merge(['composer', 'require'], $recommendedDependencies));
            $composer->run();
            if ($composer->isSuccessful()) {
                $this->info($composer->getOutput());

                if (Arr::has(array_flip($recommendedDependencies), 'brilliant-packages/betteruptime-laravel')) {
                    $this->appendToEnv(PHP_EOL.'BETTER_UPTIME_HEARTBEAT_URL='.PHP_EOL);
                }

                if (Arr::has(array_flip($recommendedDependencies), 'hammerstone/airdrop')) {
                    $this->appendToEnv(PHP_EOL.'AIRDROP_AWS_ACCESS_KEY_ID='.PHP_EOL.'AIRDROP_AWS_SECRET_ACCESS_KEY='.PHP_EOL.'AIRDROP_REMOTE_DIR='.basename(config('app.url')).PHP_EOL);
                }

                if (Arr::has(array_flip($recommendedDependencies), 'vemcogroup/laravel-sparkpost-driver')) {
                    copy(__DIR__.'/../../stubs/config/mail.stub.php', base_path('config/mail.php'));
                    copy(__DIR__.'/../../stubs/config/services.stub.php', base_path('config/services.php'));
                    $this->appendToEnv(PHP_EOL.'MAIL_MAILER=sparkpost'.PHP_EOL.'SPARKPOST_SECRET='.PHP_EOL);

                    $this->replaceInFile(
                        search: 'MAIL_MAILER=smtp'.PHP_EOL.'MAIL_HOST=mailhog'.PHP_EOL.'MAIL_PORT=1025'.PHP_EOL.'MAIL_USERNAME=null'.PHP_EOL.'MAIL_PASSWORD=null'.PHP_EOL.'MAIL_ENCRYPTION=null'.PHP_EOL.'MAIL_FROM_ADDRESS="hello@example.com"'.PHP_EOL.'MAIL_FROM_NAME="${APP_NAME}"',
                        replace: 'MAIL_MAILER=sparkpost'.PHP_EOL.'SPARKPOST_SECRET='.PHP_EOL.'MAIL_FROM_ADDRESS=help@'.basename(config('app.url')).PHP_EOL.'MAIL_FROM_NAME="${APP_NAME}"',
                        path: base_path('.env.example'),
                    );
                }
            } else {
                $this->error($composer->getErrorOutput());
            }
        }

        /**
         * Dev dependencies.
         */
        $devDependencies = $this->choice(
            'Choose the dev dependencies you would like to install separated by commas',
            [
                'None',
                'barryvdh/laravel-ide-helper',
                'barryvdh/laravel-debugbar',
                'brianium/paratest',
                'laravel/pint',
                'nunomaduro/larastan',
                'spatie/invade',
            ],
            null,
            null,
            true
        );

        if ($devDependencies && ! Arr::has(array_flip($devDependencies), 'None')) {
            $this->info('Installing dev dependencies…');
            $composer = new Process(array_merge(['composer', 'require', '--dev'], $devDependencies));
            $composer->run();
            if ($composer->isSuccessful()) {
                $this->info($composer->getOutput());

                $this->appendToEnv('IGNITION_EDITOR=vscode');
                if (Arr::has(array_flip($devDependencies), 'barryvdh/laravel-debugbar')) {
                    $this->appendToEnv('DEBUGBAR_EDITOR=vscode');
                }
                if (Arr::has(array_flip($devDependencies), 'nunomaduro/larastan')) {
                    copy(__DIR__.'/../../stubs/phpstan.neon.dist', base_path('phpstan.neon.dist'));
                }
            } else {
                $this->error($composer->getErrorOutput());
            }
        }

        /**
         * Recommended JS dependencies.
         */
        $recommendedJsDependencies = $this->choice(
            'Choose any additional recommended Javascript dev dependencies you would like to install separated by commas',
            array_filter(array_merge([
                'None',
            ],
                $this->option('stack') === 'livewire' ? ['@defstudio/vite-livewire-plugin'] : [],
                $this->option('stack') === 'inertia' ? ['@headlessui/vue', '@heroicons/vue'] : [],
            )),
            null,
            null,
            true
        );

        if ($recommendedJsDependencies && ! Arr::has(array_flip($recommendedJsDependencies), 'None')) {
            $this->info('Installing dependencies…');
            $npm = new Process(array_merge(['npm', 'install', '--save-dev'], $recommendedJsDependencies));
            $npm->run();
            if ($npm->isSuccessful()) {
                $this->info($npm->getOutput());
            } else {
                $this->error($npm->getErrorOutput());
            }
        }

        /**
         * Vite and assets config.
         */
        if ($this->option('with-airdrop') && file_exists('vendor/hammerstone/airdrop/config/airdrop.php')) {
            $this->checkFileHash('vendor/hammerstone/airdrop/config/airdrop.php', 'd69661927e3dfb37fcad0895afff56d76b02c8e222f213e3f9df7a0c6e108416');
            copy(__DIR__.'/../../stubs/config/airdrop.stub.php', base_path('config/airdrop.php'));
            copy(__DIR__.'/../../stubs/config/filesystems.stub.php', base_path('config/filesystems.php'));
        }
        if ($this->option('stack') === 'livewire') {
            copy(__DIR__.'/../../stubs/vite-livewire.config.js', base_path('vite.config.js'));
        } elseif ($this->option('stack') === 'inertia') {
            copy(__DIR__.'/../../stubs/vite-inertia.config.js', base_path('vite.config.js'));
            copy(__DIR__.'/../../stubs/jsconfig.json', base_path('jsconfig.json'));
        } else {
            copy(__DIR__.'/../../stubs/vite-standard.config.js', base_path('vite.config.js'));
        }
        $this->replaceInFile(
            search: 'localhost.test',
            replace: basename(config('app.url')),
            path: base_path('vite.config.js'),
        );

        $this->maybeDisplayVendorErrors();
        $this->info('Done!');

        if (! $databaseIsConfigured) {
            $this->info('Don’t forget to configure your database and run migrations.');
        }
    }
}
