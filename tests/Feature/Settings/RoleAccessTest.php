<?php
namespace Tests\Feature\Settings;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class RoleAccessTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void {
        parent::setUp();
        \$this->seed(DepartmentSeeder::class);
        \$this->seed(RoleAndPermissionSeeder::class);
    }
    public function test_admin_can_access_users(): void {
        $u = User::factory()->create(); $u->assignRole('administrator');
        \$this->actingAs($u)->get(route('settings.users.index'))->assertStatus(200);
    }
    public function test_non_admin_cannot_access_users(): void {
        $u = User::factory()->create(); $u->assignRole('pogrebno');
        \$this->actingAs($u)->get(route('settings.users.index'))->assertStatus(403);
    }
    public function test_guest_redirected_to_login(): void {
        \$this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}
