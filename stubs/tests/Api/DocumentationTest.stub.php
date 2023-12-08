<?php

namespace Tests\Feature\Api;

use App\Models\Team;
use App\Models\User;
use BrilliantPortal\Framework\Framework;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Inertia;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Jetstream\Features;
use Tests\TestCase;

class DocumentationTest extends TestCase
{
    use RefreshDatabase;

    public function testLoggedOutUsersCantSeeApiDocs(): void
    {
        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        $this
            ->get('/dashboard/api-documentation')
            ->assertRedirect('/login');
    }

    public function testNonAdminUsersCantSeeApiDocs(): void
    {
        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        $team = Team::factory()->create();
        $user = User::factory()->create([
            'current_team_id' => $team->id,
        ]);

        $team->users()->attach($user, ['role' => 'editor']);

        $this
            ->actingAs($user)
            ->get('/dashboard/api-documentation')
            ->assertForbidden();
    }

    public function testAdminUsersCanSeeApiDocs(): void
    {
        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        Framework::addApiAuthMechanism();

        $user = User::factory()->withPersonalTeam()->create();

        $response = $this
            ->actingAs($user)
            ->get('/dashboard/api-documentation')
            ->assertOk();

        if (class_exists(Inertia::class)) {
            $response->assertInertia(fn (Assert $page) => $page
                ->component('API/Documentation')
                ->has('spec')
            );
        } else {
            $response->assertViewIs('brilliant-portal-framework::api.documentation');
        }
    }
}
