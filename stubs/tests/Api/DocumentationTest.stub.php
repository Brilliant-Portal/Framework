<?php

namespace Tests\Feature\Api;

use App\Models\Team;
use App\Models\User;
use BrilliantPortal\Framework\Framework;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Jetstream\Features;
use Tests\TestCase;

class DocumentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_out_users_cant_see_api_docs()
    {
        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        $this
            ->get('/dashboard/api-documentation')
            ->assertRedirect('/login');
    }

    public function test_non_admin_users_cant_see_api_docs()
    {
        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
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

    public function test_admin_users_can_see_api_docs()
    {
        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        Framework::addApiAuthMechanism();

        $user = User::factory()->withPersonalTeam()->create();

        $this
            ->actingAs($user)
            ->get('/dashboard/api-documentation')
            ->assertOk()
            ->assertViewIs('brilliant-portal-framework::api.documentation');
    }
}
