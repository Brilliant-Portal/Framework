<?php

namespace Tests\Feature;

use Symfony\Component\Console\Command\Command;
use Tests\TestCase;

class DeploymentTest extends TestCase
{
    public function testRouteCaching()
    {
        try {
            $this
                ->artisan('route:cache')
                ->assertExitCode(Command::SUCCESS);
        } finally {
            $this->artisan('route:clear');
        }
    }

    public function testConfigCaching()
    {
        try {
            $this
                ->artisan('config:cache')
                ->assertExitCode(Command::SUCCESS);
        } finally {
            $this->artisan('config:clear');
        }
    }
}
