<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class ConfigCacheTest extends TestCase
{
    public function testCanCacheConfig(): void
    {
        try {
            $this->artisan('config:cache')->assertSuccessful();
        } finally {
            $this->artisan('config:clear')->assertSuccessful();
        }
    }
}
