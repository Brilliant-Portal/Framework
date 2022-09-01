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

        copy(__DIR__ . '/../../stubs/database/migrations/2015_01_01_000000_add_super_admins.stub.php', base_path('database/migrations/2015_01_01_000000_add_super_admins.php'));

        if ($this->option('teams')) {
            // Actions.
            $this->checkFileHash('vendor/laravel/jetstream/stubs/app/Actions/Fortify/CreateNewUser.php', 'a40bf00dd23a574d2515df28ce35496e987c951722c880f0e70605a68f7b2d52');
            copy(__DIR__ . '/../../stubs/app/Actions/Fortify/CreateNewUser.stub.php', app_path('Actions/Fortify/CreateNewUser.php'));

            // Migrations and Models.
            $this->checkFileHash('vendor/laravel/jetstream/stubs/app/Models/UserWithTeams.php', 'f357b9f1253bf3320c19dd506cf86bef2ad56c2851542bb14000c238ff97be80');
            copy(__DIR__ . '/../../stubs/app/Models/UserWithTeams.stub.php', app_path('Models/User.php'));

            // Providers.
            $this->checkFileHash('vendor/laravel/jetstream/stubs/app/Providers/AuthServiceProvider.php', 'f1d80a0c8a3b252187173c08952a4801683aa71136805d8b0ed100b33935fd7b');
            copy(__DIR__ . '/../../stubs/app/Providers/AuthServiceProvider.stub.php', app_path('Providers/AuthServiceProvider.php'));

            // Views.
            $this->replaceInFile('@if (Laravel\Jetstream\Jetstream::hasTeamFeatures())', '@if (Laravel\Jetstream\Jetstream::hasTeamFeatures() && Auth::user()->isMemberOfATeam())', resource_path('views/navigation-menu.blade.php'));
        }

        if ($this->option('api')) {
            $this->replaceInFile('// Features::api(),', 'Features::api(),', config_path('jetstream.php'));
        }

        /**
         * Tailwind config.
         */
        $this->replaceInFile(
            search: "'./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',",
            replace: "'./vendor/brilliant-portal/framework/resources/views/**/*.blade.php'," . PHP_EOL . "        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',",
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
        if ($this->confirm('Would you like to run npm install && npm run dev now?', true)) {
            $install = new Process(['npm', 'install']);
            $install->run();
            $this->info($install->getOutput());

            $dev = new Process(['npm', 'run', 'dev']);
            $dev->run();
            $this->info($dev->getOutput());
        }

        /**
         * Code style and quality.
         */
        copy(__DIR__ . '/../../stubs/phpcs.xml.dist', base_path('phpcs.xml.dist'));

        /**
         * OpenAPI docs.
         */
        if ($this->option('api')) {
            $this->checkFileHash('vendor/vyuldashev/laravel-openapi/config/openapi.php', '195766f8e3d163dd320baebd2633df4b9e136a13fbb3d1fa790a25d8c297bfb7');
            copy(__DIR__ . '/../../stubs/config/openapi.stub.php', config_path('openapi.php'));

            $this->replaceInFile('Jetstream::role(\'admin\', __(\'Administrator\'), [', 'Jetstream::role(\'admin\', __(\'Administrator\'), [
            \'see-api-docs\',', app_path('Providers/JetstreamServiceProvider.php'));

            try {
                mkdir(base_path('tests/Feature/Api'), 0755);
            } catch (ErrorException $e) {
                if ('mkdir(): File exists' !== $e->getMessage()) {
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
                'vemcogroup/laravel-sparkpost-driver',
            ],
            null,
            null,
            true
        );

        if ($recommendedDependencies && ! Arr::has(array_flip($recommendedDependencies), 'None')) {
            $this->info('Installing dependencies…');
            $composer = new Process(array_merge(['composer', 'require'], $recommendedDependencies));
            $composer->run();
            if ($composer->isSuccessful()) {
                $this->info($composer->getOutput());

                if (Arr::has(array_flip($recommendedDependencies), 'brilliant-packages/betteruptime-laravel')) {
                    $this->appendToEnv('BETTER_UPTIME_HEARTBEAT_URL=');
                }
                if (Arr::has(array_flip($recommendedDependencies), 'vemcogroup/laravel-sparkpost-driver')) {
                    copy(__DIR__ . '/../../stubs/config/mail.stub.php', base_path('config/mail.php'));
                    copy(__DIR__ . '/../../stubs/config/services.stub.php', base_path('config/services.php'));
                    $this->appendToEnv('MAIL_MAILER=sparkpost'.PHP_EOL.'SPARKPOST_SECRET='.PHP_EOL.'MAIL_FROM_ADDRESS=help@{insert sending domain here}'.PHP_EOL.'MAIL_FROM_NAME="${APP_NAME}"');
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

                if (Arr::has(array_flip($devDependencies), 'barryvdh/laravel-debugbar')) {
                    $this->appendToEnv('IGNITION_EDITOR=vscode');
                }
                if (Arr::has(array_flip($devDependencies), 'nunomaduro/larastan')) {
                    copy(__DIR__ . '/../../stubs/phpstan.neon.dist', base_path('phpstan.neon.dist'));
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
            array_filter([
                'None',
                'livewire' === $this->option('stack') ? '@defstudio/vite-livewire-plugin' : null,
            ]),
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
        if ($this->option('with-airdrop')) {
            $this->checkFileHash('vendor/hammerstone/airdrop/config/airdrop.php', 'd69661927e3dfb37fcad0895afff56d76b02c8e222f213e3f9df7a0c6e108416');
            copy(__DIR__ . '/../../stubs/config/airdrop.stub.php', base_path('config/airdrop.php'));
            copy(__DIR__ . '/../../stubs/config/filesystems.stub.php', base_path('config/filesystems.php'));
        }
        copy(__DIR__ . '/../../stubs/vite.config.js', base_path('vite.config.js'));
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
