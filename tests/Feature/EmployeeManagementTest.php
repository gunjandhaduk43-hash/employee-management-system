<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $nonAdmin;
    protected Department $department;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->nonAdmin = User::factory()->create(['is_admin' => false]);
        $this->department = Department::factory()->create(['name' => 'Engineering']);
    }

    public function test_guest_is_redirected_to_login_when_accessing_employees(): void
    {
        $response = $this->get(route('employees.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_access_employees(): void
    {
        $response = $this->actingAs($this->nonAdmin)->get(route('employees.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_create_employee(): void
    {
        $data = [
            'employee_id' => 'EMP-9001',
            'name' => 'Sarah Connor',
            'email' => 'sarah.connor@example.com',
            'phone' => '+1-555-0199',
            'address' => '42 Cyber Road',
            'gender' => 'Female',
            'date_of_birth' => '1990-05-20',
            'joining_date' => '2023-01-15',
            'salary' => '85000.00',
            'designation' => 'Security Architect',
            'department_id' => $this->department->id,
        ];

        $response = $this->actingAs($this->admin)->post(route('employees.store'), $data);

        $response->assertRedirect(route('employees.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('employees', [
            'employee_id' => 'EMP-9001',
            'email' => 'sarah.connor@example.com',
        ]);
    }

    public function test_employee_id_and_email_must_be_unique(): void
    {
        Employee::factory()->create([
            'employee_id' => 'EMP-9001',
            'email' => 'duplicate@example.com',
            'department_id' => $this->department->id,
        ]);

        $response = $this->actingAs($this->admin)->post(route('employees.store'), [
            'employee_id' => 'EMP-9001',
            'name' => 'Another Person',
            'email' => 'duplicate@example.com',
            'phone' => '+1-555-0200',
            'joining_date' => '2023-02-01',
            'department_id' => $this->department->id,
        ]);

        $response->assertSessionHasErrors(['employee_id', 'email']);
    }

    public function test_admin_can_search_employees_by_name_or_id(): void
    {
        $emp1 = Employee::factory()->create([
            'employee_id' => 'EMP-1111',
            'name' => 'Michael Scott',
            'email' => 'michael@dunder.com',
            'department_id' => $this->department->id,
        ]);

        $emp2 = Employee::factory()->create([
            'employee_id' => 'EMP-2222',
            'name' => 'Dwight Schrute',
            'email' => 'dwight@dunder.com',
            'department_id' => $this->department->id,
        ]);

        // Search by name
        $response = $this->actingAs($this->admin)->get(route('employees.index', ['search' => 'Michael']));
        $response->assertOk();
        $response->assertSee('Michael Scott');
        $response->assertDontSee('Dwight Schrute');

        // Search by ID
        $response2 = $this->actingAs($this->admin)->get(route('employees.index', ['search' => 'EMP-2222']));
        $response2->assertOk();
        $response2->assertSee('Dwight Schrute');
        $response2->assertDontSee('Michael Scott');
    }

    public function test_admin_can_filter_employees_by_department(): void
    {
        $deptSales = Department::factory()->create(['name' => 'Sales Team']);

        $empEng = Employee::factory()->create([
            'name' => 'Engineer Dave',
            'department_id' => $this->department->id,
        ]);

        $empSales = Employee::factory()->create([
            'name' => 'Sales Pam',
            'department_id' => $deptSales->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('employees.index', ['department_id' => $deptSales->id]));
        $response->assertOk();
        $response->assertSee('Sales Pam');
        $response->assertDontSee('Engineer Dave');
    }

    public function test_admin_can_update_employee(): void
    {
        $emp = Employee::factory()->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->admin)->put(route('employees.update', $emp), [
            'employee_id' => $emp->employee_id,
            'name' => 'Updated Name',
            'email' => $emp->email,
            'phone' => '+1-555-9999',
            'joining_date' => '2023-05-10',
            'department_id' => $this->department->id,
        ]);

        $response->assertRedirect(route('employees.index'));
        $this->assertDatabaseHas('employees', [
            'id' => $emp->id,
            'name' => 'Updated Name',
            'phone' => '+1-555-9999',
        ]);
    }

    public function test_admin_can_delete_employee(): void
    {
        $emp = Employee::factory()->create(['department_id' => $this->department->id]);

        $response = $this->actingAs($this->admin)->delete(route('employees.destroy', $emp));

        $response->assertRedirect(route('employees.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('employees', ['id' => $emp->id]);
    }
}
