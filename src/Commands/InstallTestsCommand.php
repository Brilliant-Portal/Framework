<?php

namespace BrilliantPortal\Framework\Commands;

use Laravel\Jetstream\Features;

class InstallTestsCommand extends BaseCommand
{
    public $signature = 'brilliant-portal:install-tests
                                        {--api : Indicates if API tests should be installed};
                                        {--teams : Indicates if team tests should be installed}';

    public $description = 'Install BrilliantPortal Framework tests';

    public function handle()
    {
        if (Features::hasApiFeatures() || $this->option('api')) {
            copy(__DIR__ . '/../../stubs/tests/Api/ConfigCacheTest.stub.php', base_path('tests/Feature/Api/ConfigCacheTest.php'));
            copy(__DIR__ . '/../../stubs/tests/Api/DocumentationTest.stub.php', base_path('tests/Feature/Api/DocumentationTest.php'));
            copy(__DIR__ . '/../../stubs/tests/Api/V1UsersTest.stub.php', base_path('tests/Feature/Api/V1UsersTest.php'));

            if (Features::hasTeamFeatures() || $this->option('teams')) {
                copy(__DIR__ . '/../../stubs/tests/Api/V1TeamsTest.stub.php', base_path('tests/Feature/Api/V1TeamsTest.php'));
            }
        }

        if (Features::hasTeamFeatures() || $this->option('teams')) {
            copy(__DIR__ . '/../../stubs/tests/EnsureHasNoTeamTest.stub.php', base_path('tests/Feature/EnsureHasNoTeamTest.php'));
        }

        copy(__DIR__ . '/../../stubs/tests/DeploymentTest.stub.php', base_path('tests/Feature/DeploymentTest.php'));
        copy(__DIR__ . '/../../stubs/tests/RobotsMiddlewareTest.stub.php', base_path('tests/Feature/RobotsMiddlewareTest.php'));

        $this->info('Copied tests to your app.');

        return 0;
    }
}
