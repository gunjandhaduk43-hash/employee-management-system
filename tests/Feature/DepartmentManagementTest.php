<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $nonAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->nonAdmin = User::factory()->create(['is_admin' => false]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_departments(): void
    {
        $response = $this->get(route('departments.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_departments(): void
    {
        $response = $this->actingAs($this->nonAdmin)->get(route('departments.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_departments(): void
    {
        $dept = Department::factory()->create(['name' => 'Human Resources']);

        $response = $this->actingAs($this->admin)->get(route('departments.index'));

        $response->assertOk();
        $response->assertSee('Human Resources');
    }

    public function test_admin_can_create_a_department(): void
    {
        $response = $this->actingAs($this->admin)->post(route('departments.store'), [
            'name' => 'Quality Assurance',
        ]);

        $response->assertRedirect(route('departments.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('departments', ['name' => 'Quality Assurance']);
    }

    public function test_department_name_must_be_unique(): void
    {
        Department::factory()->create(['name' => 'Finance']);

        $response = $this->actingAs($this->admin)->post(route('departments.store'), [
            'name' => 'Finance',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_a_department(): void
    {
        $dept = Department::factory()->create(['name' => 'R&D Old']);

        $response = $this->actingAs($this->admin)->put(route('departments.update', $dept), [
            'name' => 'R&D New',
        ]);

        $response->assertRedirect(route('departments.index'));
        $this->assertDatabaseHas('departments', ['id' => $dept->id, 'name' => 'R&D New']);
    }

    public function test_admin_cannot_delete_department_with_employees(): void
    {
        $dept = Department::factory()->create(['name' => 'Operations']);
        Employee::factory()->create(['department_id' => $dept->id]);

        $response = $this->actingAs($this->admin)->delete(route('departments.destroy', $dept));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('departments', ['id' => $dept->id]);
    }

    public function test_admin_can_delete_empty_department(): void
    {
        $dept = Department::factory()->create(['name' => 'Temporary']);

        $response = $this->actingAs($this->admin)->delete(route('departments.destroy', $dept));

        $response->assertRedirect(route('departments.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('departments', ['id' => $dept->id]);
    }
}
