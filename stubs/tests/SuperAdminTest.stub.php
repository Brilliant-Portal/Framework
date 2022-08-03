<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_privileges()
    {
        $abilities = Gate::abilities();
        $this->assertArrayNotHasKey('abc', $abilities);

        /** @var User $unprivilegedUser */
        $unprivilegedUser = User::factory()->create(['is_super_admin' => false]);
        $this->assertFalse($unprivilegedUser->can('abc'));

        /** @var User $superAdmin */
        $superAdmin = User::factory()->create(['is_super_admin' => true]);
        $this->assertTrue($superAdmin->can('abc'));
    }
}
