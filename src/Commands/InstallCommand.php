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
            copy(__DIR__.'/../../stubs/app/Actions/Fortify/CreateNewUser.php', app_path('Actions/Fortify/CreateNewUser.php'));

            // Migrations and Models.
            copy(__DIR__.'/../../stubs/database/migrations/2015_01_01_000000_add_super_admins.php', app_path('database/migrations/2015_01_01_000000_add_super_admins.php'));
            copy(__DIR__.'/../../stubs/app/Models/Team.php', app_path('Models/Team.php'));
            copy(__DIR__.'/../../stubs/app/Models/UserWithTeams.php', app_path('Models/User.php'));

            // Providers.
            copy(__DIR__.'/../../stubs/app/Providers/AuthServiceProvider.php', app_path('Providers/AuthServiceProvider.php'));

            // Tests.
            copy(__DIR__.'/../../stubs/tests/livewire/EnsureHasNoTeam.php', base_path('tests/Feature/EnsureHasNoTeam.php'));

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
            copy(__DIR__.'/../../stubs/config/openapi.php', config_path('openapi.php'));

            $this->replaceInFile('Jetstream::role(\'admin\', __(\'Administrator\'), [', 'Jetstream::role(\'admin\', __(\'Administrator\'), [
            \'see-api-docs\',', app_path('Providers/JetstreamServiceProvider.php'));

            copy(__DIR__.'/../../stubs/tests/ApiDocumentationTest.php', base_path('tests/Feature/ApiDocumentationTest.php'));
        }
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
}
