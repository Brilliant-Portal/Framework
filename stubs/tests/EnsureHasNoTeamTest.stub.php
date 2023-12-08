<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use BrilliantPortal\Framework\Framework;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Jetstream\Features;
use Tests\TestCase;

class EnsureHasNoTeamTest extends TestCase
{
    use RefreshDatabase;

    public function testUserWithoutTeamsCanCreateFirstTeam(): void
    {
        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        $user = User::factory()->create();

        $this->assertDatabaseMissing('teams', [
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('brilliant-portal-framework.teams.create-first'))
            ->assertOk();

        if (Framework::renderWithInertia()) {
            $response->assertInertia(fn (Assert $page) => $page->component('Teams/CreateFirst'));
        } else {
            $response->assertViewIs('brilliant-portal-framework::teams.create-first');
        }
    }

    public function testUserWithoutTeamsCanSeeAlreadyInvited(): void
    {
        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        $user = User::factory()->create();

        $this->assertDatabaseMissing('teams', [
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('brilliant-portal-framework.teams.already-invited'))
            ->assertOk();

        if (Framework::renderWithInertia()) {
            $response->assertInertia(fn (Assert $page) => $page->component('Teams/AlreadyInvited'));
        } else {
            $response->assertViewIs('brilliant-portal-framework::teams.already-invited');
        }
    }

    public function testUserWithTeamsCantCreateFirstTeam(): void
    {
        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        $user = User::factory()->withPersonalTeam()->create();

        $this->assertDatabaseHas('teams', [
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $this
            ->actingAs($user)
            ->get(route('brilliant-portal-framework.teams.create-first'))
            ->assertRedirect(RouteServiceProvider::HOME);
    }

    public function testUserWithTeamsCantSeeAlreadyInvited(): void
    {
        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        $user = User::factory()->withPersonalTeam()->create();

        $this->assertDatabaseHas('teams', [
            'user_id' => $user->id,
            'personal_team' => true,
        ]);

        $this
            ->actingAs($user)
            ->get(route('brilliant-portal-framework.teams.already-invited'))
            ->assertRedirect(RouteServiceProvider::HOME);
    }
}
