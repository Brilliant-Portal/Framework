<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ConfigCacheTest extends TestCase
{
    public function test_can_cache_config()
    {
        $this->artisan('config:cache')->assertExitCode(0);
        $this->artisan('config:clear')->assertExitCode(0);
    }
}
