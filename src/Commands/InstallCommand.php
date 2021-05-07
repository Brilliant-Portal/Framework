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

            // Models.
            copy(__DIR__.'/../../stubs/app/Models/UserWithTeams.php', app_path('Models/User.php'));

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
            return \Illuminate\Support\Str::of($user->email)->endsWith(\'@luminfire.com\');
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

            try {
                mkdir(app_path('OpenApi'), 0755);
            } catch (ErrorException $e) {
                // Directory already exists; do nothing.
            }
            copy(__DIR__.'/../../stubs/app/OpenApi/OpenApi.php', app_path('OpenApi/OpenApi.php'));

            $this->replaceInFile('"Database\\\Seeders\\\": "database/seeders/"', '"Database\\\Seeders\\\": "database/seeders/",
            "GoldSpecDigital\\\ObjectOrientedOAS\\\": "app/OpenApi/"', base_path('composer.json'));

            $composer = new Process(['composer', 'dump-autoload']);
            $composer->run();
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
