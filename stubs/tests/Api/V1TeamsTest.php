<?php

namespace Tests\Feature\Api;

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Jetstream\Features;
use Laravel\Jetstream\Jetstream;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class V1TeamsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testNoAuth()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        $response = $this->getJson('api/v1/admin/teams');
        $response->assertStatus(401);

        $response = $this->postJson('api/v1/admin/teams');
        $response->assertStatus(401);

        $response = $this->patchJson('api/v1/admin/teams/1');
        $response->assertStatus(401);

        $response = $this->deleteJson('api/v1/admin/teams/1');
        $response->assertStatus(401);
    }

    public function testIndexFailAuthorization()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        // Note: this should actually PASS authorization.
        // See TeamPolicy::viewAny().

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        Team::factory()->times(4)->create();

        Sanctum::actingAs($team->owner, []);
        $this
            ->getJson('api/v1/admin/teams')
            ->assertStatus(200);
    }

    public function testIndexAsSuperAdmin()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\User $superAdmin */
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        Team::factory()->times(5)->create();

        Sanctum::actingAs($superAdmin);

        $this
            ->getJson('api/v1/admin/teams')
            ->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.count', 5)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'user_id',
                        'name',
                        'personal_team',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'meta' => [
                    'count',
                ],
            ]);
    }

    public function testCreateAsSuperAdmin()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\User $superAdmin */
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        Sanctum::actingAs($superAdmin);
        $sampleTeam = Team::factory()->make([
            'personal_team' => false,
        ]);

        $this->assertDatabaseMissing('teams', [
            'name' => $sampleTeam->name,
            'user_id' => $sampleTeam->user_id,
        ]);

        $response = $this->postJson('api/v1/admin/teams', [
            'name' => $sampleTeam->name,
            'user_id' => $sampleTeam->user_id,
        ]);
        $response
            ->assertStatus(201)
            ->assertJsonPath('name', $sampleTeam->name)
            ->assertJsonPath('user_id', $sampleTeam->user_id)
            ->assertJsonPath('personal_team', $sampleTeam->personal_team);

        $newUserId = $response->baseResponse->getData()->id;

        $this->assertDatabaseHas('teams', [
            'id' => $newUserId,
            'name' => $sampleTeam->name,
            'user_id' => $sampleTeam->user_id,
            'personal_team' => $sampleTeam->personal_team,
        ]);
    }

    public function testCreateFailValidationAsSuperAdmin()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\User $superAdmin */
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        Sanctum::actingAs($superAdmin);

        $sampleTeam = Team::factory()->make();

        $response = $this->postJson('api/v1/admin/teams', [
            'name' => $sampleTeam->name,
        ]);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'user_id' => 'The user id field is required.',
            ]);

        $response = $this->postJson('api/v1/admin/teams', [
            'user_id' => $sampleTeam->user_id,
        ]);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name' => 'The name field is required.',
            ]);

        $response = $this->postJson('api/v1/admin/teams', [
            'user_id' => $sampleTeam->user_id,
            'name' => $sampleTeam->name,
            'personal_team' => 'test',
        ]);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'personal_team' => 'The personal team field must be true or false.',
            ]);
    }

    public function testFetchOneFailAuthorization()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\Team $team2 */
        $team2 = Team::factory()->create();

        Sanctum::actingAs($team->owner, []);
        $this
            ->getJson('api/v1/admin/teams/'.$team2->id)
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['admin:update']);
        $this
            ->getJson('api/v1/admin/teams/'.$team2->id)
            ->assertStatus(403);
    }

    public function testFetchOne()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        Sanctum::actingAs($team->owner, ['admin:read']);

        $response = $this->getJson('api/v1/admin/teams/'.$team->id);
        $response
            ->assertStatus(200)
            ->assertJsonPath('id', $team->id)
            ->assertJsonPath('name', $team->name);
    }

    public function testFetchOneAsSuperAdmin()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\User $superAdmin */
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);
        $this->assertFalse($superAdmin->belongsToTeam($team));

        Sanctum::actingAs($superAdmin);

        $response = $this->getJson('api/v1/admin/teams/'.$team->id);
        $response
            ->assertStatus(200)
            ->assertJsonPath('id', $team->id)
            ->assertJsonPath('name', $team->name);
    }

    public function testUpdateFailAuthorization()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\Team $team2 */
        $team2 = Team::factory()->create();

        Sanctum::actingAs($team->owner, []);
        $this
            ->patchJson('api/v1/admin/teams/'.$team2->id, [
                'name' => 'ABC123',
            ])
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['admin:read']);
        $this
            ->patchJson('api/v1/admin/teams/'.$team2->id, [
                'name' => 'ABC123',
            ])
            ->assertStatus(403);

        $this->assertDatabaseHas('teams', [
            'id' => $team2->id,
            'name' => $team2->name,
        ]);
    }

    public function testUpdate()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        Sanctum::actingAs($team->owner, ['admin:update']);

        $response = $this->patchJson('api/v1/admin/teams/'.$team->id, [
            'name' => 'ABC123',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('id', $team->id)
            ->assertJsonPath('name', 'ABC123');

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'ABC123',
        ]);
    }

    public function testUpdateAsSuperAdmin()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\User $superAdmin */
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);
        $this->assertFalse($superAdmin->belongsToTeam($team));

        Sanctum::actingAs($superAdmin, []);
        $this
            ->patchJson('api/v1/admin/teams/'.$team->id, [
                'name' => 'ABC123',
            ])
            ->assertStatus(200);
    }

    public function testUpdateFailValidation()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        Sanctum::actingAs($team->owner, ['admin:update']);

        $response = $this->patchJson('api/v1/admin/teams/'.$team->id, [
            'name' => '',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'name' => 'The name field must have a value.',
            ]);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => $team->name,
        ]);
    }

    public function testUpdateTeamOwnership()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\User $newOwner */
        $newOwner = User::factory()->create();

        Sanctum::actingAs($team->owner, ['admin:update']);

        $response = $this->patchJson('api/v1/admin/teams/'.$team->id, [
            'user_id' => $newOwner->id,
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('id', $team->id)
            ->assertJsonPath('user_id', $newOwner->id);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'user_id' => $newOwner->id,
        ]);
    }

    public function testUpdateTeamOwnershipAsSuperAdmin()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\User $newOwner */
        $newOwner = User::factory()->create();

        /** @var \App\Models\User $superAdmin */
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);
        $this->assertFalse($superAdmin->belongsToTeam($team));

        Sanctum::actingAs($superAdmin);

        $this
            ->patchJson('api/v1/admin/teams/'.$team->id, [
                'user_id' => $newOwner->id,
            ])
            ->assertStatus(200);
    }

    public function testDeleteFailAuthorization()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\Team $team2 */
        $team2 = Team::factory()->create();

        Sanctum::actingAs($team->owner, []);
        $this
            ->deleteJson('api/v1/admin/teams/'.$team2->id)
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['admin:create']);
        $this
            ->deleteJson('api/v1/admin/teams/'.$team2->id)
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['admin:update']);
        $this
            ->deleteJson('api/v1/admin/teams/'.$team2->id)
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['admin:delete']);
        $this
            ->deleteJson('api/v1/admin/teams/'.$team2->id)
            ->assertStatus(403);
    }

    public function testDelete()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\Team $team2 */
        $team2 = Team::factory()->create([
            'user_id' => $team->owner->id,
        ]);

        Sanctum::actingAs($team->owner, ['admin:delete']);

        $response = $this->deleteJson('api/v1/admin/teams/'.$team2->id);
        $response
            ->assertStatus(200)
            ->assertJsonPath('id', $team2->id)
            ->assertJsonPath('name', $team2->name);

        $this->assertDatabaseMissing('teams', [
            'id' => $team2->id,
        ]);
    }

    public function testDeleteAsSuperAdmin()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\User $superAdmin */
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);
        $this->assertFalse($superAdmin->belongsToTeam($team));

        Sanctum::actingAs($superAdmin);

        $this
            ->deleteJson('api/v1/admin/teams/'.$team->id)
            ->assertStatus(200);
    }

    public function testMissing()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $teams */
        User::factory()->times(5)->create();

        $this->assertDatabaseMissing('teams', [
            'id' => 10,
        ]);

        Sanctum::actingAs($team->owner, [
            'admin:read',
            'admin:create',
            'admin:update',
            'admin:delete',
        ]);

        $response = $this->getJson('api/v1/admin/teams/10');
        $response
            ->assertStatus(404)
            ->assertJsonPath('message', 'No query results for model [App\\Models\\Team] 10');

        $response = $this->patchJson('api/v1/admin/teams/10', [
            'name' => 'Testing',
        ]);
        $response
            ->assertStatus(404)
            ->assertJsonPath('message', 'No query results for model [App\\Models\\Team] 10');

        $response = $this->deleteJson('api/v1/admin/teams/10', [
            'name' => 'Testing',
        ]);
        $response
            ->assertStatus(404)
            ->assertJsonPath('message', 'No query results for model [App\\Models\\Team] 10');
    }

    public function testCanInviteUsers()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team */
        $team = Team::factory()->create();

        $role = $this->faker->randomElement(Jetstream::$roles)->key;
        $email = $this->faker->safeEmail;

        Sanctum::actingAs($team->owner, [
            'admin:create',
            'admin:read',
            'admin:update',
            'admin:delete',
        ]);

        $response = $this->postJson('api/v1/admin/teams/'.$team->id.'/invitations', [
            'role' => $role,
            'email' => $email,
        ]);

        if (Features::sendsTeamInvitations()) {
            $expectedMessage = 'Invited '.$email.' to '.$team->name.' with role '.$role;
        } else {
            $expectedMessage = 'Added '.$email.' to '.$team->name.' with role '.$role;
        }

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'invitation_id',
                'message',
            ])
            ->assertJson([
                'message' => $expectedMessage,
            ]);

        if (Features::sendsTeamInvitations()) {
            $this->assertDatabaseHas('team_invitations', [
                'team_id' => $team->id,
                'email' => $email,
                'role' => $role,
            ]);
        } else {
            $this->assertDatabaseHas('team_user', [
                'team_id' => $team->id,
                'user_id' => User::whereEmail($email)->first()->id,
                'role' => $role,
            ]);
        }
    }

    public function testCanCancelTeamInvitation()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        if (! Features::sendsTeamInvitations()) {
            return $this->markTestSkipped('Teams invitations support is not enabled.');
        }

        /** @var \App\Models\Team */
        $team = Team::factory()->create();

        $role = $this->faker->randomElement(Jetstream::$roles)->key;
        $email = $this->faker->safeEmail;

        $teamInvitation = new TeamInvitation();
        $teamInvitation->forceFill([
            'team_id' => $team->id,
            'email' => $email,
            'role' => $role,
        ]);
        $teamInvitation->save();

        $this->assertDatabaseHas('team_invitations', [
            'id' => $teamInvitation->id,
        ]);

        Sanctum::actingAs($team->owner, [
            'admin:create',
            'admin:read',
            'admin:update',
            'admin:delete',
        ]);

        $response = $this->deleteJson('api/v1/admin/teams/'.$team->id.'/invitations/'.$teamInvitation->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Removed invitation',
            ]);

        $this->assertDatabaseMissing('team_invitations', [
            'id' => $teamInvitation->id,
        ]);
    }

    public function testCanRemoveUser()
    {
        if (! Features::hasApiFeatures()) {
            return $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            return $this->markTestSkipped('Teams support is not enabled.');
        }

        if (! Features::sendsTeamInvitations()) {
            return $this->markTestSkipped('Teams invitations support is not enabled.');
        }

        /** @var \App\Models\Team */
        $team = Team::factory()->create();

        /** @var \App\Models\User */
        $user = User::factory()->create();

        $role = $this->faker->randomElement(Jetstream::$roles)->key;

        $team->users()->attach($user, [
            'role' => $role,
        ]);

        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role' => $role,
        ]);

        Sanctum::actingAs($team->owner, [
            'admin:create',
            'admin:read',
            'admin:update',
            'admin:delete',
        ]);

        $response = $this->putJson('api/v1/admin/teams/'.$team->id.'/remove/'.$user->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Removed user',
            ]);

        $this->assertDatabaseMissing('team_user', [
                'team_id' => $team->id,
                'user_id' => $user->id,
                'role' => $role,
            ]);
    }
}
