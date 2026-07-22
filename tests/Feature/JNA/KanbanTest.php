<?php
namespace Tests\Feature\JNA;

use App\Models\CaseDepartment;
use App\Models\Department;
use App\Models\FuneralCase;
use App\Models\Task;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Worker;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KanbanTest extends TestCase {
    use RefreshDatabase;

    protected function setUp(): void {
        parent::setUp();
        $this->seed(DepartmentSeeder::class);
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function makeSetup(): array {
        $admin = User::factory()->create(); $admin->assignRole('administrator');
        $case  = FuneralCase::create([
            'case_number'   => '2026-T01',
            'case_type'     => 'domaci',
            'deceased_name' => 'Test Pokojnik',
            'status'        => 'aktivan',
            'created_by'    => $admin->id,
        ]);
        $dept = Department::jna();
        $cd   = CaseDepartment::where('case_id', $case->id)->where('department_id', $dept->id)->first();
        return [$admin, $case, $cd, $dept];
    }

    // Test: JNA Kanban je dostupan
    public function test_jna_kanban_dashboard_loads(): void {
        [$admin] = $this->makeSetup();
        $this->actingAs($admin)->get(route('jna.dashboard'))->assertStatus(200);
    }

    // Test: Status se menja putem PATCH endpointa (drag-and-drop API)
    public function test_task_status_updates_via_patch(): void {
        [$admin, $case, $cd, $dept] = $this->makeSetup();
        $task = Task::create([
            'case_id'       => $case->id,
            'department_id' => $dept->id,
            'title'         => 'Test zadatak',
            'status'        => 'novi',
            'created_by'    => $admin->id,
        ]);
        $response = $this->actingAs($admin)
            ->patchJson(route('jna.tasks.status', $task), ['status' => 'u_toku']);
        $response->assertStatus(200)->assertJson(['success' => true, 'status' => 'u_toku']);
        $this->assertEquals('u_toku', $task->fresh()->status);
    }

    // Test: Promena na zavrsen postavlja completed_at
    public function test_completing_task_sets_completed_at(): void {
        [$admin, $case, $cd, $dept] = $this->makeSetup();
        $task = Task::create([
            'case_id' => $case->id, 'department_id' => $dept->id,
            'title' => 'Test', 'status' => 'u_toku', 'created_by' => $admin->id,
        ]);
        $this->actingAs($admin)->patchJson(route('jna.tasks.status', $task), ['status' => 'zavrsen']);
        $this->assertNotNull($task->fresh()->completed_at);
    }

    // Test: Zadatak moze biti dodeljen radniku
    public function test_task_can_be_assigned_to_worker(): void {
        [$admin, $case, $cd, $dept] = $this->makeSetup();
        $worker = Worker::create(['name' => 'Marko', 'department_id' => $dept->id, 'active' => true]);
        $task = Task::create([
            'case_id' => $case->id, 'department_id' => $dept->id,
            'title' => 'Prevoz', 'status' => 'novi', 'created_by' => $admin->id,
        ]);
        $response = $this->actingAs($admin)->patchJson(route('jna.tasks.assign', $task), [
            'assigned_worker_id' => $worker->id,
        ]);
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals($worker->id, $task->fresh()->assigned_worker_id);
    }

    // Test: Zadatak moze biti dodeljen vozilu
    public function test_task_can_be_assigned_to_vehicle(): void {
        [$admin, $case, $cd, $dept] = $this->makeSetup();
        $vehicle = Vehicle::create(['name' => 'Sprinter', 'registration_number' => 'BG-001-AA', 'type' => 'Kombi', 'active' => true]);
        $task = Task::create([
            'case_id' => $case->id, 'department_id' => $dept->id,
            'title' => 'Prevoz', 'status' => 'novi', 'created_by' => $admin->id,
        ]);
        $this->actingAs($admin)->patchJson(route('jna.tasks.assign', $task), [
            'vehicle_id' => $vehicle->id,
        ])->assertJson(['success' => true]);
        $this->assertEquals($vehicle->id, $task->fresh()->vehicle_id);
    }

    // Test: Drag-and-drop menja status i Kanban kolonu
    public function test_kanban_column_change_updates_status(): void {
        [$admin, $case, $cd, $dept] = $this->makeSetup();
        $task = Task::create([
            'case_id' => $case->id, 'department_id' => $dept->id,
            'title' => 'Test', 'status' => 'novi', 'created_by' => $admin->id,
        ]);
        foreach (['dodeljen', 'u_toku', 'zavrsen', 'novi'] as $s) {
            $this->actingAs($admin)
                ->patchJson(route('jna.tasks.status', $task), ['status' => $s])
                ->assertJson(['success' => true, 'status' => $s]);
            $this->assertEquals($s, $task->fresh()->status);
        }
    }

    // Test: JNA radnik ne moze da pristupa Pogrebnom
    public function test_jna_role_cannot_access_pogrebno(): void {
        [$admin, $case] = $this->makeSetup();
        $jnaUser = User::factory()->create(); $jnaUser->assignRole('jna');
        $this->actingAs($jnaUser)->get(route('pogrebno.show', $case))->assertStatus(403);
    }

    // Test: JNA dashboard dostupan JNA roli
    public function test_jna_role_can_access_jna_dashboard(): void {
        $this->makeSetup();
        $jnaUser = User::factory()->create(); $jnaUser->assignRole('jna');
        $this->actingAs($jnaUser)->get(route('jna.dashboard'))->assertStatus(200);
    }
}
