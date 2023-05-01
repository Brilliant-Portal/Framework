<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Inertia;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Jetstream\Features;
use Tests\TestCase;

class EnsureHasNoTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_teams_can_create_first_team()
    {
        if (! Features::hasTeamFeatures()) {
            $this->marktestSkipped('Teams support is not enabled.');
        }

        $user = User::factory()->create();

        $this->assertDatabaseMissing('teams', [
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('brilliant-portal-framework.teams.create-first'))
            ->assertOk();

        if (class_exists(Inertia::class)) {
            $response->assertInertia(fn (Assert $page) => $page->component('Teams/CreateFirst'));
        } else {
            $response->assertViewIs('brilliant-portal-framework::teams.create-first');
        }
    }

    public function test_user_without_teams_can_see_already_invited()
    {
        if (! Features::hasTeamFeatures()) {
            $this->marktestSkipped('Teams support is not enabled.');
        }

        $user = User::factory()->create();

        $this->assertDatabaseMissing('teams', [
            'user_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('brilliant-portal-framework.teams.already-invited'))
            ->assertOk();

        if (class_exists(Inertia::class)) {
            $response->assertInertia(fn (Assert $page) => $page->component('Teams/AlreadyInvited'));
        } else {
            $response->assertViewIs('brilliant-portal-framework::teams.already-invited');
        }
    }

    public function test_user_with_teams_cant_create_first_team()
    {
        if (! Features::hasTeamFeatures()) {
            $this->marktestSkipped('Teams support is not enabled.');
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

    public function test_user_with_teams_cant_see_already_invited()
    {
        if (! Features::hasTeamFeatures()) {
            $this->marktestSkipped('Teams support is not enabled.');
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
