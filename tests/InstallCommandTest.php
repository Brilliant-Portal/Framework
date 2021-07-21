<?php

namespace BrilliantPortal\Framework\Tests;

use BrilliantPortal\Framework\Commands\BaseCommand;
use Illuminate\Filesystem\Filesystem;

class InstallCommandTest extends TestCase
{
    public function test_env_keys()
    {
        $filesystem = new Filesystem();
        $filesystem->put(base_path('.env'), '');
        $filesystem->put(base_path('.env.example'), '');

        $this->assertStringNotContainsString('BETTER_UPTIME_HEARTBEAT_URL', file_get_contents(base_path('.env')));
        $this->assertStringNotContainsString('BETTER_UPTIME_HEARTBEAT_URL', file_get_contents(base_path('.env.example')));
        $this->assertStringNotContainsString('STRIPE', file_get_contents(base_path('.env')));
        $this->assertStringNotContainsString('STRIPE', file_get_contents(base_path('.env.example')));

        $command = new TestCommand();

        /**
         * Single key.
         */
        $command->testAppendToEnv('BETTER_UPTIME_HEARTBEAT_URL=test.example');

        $this->assertStringContainsString(PHP_EOL.'BETTER_UPTIME_HEARTBEAT_URL=test.example'.PHP_EOL, file_get_contents(base_path('.env')));
        $this->assertStringContainsString(PHP_EOL.'BETTER_UPTIME_HEARTBEAT_URL=test.example'.PHP_EOL, file_get_contents(base_path('.env.example')));

        /**
         * Multiple keys.
         */
        $command->testAppendToEnv('STRIPE_KEY=pk_test_123', 'STRIPE_SECRET=sk_test_123');

        $this->assertStringContainsString(PHP_EOL.'STRIPE_KEY=pk_test_123'.PHP_EOL.'STRIPE_SECRET=sk_test_123'.PHP_EOL, file_get_contents(base_path('.env')));
        $this->assertStringContainsString(PHP_EOL.'STRIPE_KEY=pk_test_123'.PHP_EOL.'STRIPE_SECRET=sk_test_123'.PHP_EOL, file_get_contents(base_path('.env.example')));
    }
}

class TestCommand extends BaseCommand
{
    public function testAppendToEnv(...$content)
    {
        return parent::appendToEnv(...$content);
    }
}
