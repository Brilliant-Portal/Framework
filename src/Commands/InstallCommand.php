<?php

namespace BrilliantPortal\Framework\Commands;

use ErrorException;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    public $signature = 'brilliant-portal:install
                                        {--stack=livewire : The development stack that should be installed}
                                        {--api : Indicates if API support should be installed};
                                        {--teams : Indicates if team support should be installed}';

    public $description = 'Install all of the resources and components';

    protected $changedVendorFiles = [];

    public function handle()
    {
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

        if ($this->option('teams')) {
            // Actions.
            $this->checkFileHash('vendor/laravel/jetstream/stubs/app/Actions/Fortify/CreateNewUser.php', 'a40bf00dd23a574d2515df28ce35496e987c951722c880f0e70605a68f7b2d52');
            copy(__DIR__.'/../../stubs/app/Actions/Fortify/CreateNewUser.stub.php', app_path('Actions/Fortify/CreateNewUser.php'));

            // Migrations and Models.
            $this->checkFileHash('vendor/laravel/jetstream/stubs/app/Models/Team.php', '6da867dcf458b38d313517a529abec1852a4f9ffdeae5ab667004b3de198b53f');
            $this->checkFileHash('vendor/laravel/jetstream/stubs/app/Models/UserWithTeams.php', '7066970d21811528c0a502aefb6da0f616b52b86180b093ecffa665db5a88658');
            copy(__DIR__.'/../../stubs/database/migrations/2015_01_01_000000_add_super_admins.stub.php', base_path('database/migrations/2015_01_01_000000_add_super_admins.php'));
            copy(__DIR__.'/../../stubs/app/Models/Team.stub.php', app_path('Models/Team.php'));
            copy(__DIR__.'/../../stubs/app/Models/UserWithTeams.stub.php', app_path('Models/User.php'));

            // Providers.
            $this->checkFileHash('vendor/laravel/jetstream/stubs/app/Providers/AuthServiceProvider.php', 'f1d80a0c8a3b252187173c08952a4801683aa71136805d8b0ed100b33935fd7b');
            copy(__DIR__.'/../../stubs/app/Providers/AuthServiceProvider.stub.php', app_path('Providers/AuthServiceProvider.php'));

            // Tests.
            copy(__DIR__.'/../../stubs/tests/livewire/EnsureHasNoTeam.stub.php', base_path('tests/Feature/EnsureHasNoTeam.php'));

            // Views.
            $this->replaceInFile('@if (Laravel\Jetstream\Jetstream::hasTeamFeatures())', '@if (Laravel\Jetstream\Jetstream::hasTeamFeatures() && Auth::user()->isMemberOfATeam())', resource_path('views/navigation-menu.blade.php'));
        }

        if ($this->option('api')) {
            $this->replaceInFile('// Features::api(),', 'Features::api(),', config_path('jetstream.php'));
        }

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
        if ($this->confirm('Would you like to run php artisan migrate now?', true)) {
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
         * OpenAPI docs.
         */
        if ($this->option('api')) {
            $this->checkFileHash('vendor/vyuldashev/laravel-openapi/config/openapi.php', '83d3ef3b1887c1e11dace8375abf047c640678944d2b54fa9679a11927879601');
            copy(__DIR__.'/../../stubs/config/openapi.stub.php', config_path('openapi.php'));

            $this->replaceInFile('Jetstream::role(\'admin\', __(\'Administrator\'), [', 'Jetstream::role(\'admin\', __(\'Administrator\'), [
            \'see-api-docs\',', app_path('Providers/JetstreamServiceProvider.php'));

            try {
                mkdir(base_path('tests/Feature/Api'), 0755);
            } catch (ErrorException $e) {
                if ('mkdir(): File exists' !== $e->getMessage()) {
                    throw $e;
                }
            }
            copy(__DIR__.'/../../stubs/tests/Api/DocumentationTest.stub.php', base_path('tests/Feature/Api/DocumentationTest.php'));
            copy(__DIR__.'/../../stubs/tests/Api/V1UsersTest.stub.php', base_path('tests/Feature/Api/V1UsersTest.php'));
            copy(__DIR__.'/../../stubs/tests/Api/V1TeamsTest.stub.php', base_path('tests/Feature/Api/V1TeamsTest.php'));
        }

        /**
         * Dev dependencies.
         */
        $devDependencies = [];
        if ($this->confirm('Would you like to install barryvdh/laravel-ide-helper as a dev dependency?', true)) {
            $devDependencies[] = 'barryvdh/laravel-ide-helper';
        }
        if ($this->confirm('Would you like to install barryvdh/laravel-debugbar as a dev dependency?', true)) {
            $devDependencies[] = 'barryvdh/laravel-debugbar';
        }
        if ($devDependencies) {
            $this->info('Installing dev dependencies…');
            $composer = new Process(array_merge(['composer', 'require', '--dev'], $devDependencies));
            $composer->run();
            if ($composer->isSuccessful()) {
                $this->info($composer->getOutput());
            } else {
                $this->error($composer->getErrorOutput());
            }
        }

        /**
         * Modified vendor files.
         */
        if ($this->changedVendorFiles) {
            $this->warn('Some of the vendor files overridden by BrilliantPortal Framework have been modified. Please open an issue and assign to the appropriate person.');
            $this->warn('Click the link below to start an issue, then copy-and-paste the table into the issue dsecription.');
            $this->newLine();
            $this->line('https://git.luminfire.net/products/brilliantportal/brilliantportal-framework/-/issues/new?issue[title]=Modified vendor files&issue[description]=These vendor files have been modified:');
            $this->table(
                ['Modified Files'],
                array_map(function ($file) {
                    return [$file];
                }, $this->changedVendorFiles)
            );
        }

        $this->info('Done!');
    }

    /**
     * Replace a given string within a given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Check vendor file against known hash.
     *
     * @since 1.1.0
     *
     * @param string $path
     * @param string $expectedHash
     *
     * @return void
     */
    protected function checkFileHash($path, $expectedHash)
    {
        $actualHash = hash('sha256', file_get_contents(base_path($path)));

        if ($actualHash !== $expectedHash) {
            $this->changedVendorFiles[] = $path;
        }
    }
}
