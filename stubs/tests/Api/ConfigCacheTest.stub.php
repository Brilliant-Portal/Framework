<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class ConfigCacheTest extends TestCase
{
    public function test_can_cache_config()
    {
        try {
            $this->artisan('config:cache')->assertExitCode(0);
        } finally {
            $this->artisan('config:clear')->assertExitCode(0);
        }
    }
}
