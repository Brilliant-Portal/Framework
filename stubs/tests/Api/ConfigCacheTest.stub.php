<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class ConfigCacheTest extends TestCase
{
    public function testCanCacheConfig(): void
    {
        try {
            $this->artisan('config:cache')->assertExitCode(0);
        } finally {
            $this->artisan('config:clear')->assertExitCode(0);
        }
    }
}
