<?php

namespace BrilliantPortal\Framework\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    public $signature = 'brilliant-portal:install
                                        {--stack=livewire : The development stack that should be installed}
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
            copy(__DIR__.'/../../stubs/app/Actions/Fortify/CreateNewUser.php', app_path('Actions/Fortify/CreateNewUser.php'));
            copy(__DIR__.'/../../stubs/app/Models/UserWithTeams.php', app_path('Models/User.php'));

            $this->replaceInFile('@if (Laravel\Jetstream\Jetstream::hasTeamFeatures())', '@if (Laravel\Jetstream\Jetstream::hasTeamFeatures() && Auth::user()->isMemberOfATeam())', resource_path('views/navigation-menu.blade.php'));
        }

        /**
         * Telescope.
         */
        $this->callSilent('telescope:install');

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