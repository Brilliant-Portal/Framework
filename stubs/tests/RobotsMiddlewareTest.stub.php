<?php

namespace Tests\Feature;

use Tests\TestCase;

class RobotsMiddlewareTest extends TestCase
{
    public function testFlagDisablesIndexing(): void
    {
        config([
            'brilliant-portal-framework.seo.should-index' => false,
        ]);

        $this->get('/')->assertHeader('x-robots-tag', 'none');
    }

    public function testFlagAllowsIndexing(): void
    {
        config([
            'brilliant-portal-framework.seo.should-index' => true,
        ]);

        $this->get('/')->assertHeader('x-robots-tag', 'all');
    }

    public function testRoutePatternsCanBlockAccess(): void
    {
        config([
            'brilliant-portal-framework.seo.should-index' => true,
            'brilliant-portal-framework.seo.block-route-patterns' => [
                'login',
                'two-factor.*',
            ],
        ]);

        $this
            ->get('/')
            ->assertOk()
            ->assertHeader('x-robots-tag', 'all');
        $this
            ->get(route('login'))
            ->assertok()
            ->assertHeader('x-robots-tag', 'none');
    }
}
