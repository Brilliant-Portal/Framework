<?php

namespace Tests\Feature\Api;

use App\Models\Team;
use App\Models\User;
use BrilliantPortal\Framework\Framework;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Jetstream\Features;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class V1UsersTest extends TestCase
{
    use RefreshDatabase;

    public function testNoAuth(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        $response = $this->getJson('api/v1/admin/users');
        $response->assertStatus(401);

        $response = $this->postJson('api/v1/admin/users');
        $response->assertStatus(401);

        $response = $this->patchJson('api/v1/admin/users/1');
        $response->assertStatus(401);

        $response = $this->deleteJson('api/v1/admin/users/1');
        $response->assertStatus(401);
    }

    public function testIndexFailAuthorization(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        User::factory()->times(4)->create();

        Sanctum::actingAs($team->owner, []);
        $this
            ->getJson('api/v1/admin/users')
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['read']);
        $this
            ->getJson('api/v1/admin/users')
            ->assertStatus(403);
    }

    public function testIndexAsTeamAdmin(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        // We have the team owner already, so create 4 more for a total of 5.
        User::factory()->times(4)->create();

        Sanctum::actingAs($team->owner, ['admin:read']);

        $this
            ->getJson('api/v1/admin/users')
            ->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.count', 5)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'meta' => [
                    'count',
                ],
            ]);
    }

    public function testIndexAsSuperAdmin(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        /** @var \App\Models\User $superAdmin */
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        // We have the superadmin already, so create 4 more for a total of 5.
        User::factory()->times(4)->create();

        Sanctum::actingAs($superAdmin);

        $this
            ->getJson('api/v1/admin/users')
            ->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    public function testCreateFailAuthorizationAsTeamAdmin(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        Sanctum::actingAs($team->owner, []);

        $this
            ->postJson('api/v1/admin/users')
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['update']);

        $this
            ->postJson('api/v1/admin/users')
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['delete']);

        $this
            ->postJson('api/v1/admin/users')
            ->assertStatus(403);
    }

    public function testCreateAsTeamAdmin(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        Event::fake();

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        Sanctum::actingAs($team->owner, ['admin:create']);

        $sampleUser = User::factory()->make();

        if (Framework::userHasIndividualNameFields()) {
            $testData = [
                'first_name' => $sampleUser->first_name,
                'last_name' => $sampleUser->last_name,
            ];
        } else {
            $testData = [
                'name' => $sampleUser->name,
            ];
        }
        $testData['email'] = $sampleUser->email;

        $this->assertDatabaseMissing('users', $testData);

        $response = $this->postJson('api/v1/admin/users', $testData);
        $response
            ->assertStatus(201)
            ->assertJsonPath('name', $sampleUser->name)
            ->assertJsonPath('email', $sampleUser->email);

        if (Framework::userHasIndividualNameFields()) {
            $response
                ->assertJsonPath('first_name', $sampleUser->first_name)
                ->assertJsonPath('last_name', $sampleUser->last_name);
        }

        $newUserId = $response->baseResponse->getData()->id;

        Event::assertDispatched(Registered::class);

        $this->assertDatabaseHas('users', [
            'id' => $newUserId,
            'email' => $sampleUser->email,
        ] + $testData);

        $this->assertDatabaseMissing('users', [
            'id' => $newUserId,
            'password' => $sampleUser->password,
        ]);
    }

    public function testCreateAsSuperAdmin(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        /** @var \App\Models\User $superAdmin */
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        Sanctum::actingAs($superAdmin);

        $sampleUser = User::factory()->make();

        Event::fake();

        $this
            ->postJson('api/v1/admin/users', Framework::userHasIndividualNameFields()
                ? [
                    'first_name' => $sampleUser->first_name,
                    'last_name' => $sampleUser->last_name,
                    'email' => $sampleUser->email,
                ]
                : [
                    'name' => $sampleUser->name,
                    'email' => $sampleUser->email,
                ])
            ->assertStatus(201);

        Event::assertDispatched(Registered::class);
    }

    public function testCreateFailValidation(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        $admin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        Sanctum::actingAs($admin, ['admin:create']);

        $sampleUser = User::factory()->create();

        if (Framework::userHasIndividualNameFields()) {
            $testData = [
                'first_name' => $sampleUser->first_name,
                'last_name' => $sampleUser->last_name,
            ];
        } else {
            $testData = [
                'name' => $sampleUser->name,
            ];
        }
        $testData['email'] = $sampleUser->email;

        $this->assertDatabaseHas('users', $testData);

        $response = $this->postJson('api/v1/admin/users', $testData);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email' => 'The email has already been taken',
            ]);

        $response = $this->postJson('api/v1/admin/users', collect($testData)->except('email')->all());
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email' => 'The email field is required.',
            ]);

        $response = $this->postJson('api/v1/admin/users', [
            'email' => $sampleUser->email,
        ]);

        if (Framework::userHasIndividualNameFields()) {
            $response
                ->assertStatus(422)
                ->assertJsonValidationErrors([
                    'first_name' => 'The first name field is required.',
                    'last_name' => 'The last name field is required.',
                ]);
        } else {
            $response
                ->assertStatus(422)
                ->assertJsonValidationErrors([
                    'name' => 'The name field is required.',
                ]);

        }
    }

    public function testFetchOneFailAuthorizationAsTeamAdmin(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $this->assertFalse($user->belongsToTeam($team));

        Sanctum::actingAs($team->owner, []);
        $this
            ->getJson('api/v1/admin/users/'.$user->id)
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['admin:update']);
        $this
            ->getJson('api/v1/admin/users/'.$user->id)
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['admin:delete']);
        $this
            ->getJson('api/v1/admin/users/'.$user->id)
            ->assertStatus(403);
    }

    public function testFetchOne(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $team->users()->attach($user);

        $this->assertTrue($user->belongsToTeam($team));

        Sanctum::actingAs($team->owner, ['admin:read']);

        $response = $this->getJson('api/v1/admin/users/'.$user->id);

        $response
            ->assertStatus(200)
            ->assertJsonPath('id', $user->id);
    }

    public function testFetchOneAsSuperAdmin(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        /** @var \App\Models\User $superAdmin */
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($superAdmin);

        $response = $this->getJson('api/v1/admin/users/'.$user->id);

        $response
            ->assertStatus(200)
            ->assertJsonPath('id', $user->id);
    }

    public function testUpdateFailAuthorizationAsTeamAdmin(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($team->owner, []);
        $this
            ->patchJson('api/v1/admin/users/'.$user->id, [
                'external_id' => 'ABC123',
            ])
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['admin:create']);
        $this
            ->patchJson('api/v1/admin/users/'.$user->id, [
                'external_id' => 'ABC123',
            ])
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['admin:delete']);
        $this
            ->patchJson('api/v1/admin/users/'.$user->id, [
                'external_id' => 'ABC123',
            ])
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['update']);
        $this
            ->patchJson('api/v1/admin/users/'.$user->id, [
                'external_id' => 'ABC123',
            ])
            ->assertStatus(403);
    }

    public function testUpdateAsTeamAdmin(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($team->owner, ['admin:update']);

        if (Framework::userHasIndividualNameFields()) {
            $newData = [
                'first_name' => 'John',
                'last_name' => 'Doe',
            ];
        } else {
            $newData = [
                'name' => 'John Doe',
            ];
        }
        $newData['email'] = 'john.doe@example.com';

        $response = $this->patchJson('api/v1/admin/users/'.$user->id, $newData);

        $response
            ->assertStatus(200)
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('name', 'John Doe')
            ->assertJsonPath('email', 'john.doe@example.com');

        if (Framework::userHasIndividualNameFields()) {
            $response
                ->assertJsonPath('first_name', 'John')
                ->assertJsonPath('last_name', 'Doe');
        }

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ] + $newData);
    }

    public function testUpdateAsSuperAdmin(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        /** @var \App\Models\User $superAdmin */
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($superAdmin);

        if (Framework::userHasIndividualNameFields()) {
            $newData = [
                'first_name' => 'John',
                'last_name' => 'Doe',
            ];
        } else {
            $newData = [
                'name' => 'John Doe',
            ];
        }
        $newData['email'] = 'john.doe@example.com';

        $response = $this->patchJson('api/v1/admin/users/'.$user->id, $newData);

        $response
            ->assertStatus(200)
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('name', 'John Doe')
            ->assertJsonPath('email', 'john.doe@example.com');

        if (Framework::userHasIndividualNameFields()) {
            $response
                ->assertJsonPath('first_name', 'John')
                ->assertJsonPath('last_name', 'Doe');
        }

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ] + $newData);
    }

    public function testUpdateWithSameEmail(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        /** @var \App\Models\User $superAdmin */
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($superAdmin);

        if (Framework::userHasIndividualNameFields()) {
            $newData = [
                'first_name' => 'John',
                'last_name' => 'Doe',
            ];
        } else {
            $newData = [
                'name' => 'John Doe',
            ];
        }
        $newData['email'] = $user->email;

        $response = $this->patchJson('api/v1/admin/users/'.$user->id, $newData);

        $response
            ->assertStatus(200)
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('name', 'John Doe')
            ->assertJsonPath('email', $user->email);

        if (Framework::userHasIndividualNameFields()) {
            $response
                ->assertJsonPath('first_name', 'John')
                ->assertJsonPath('last_name', 'Doe');
        }

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
        ] + $newData);
    }

    public function testDeleteFailAuthorizationAsTeamAdmin(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($team->owner, []);
        $this
            ->deleteJson('api/v1/admin/users/'.$user->id)
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['admin:create']);
        $this
            ->deleteJson('api/v1/admin/users/'.$user->id)
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['admin:update']);
        $this
            ->deleteJson('api/v1/admin/users/'.$user->id)
            ->assertStatus(403);

        Sanctum::actingAs($team->owner, ['delete']);
        $this
            ->deleteJson('api/v1/admin/users/'.$user->id)
            ->assertStatus(403);
    }

    public function testDeleteAsTeamAdmin(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        if (! Features::hasTeamFeatures()) {
            $this->markTestSkipped('Teams support is not enabled.');
        }

        /** @var \App\Models\Team $team */
        $team = Team::factory()->create();

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($team->owner, ['admin:delete']);

        $response = $this->deleteJson('api/v1/admin/users/'.$user->id);
        $response
            ->assertStatus(200)
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('name', $user->name);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function testDeleteAsSuperAdmin(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        /** @var \App\Models\User $superAdmin */
        $superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        Sanctum::actingAs($superAdmin);

        $this
            ->deleteJson('api/v1/admin/users/'.$user->id)
            ->assertStatus(200);
    }

    public function testMissing(): void
    {
        if (! Features::hasApiFeatures()) {
            $this->markTestSkipped('API support is not enabled.');
        }

        $admin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        User::factory()->times(5)->create();

        $this->assertDatabaseMissing('users', [
            'id' => 10,
        ]);

        Sanctum::actingAs($admin, [
            'admin:read',
            'admin:create',
            'admin:update',
            'admin:delete',
        ]);

        $response = $this->getJson('api/v1/admin/users/10');
        $response
            ->assertStatus(404)
            ->assertJsonPath('message', 'No query results for model [App\\Models\\User] 10');

        $response = $this->patchJson('api/v1/admin/users/10', [
            'name' => 'Testing',
        ]);
        $response
            ->assertStatus(404)
            ->assertJsonPath('message', 'No query results for model [App\\Models\\User] 10');

        $response = $this->deleteJson('api/v1/admin/users/10', [
            'name' => 'Testing',
        ]);
        $response
            ->assertStatus(404)
            ->assertJsonPath('message', 'No query results for model [App\\Models\\User] 10');
    }
}
