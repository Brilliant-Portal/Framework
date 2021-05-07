<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Jetstream\Features;
use Tests\TestCase;

class ApiDocumentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_out_users_cant_see_api_docs()
    {
        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        $this
            ->get('/dashboard/api-documentation')
            ->assertForbidden();
    }

    public function test_non_admin_users_cant_see_api_docs()
    {
        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        $user = User::factory()->create();

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

        $user = User::factory()->withPersonalTeam()->create();

        $this
            ->actingAs($user)
            ->get('/dashboard/api-documentation')
            ->assertOk()
            ->assertViewIs('brilliant-portal-framework::api.documentation');
    }
}
